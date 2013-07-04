<?php
namespace Sharif\CalendarBundle\Controller;
use Sharif\CalendarBundle\Entity\User;
use Sharif\CalendarBundle\FormData\SignupDataOpenId;
use Sharif\CalendarBundle\FormData\SignupDataUserPass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\SecurityContext;

class UserManagementController extends Controller {
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

		return $this
		        ->render(
		                'SharifCalendarBundle:UserManagement:login.html.twig',
		                array('error' => $error));
	}

	private function registerOpenId($data) {
		$em = $this->getDoctrine()->getManager();

		// Check for redundancy
		$repository = $this->getDoctrine()->
		        getRepository('SharifCalendarBundle:OpenIdIdentity');
		if(null != $repository->findOneByIdentity($data->getOpenId())) {
			$this->getRequest()->getSession()->getFlashBag()->
			        add('error', 'openid_already_exists');
			return $this->redirect(
			        $this->generateUrl('sharif_calendar_signup'));
		}

		// New user and open ID
		$user = new User($data->getFirstName(), $data->getLastName(), null,
		        null, $data->getOpenId());

		// Persisting to DB
		$em->persist($user);
		$em->persist($user->getOpenIds()[0]);
		$em->flush();
		return $this->redirect($this->generateUrl(
				'sharif_calendar_signup_successful'));
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
		$user = new User($data->getFirstName(), $data->getLastName(),
		        $data->getUserName(), $data->getPassword(), null);

		$em->persist($user);
		$em->flush();
		return $this->redirect(
		        $this->generateUrl('sharif_calendar_signup_successful'));
	}

	public function signupAction() {
		$request = $this->getRequest();
		// Build form
		$formOpenId = $this->createForm(new SignupDataOpenId());
		$formUserPass = $this->createForm(new SignupDataUserPass());

		// Fill the form with previously entered data.
		if($request->isMethod('POST')) {
			if($request->get('form') == 'openId') {
				// Bind data
				$formOpenId->bind($request);
				// Check if the data is valid.
				if($formOpenId->isValid()) {
					return $this->registerOpenId($formOpenId->getData());
				}
			} else if($request->get('form') == 'userPass') {
				// Bind data
				$formUserPass->bind($request);
				// Check if the data is valid.
				if($formUserPass->isValid()) {
					return $this->registerUserPass($formUserPass->getData());
				}
			}
		}

		// Output form.
		return $this->render(
                'SharifCalendarBundle:UserManagement:signup.html.twig',
                array('formOpenId' => $formOpenId->createView(),
                        'formUserPass' => $formUserPass->createView()));
	}

	public function signupSuccessfulAction() {
		return $this->render(
		        'SharifCalendarBundle:UserManagement:signupSuccessful.html.twig');
	}
}
