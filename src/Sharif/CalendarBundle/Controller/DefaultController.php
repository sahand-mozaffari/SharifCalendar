<?php

namespace Sharif\CalendarBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sharif\CalendarBundle\Entity\Date\SingleDate;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
	public function indexAction($name) {
		return $this->render('SharifCalendarBundle:Default:index.html.twig',
			array('name' => $name));
	}

	public function sendMailAction() {
		$repository =
			$this->getDoctrine()->getRepository('SharifCalendarBundle:User');
		$users = $repository->findAll();
		$tomorrow = (new SingleDate())->add(1);

		$template =
			"Dear __USER_NAME__,<br><br>
			This email is to remind you of the event: '__EVENT_NAME__':<br>
			<div style='border-style: solid; border-width: 1px; border-color:  darkorange; border-radius: 10px; padding:  10px; margin: 10px;'>
				__EVENT_DESCRIPTION__
			</div>";

		foreach($users as $user) {
			if($user->getEmail() != null) {
				$template_temp =
					str_replace('__USER_NAME__', $user->getName(),$template);
				foreach($user->getReminders() as $reminder) {
					if($reminder->getDate()->matches($tomorrow)) {
						$body = str_replace('__EVENT_NAME__',
							$reminder->getTitle(), $template_temp);
						$body = str_replace('__EVENT_DESCRIPTION__',
							$reminder->getDescription(), $body);
						$message = \Swift_Message::newInstance();
						$message->setTo($user->getEmail());
						$message->setBody($body, 'text/html');
						$message->setFrom('noreply@sharif-calendar.com');
						$message->setReadReceiptTo(
							'noreply@sharif-calendar.com');
						$message->setReturnPath('noreply@sharif-calendar.com');
						$message->setSender('noreply@sharif-calendar.com');
						$message->setSubject('SharifCalendar invitation');
						$this->get('mailer')->send($message);
					}
				}
			}
		}
					return new Response();
	}
}
