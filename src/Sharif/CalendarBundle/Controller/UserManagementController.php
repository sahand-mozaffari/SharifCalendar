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

	public function signupSuccessfulAction() {
		return $this->render(
		        'SharifCalendarBundle:UserManagement:signupSuccessful.html.twig');
	}
}
