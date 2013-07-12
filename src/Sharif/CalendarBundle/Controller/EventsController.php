<?php

namespace Sharif\CalendarBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EventsController extends Controller {
	public function calendarAction() {

	}

	public function newAction() {
		$request = $this->getRequest();
		$form = $this->createForm(new EventForm());

		if($this->getRequest()->isMethod('post')) {
			$form->bind($request);
			if($form->isValid()) {
				$user = $this->get('security.context')->getToken()->getUser();
				$em = $this->getDoctrine()->getManager();

				$event = $form->getData();
				$event->setOwner($user);
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
			'SharifCalendarBundle:EventManagement:newEvent.html.twig',
			array('form' => $form->createView(), 'data' => json_encode($data)));
	}
}
