<?php

namespace Sharif\CalendarBundle\Controller;
use Sharif\CalendarBundle\Entity\Date\AnnualDate;
use Sharif\CalendarBundle\Entity\Date\MonthlyDate;
use Sharif\CalendarBundle\Entity\Date\DailyDate;
use Sharif\CalendarBundle\Entity\Date\SingleDate;
use Sharif\CalendarBundle\FormData\EventForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventsController extends Controller {
	private function copyDate($date) {
		switch(substr(get_class($date), strripos(get_class($date), '\\') + 1)) {
			case 'SingleDate' :
			return new SingleDate($date->getYear(), $date->getMonth(),
				$date->getDay(), $date->getType());
			case 'AnnualDate' :
				return new AnnualDate($this->copyDate($date->getBase()),
					$this->copyDate($date->getStart()),
					$this->copyDate($date->getEnd()),
					$date->getStep());
			case 'MonthlyDate' :
				return new MonthlyDate($this->copyDate($date->getBase()),
					$this->copyDate($date->getStart()),
					$this->copyDate($date->getEnd()),
					$date->getStep());
			case 'DailyDate' :
				return new DailyDate($this->copyDate($date->getBase()),
					$this->copyDate($date->getStart()),
					$this->copyDate($date->getEnd()),
					$date->getStep());
			default:
				return null;
		}
	}

	public function calendarAction() {

	}

	public function editEventAction($id) {
		$user = $this->getUser();
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getManager();
		$oldEvent = $this->getDoctrine()->
			getRepository('SharifCalendarBundle:Event')->findOneById($id);
		if($oldEvent == null ||
				$oldEvent->getOwner()->getId() != $user->getId()) {
			return $this->createNotFoundException();
		}
		$form = $this->createForm(new EventForm(), clone $oldEvent);

		if($this->getRequest()->isMethod('post')) {
			$form->bind($request);
			if($form->isValid()) {
				$newEvent = $form->getData();
				$oldEvent->setDate($this->copyDate($newEvent->getDate()));
				$oldEvent->setTitle($newEvent->getTitle());
				$oldEvent->setDescription($newEvent->getDescription());
				$oldEvent->setLabels($newEvent->getLabels());
				$em->persist($oldEvent->getDate());
				$em->persist($oldEvent);
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

	public function newAction() {
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

	public function getLabelsAction($fromYear, $fromMonth, $fromDay, $toYear,
	                                $toMonth, $toDay) {
		$from = new SingleDate($fromYear,$fromMonth, $fromDay);
		$to = new SingleDate($toYear,$toMonth, $toDay);
		if($from->diff($to) > 370) {
			return $this->createNotFoundException('GO away you spammer!');
		}

		$user = $this->getUser();
		$events = $user->getEvents();

		$result = array();
		while(!$from->isGreaterThan($to)) {
			foreach($events as $event) {
				$date = $event->getDate();
				if($date->matches($from)) {
					$result[] = array('date' => $from, 'event' => $event);
				}
			}
			$from = $from->add(1);
		}

		return new JsonResponse($result);
	}
}
