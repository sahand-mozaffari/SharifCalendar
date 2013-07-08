<?php
namespace Sharif\CalendarBundle\Entity\Date;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class MonthlyDate
 * @package Sharif\CalendarBundle\Entity\Date
 * @ORM\Entity
 */
class MonthlyDate extends RecurringDate {
	/**
	 * @inheritdoc
	 */
	public function matches(SingleDate $date) {
		$that = $date->castTo($this->type);

		if($this->start != null && $that->isLessThan($this->start)) {
			return false;
		}

		if($this->end != null && $that->isGreaterThan($this->end)) {
			return false;
		}

		return $this->getBase()->getDay() == $that->getDay() &&
		(($this->getBase()->getYear() - $that->getYear()) * 12 +
		$this->getBase()->getMonth() - $that->getMonth()) %
				$this->getStep() == 0;
	}
}
