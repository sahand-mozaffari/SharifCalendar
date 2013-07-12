<?php
namespace Sharif\CalendarBundle\Controller;
use Sharif\CalendarBundle\Entity\Label;
use Sharif\CalendarBundle\Entity\User;
use Sharif\CalendarBundle\FormData\SignupDataOpenId;
use Sharif\CalendarBundle\FormData\SignupDataUserPass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\Exception\RuntimeException;
use Symfony\Component\Security\Core\SecurityContext;

class UserManagementController extends Controller {
	/**
	 * Decodes the data received from a label setting form, into an array of
	 *  labels.
	 * @param $nodes array JSON-decoded data from the form.
	 * @param null $parent Label For internal use.
	 * @return array Array of labels decoded from form data.
	 */
	private function decodeNodes($nodes, $parent=null) {
		$result = array();
		foreach($nodes as $node) {
			$newLabel = (new Label(null, $node['name'],
				intval('0X'.substr($node['color'], 1), 16), $parent))->
				setId($node['id']);
			if($parent != null) {
				$parent->addChild($newLabel);
			}

			$result[] = $newLabel;
			if(isset($node['items'])) {
				$result = array_merge($result,
					$this->decodeNodes($node['items'], $newLabel));
			}
		}
		return $result;
	}

	/**
	 * @param $node Array containing labels owned by current user.
	 * @return array Array containing labels owned by current user, linked as
	 *      in a tree, and consumable by a KendoUI TreeView.
	 */
	private function encodeNode($node) {
		$result = array('id' => $node->getId(), 'text' => $node->getName(),
			'color' => sprintf("#%6X", $node->getColor()), 'image' => "");
		if(!$node->getChildren()->isEmpty()) {
			$children = array();
			foreach($node->getChildren() as $child) {
				$children[] = $this->encodeNode($child);
			}
			$result['items'] = $children;
		}
		return $result;
	}

	public function loginAction() {
		$request = $this->container->get('request');
		$session = $request->getSession();

		// get the error if any
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $request->attributes
			        ->get(SecurityContext::AUTHENTICATION_ERROR);
		} elseif (null !== $session
		        && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			$session->remove(SecurityContext::AUTHENTICATION_ERROR);
		} else {
			$error = '';
		}
		if ($error) {
			$error = $error->getMessage();
		}

		$this->get('session')->set('create_open_id_action', 'throw_exception');
		return $this
		        ->render(
		                'SharifCalendarBundle:UserManagement:login.html.twig',
		                array('error' => $error));
	}

	private function registerUserPass($data) {
		$em = $this->getDoctrine()->getManager();

		// Check for redundancy
		$repository = $this->getDoctrine()->
			getRepository('SharifCalendarBundle:User');
		if(null != $repository->findOneByUsername($data->getUserName())) {
			$this->getRequest()->getSession()->getFlashBag()->
				add('error', 'username_already_exists');
			return $this->redirect(
				$this->generateUrl('sharif_calendar_signup'));
		}

		// New user
		$user = new User($data->getFullName(), $data->getEmail(),
			$data->getUserName(), $data->getPassword());

		$em->persist($user);
		$em->flush();
		return $this->redirect(
			$this->generateUrl('sharif_calendar_signup_successful'));
	}

	public function settingLabelsAction() {
		$labels =
			$this->get('security.context')->getToken()->getUser()->getLabels();

		 $tops = array();
		foreach($labels as $label) {
			if($label->getParent() == null) {
				$tops[] = $label;
			}
		}
		$result = array();
		foreach($tops as $top) {
			$result[] = $this->encodeNode($top);
		}

		return $this->render(
			'SharifCalendarBundle:UserManagement:labelSettings.html.twig',
			array('data' => json_encode($result)));
	}

	public function signupAction() {
		$request = $this->getRequest();
		// Build form
		$formOpenId = $this->createForm(new SignupDataOpenId());
		$formUserPass = $this->createForm(new SignupDataUserPass());

		// Fill the form with previously entered data.
		if($request->isMethod('POST')) {
			// Bind data
			$formUserPass->bind($request);
			// Check if the data is valid.
			if($formUserPass->isValid()) {
				return $this->registerUserPass($formUserPass->getData());
			}
		}

		$this->get('session')->set('create_open_id_action', 'create_new');
		// Output form.
		return $this->render(
                'SharifCalendarBundle:UserManagement:signup.html.twig',
                array('formOpenId' => $formOpenId->createView(),
                        'formUserPass' => $formUserPass->createView()));
	}

	public function submitLabelsAction() {
		$em = $this->getDoctrine()->getManager();
		$repository = $this->getDoctrine()->getRepository('Sharif\CalendarBundle\Entity\Label');
		$oldLabels = $this->getUser()->getLabels();
		$this->getUser()->clearLabels();

		$content = $this->getRequest()->getContent();
		if(!empty($content)) {
			$content = json_decode($content, true);
		}
		$labels = $this->decodeNodes($content);
		$index = array();
		$dbIndex = array();

		foreach($labels as $label) {
			$index[$label->getId()] = $label;
				$dbIndex[$label->getId()] = $repository->findOneById($label->getId());
			if($dbIndex[$label->getId()] == null) {
				$dbIndex[$label->getId()] = $label;
				$label->setOwner($this->getUser());
			} else {
				$dbIndex[$label->getId()]->setName($label->getName());
				$dbIndex[$label->getId()]->setColor($label->getColor());
			}
			$dbIndex[$label->getId()]->clearChildren();
			$index[$label->getId()]->clearChildren();
		}
		foreach(array_keys($index) as $id) {
			$parent = $index[$id]->getParent();
			if($parent != null) {
				$parentId = $parent->getId();
				$dbIndex[$id]->setParent($dbIndex[$parentId]);
				$dbIndex[$parentId]->addChild($dbIndex[$id]);
			} else {
				$dbIndex[$id]->setParent(null);
			}
			$this->getUser()->addLabel($dbIndex[$id]);
		}
		foreach($oldLabels as $oldLabel) {
			if(!array_key_exists($oldLabel->getId(), array_keys($index))) {
				$em->remove($oldLabel);
			}
		}

		$em->persist($this->getUser());
		$em->flush();

		return new Response();
	}
}
