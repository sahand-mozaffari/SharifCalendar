<?php
namespace Sharif\CalendarBundle\Controller;
use Doctrine\ORM\QueryBuilder;
use Sharif\CalendarBundle\FormData\EventForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sharif\CalendarBundle\Entity\Label;
use Sharif\CalendarBundle\Entity\User;
use Sharif\CalendarBundle\FormData\SignupDataOpenId;
use Sharif\CalendarBundle\FormData\SignupDataUserPass;
use Symfony\Component\Security\Core\SecurityContext;

class UserManagementController extends Controller {
	/**
	 * Decodes the data received from a label setting form, into an array of
	 *  labels.
	 * @param $nodes array JSON-decoded data from the form.
	 * @param null $parent Label For internal use.
	 * @return array Array of labels decoded from form data.
	 */
	public static function decodeNodes($nodes, $parent=null) {
		$result = array();
		foreach($nodes as $node) {
			$newLabel = (new Label(null, $node['name'],
				intval('0X'.substr($node['color'], 1), 16), $parent,
				$node['description'], $node['publicity']))->setId($node['id']);
			if($parent != null) {
				$parent->addChild($newLabel);
			}

			$result[] = $newLabel;
			if(isset($node['items'])) {
				$result = array_merge($result,
					self::decodeNodes($node['items'], $newLabel));
			}
		}
		return $result;
	}

	/**
	 * @param $node Array containing labels owned by current user.
	 * @return array Array containing labels owned by current user, linked as
	 *      in a tree, and consumable by a KendoUI TreeView.
	 */
	public static function encodeNode($node) {
		$result = array('id' => $node->getId(), 'text' => $node->getName(),
			'color' => sprintf("#%6X", $node->getColor()), 'image' => "",
			'description' => $node->getDescription(),
			'publicity' => $node->isPublic());
		if(!$node->getChildren()->isEmpty()) {
			$children = array();
			foreach($node->getChildren() as $child) {
				$children[] = self::encodeNode($child);
			}
			$result['items'] = $children;
		}
		return $result;
	}

	public function indexAction() {
		$user = $this->getUser();
		$em = $this->getDoctrine()->getManager();
		$request = $this->getRequest();
		$form = $this->createForm(new EventForm());

		if($this->getRequest()->isMethod('post')) {
			$form->bind($request);
			if($form->isValid()) {
				$event = $form->getData();
				$event->setOwner($user);
				$user->addEvent($event);
				foreach($event->getLabels() as $label) {
					$label->addEvent($event);
					$em->persist($label);
				}
				$em->persist($event);
				$em->flush();
				return $this->redirect('sharif_calendar_calendar');
			}
		}

		$labels = $user->getLabels();
		$data = array();
		foreach($labels as $label) {
			$data[] = array('name' => $label->getName(),
				'fullName' => $label->getFullName(),
				'color' => sprintf("#%06X", $label->getColor()),
				'id' => $label->getId());
		}

		return $this ->render(
			'SharifCalendarBundle::index.html.twig',
			array('form' => $form->createView(), 'data' => json_encode($data)));
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
			$result[] = self::encodeNode($top);
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
		$repository = $this->getDoctrine()->
			getRepository('Sharif\CalendarBundle\Entity\Label');
		$oldLabels = $this->getUser()->getLabels();
		$this->getUser()->clearLabels();

		$content = $this->getRequest()->getContent();
		if(!empty($content)) {
			$content = json_decode($content, true);
		}
		$labels = self::decodeNodes($content);
		$index = array();
		$dbIndex = array();

		foreach($labels as $label) {
			$index[$label->getId()] = $label;
				$dbIndex[$label->getId()] =
					$repository->findOneById($label->getId());
			if($dbIndex[$label->getId()] == null) {
				$dbIndex[$label->getId()] = $label;
				$label->setOwner($this->getUser());
			} else {
				$dbIndex[$label->getId()]->setName($label->getName());
				$dbIndex[$label->getId()]->setColor($label->getColor());
				$dbIndex[$label->getId()]->
					setDescription($label->getDescription());
				$dbIndex[$label->getId()]->setPublic($label->isPublic());
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
		foreach(array_keys($index) as $id) {
			$em->persist($dbIndex[$id]);
		}
		foreach($oldLabels as $oldLabel) {
			if(!array_key_exists($oldLabel->getId(), $index)) {
				foreach($oldLabel->getEvents() as $event) {
					$event->removeLabel($oldLabel);
					$em->persist($event);
				}
				$em->remove($oldLabel);
			}
		}

		$em->persist($this->getUser());
		$em->flush();

		return new Response();
	}
}
