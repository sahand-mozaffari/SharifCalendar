<?php

namespace Sharif\CalendarBundle\Controller;
use Doctrine\ORM\QueryBuilder;
use Sharif\CalendarBundle\Entity\Date\AnnualDate;
use Sharif\CalendarBundle\Entity\Date\MonthlyDate;
use Sharif\CalendarBundle\Entity\Date\DailyDate;
use Sharif\CalendarBundle\Entity\Date\SingleDate;
use Sharif\CalendarBundle\FormData\EventForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Locale;

class EventsController extends Controller {
	public function addReminderAction() {
		$id = $this->getRequest()->getContent();
		$user = $this->getUser();
		$em = $this->getDoctrine()->getManager();
		$repository = $this->getDoctrine()
			->getRepository('SharifCalendarBundle:Event');
		$event = $repository->findOneById($id);
		$user->addReminder($event);
		$em->persist($user);
		$em->flush();
		return new Response();
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
				foreach($oldEvent->getLabels() as $oldLabel) {
					$oldLabel->removeEvent($oldEvent);
					$em->persist($oldLabel);
				}
				$oldEvent->setLabels(clone $newEvent->getLabels());
				foreach($newEvent->getLabels() as $newLabel) {
					$newLabel->addEvent($oldEvent);
					$em->persist($newLabel);
				}
				$em->persist($oldEvent->getDate());
				$em->persist($oldEvent);
				$em->flush();
				return $this->redirect($this->generateUrl('sharif_calendar_calendar'));
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

	public function enlistEventsAction() {
		$content = $this->getRequest()->getContent();
		$data = json_decode($content);
		$tokens = preg_split('/[\s+,]/', $data->term);
		$checkedLabels = $data->checkedLabels;
		$user = $this->getUser();

		$result = array();
		foreach($user->getAllEvents() as $event) {
			foreach($tokens as $token) {
				if(stripos($event->getTitle(), $token) === FALSE &&
					stripos($event->getDescription(), $token) === FALSE &&
					stripos($event->getOwner()->getName(), $token) === FALSE) {
					continue 2;
				}
			}

			if(count($checkedLabels) === 0) {
				$result[] = array_merge($event->jsonSerialize(),
					array('hasReminder' =>
					in_array($event, $user->getReminders()->toArray(), true)));
				continue 1;
			}

			foreach($checkedLabels as $id) {
				foreach($event->getLabels() as $label) {
					if($label->getId() === $id) {
						$result[] = array_merge($event->jsonSerialize(),
							array('hasReminder' =>
							in_array($event, $user->getReminders()->toArray(), true)));
						continue 3;
					}
				}
			}
		}

		return new JsonResponse($result);
	}

	public function getEventsAction($fromYear, $fromMonth, $fromDay, $toYear,
	                                $toMonth, $toDay) {
		$from = new SingleDate($fromYear,$fromMonth, $fromDay);
		$to = new SingleDate($toYear,$toMonth, $toDay);
		if($from->diff($to) > 370) {
			return $this->createNotFoundException('GO away you spammer!');
		}

		$events = $this->getUser()->getAllEvents();
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

	public function getLabelsAction() {
		$name = $this->getRequest()->getContent();
		$tokens = preg_split('/[-\s,_،+]+/', $name);
		$repository = $this->getDoctrine()->
			getRepository('SharifCalendarBundle:Label');
		$qb = $repository->createQueryBuilder('l');
		$qb->where('l.public = true');
		foreach($tokens as $token) {
			$qb->andWhere(
				$qb->expr()->orX(
					$qb->expr()->like('l.name',
						$qb->expr()->literal("%$token%")),
					$qb->expr()->like('l.description',
						$qb->expr()->literal("%$token%"))
				)
			);
		}
		$qb->orderBy('l.name', 'ASC');

		$q = $qb->getQuery();
		$labels = $q->getResult();
		$result = array();
		foreach($labels as $label) {
			$result[] = array('color' => $label->getColor(),
				'description' => $label->getDescription(),
				'name' => $label->getName(), 'id' => $label->getId(),
				'owner_name' => $label->getOwner()->getName(),
				'id' => $label->getId(),
				'am_subscribed' => in_array($label, $this->getUser()->getSubscribedLabels()->toArray(), true));
		}
		return new JsonResponse($result);
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
			'SharifCalendarBundle:EventManagement:newEvent.html.twig',
			array('form' => $form->createView(), 'data' => json_encode($data)));
	}

	public function removeReminderAction() {
		$id = $this->getRequest()->getContent();
		$user = $this->getUser();
		$em = $this->getDoctrine()->getManager();
		$repository = $this->getDoctrine()
			->getRepository('SharifCalendarBundle:Event');
		$event = $repository->findOneById($id);
		$user->removeReminder($event);
		$em->persist($user);
		$em->flush();
		return new Response();
	}

	public function searchEventsAction() {
		$labels = $this->getUser()->getLabels();

		$tops = array();
		foreach($labels as $label) {
			if($label->getParent() == null) {
				$tops[] = $label;
			}
		}
		$result = array();
		foreach($tops as $top) {
			$result[] = UserManagementController::encodeNode($top);
		}

		return $this->render(
			'SharifCalendarBundle:EventManagement:searchEvent.html.twig',
			array('labels' => json_encode($result)));
	}

	public function subscribeLabelAction() {
		$user = $this->getUser();
		$id = intval($this->getRequest()->getContent());
		$repository =
			$this->getDoctrine()->getRepository('SharifCalendarBundle:Label');
		$label = $repository->findOneById($id);

		if(in_array($label, $user->getSubscribedLabels()->toArray(), true)) {
			return new Response('You are already subscribed.', 403);
		} elseif(in_array($label, $user->getLabels()->toArray(), true)) {
			return new Response('You are already own the label.', 403);
		} else {
			$user->addSubscribedLabel($label);
			$em = $this->getDoctrine()->getManager();
			$em->persist($label);
			$em->persist($user);
			$em->flush();
			return new Response('You subscribed to this label successfully.');
		}
	}

	public function unsubscribeLabelAction() {
		$user = $this->getUser();
		$id = intval($this->getRequest()->getContent());
		$repository =
			$this->getDoctrine()->getRepository('SharifCalendarBundle:Label');
		$label = $repository->findOneById($id);

		if(in_array($label, $user->getSubscribedLabels()->toArray(), true)) {
			$user->removeSubscribedLabel($label);
			$em = $this->getDoctrine()->getManager();
			$em->persist($label);
			$em->persist($user);
			$em->flush();
			return new Response(
				'You unsubscribed from this label successfully.');
		} elseif(in_array($label, $user->getLabels()->toArray(), true)) {
			return new Response(
				'You own the label. If you wish to dismiss this label, use the remove option in label settings page.', 403);
		} else {
			return new Response('You are not subscribed to this label', 403);
		}
	}

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
}
