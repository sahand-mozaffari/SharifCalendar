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
	public function jsonSerialize() {
		return array_merge(parent::jsonSerialize(),
			array('class' => 'monthly'));
	}

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

	/**
	 * @inheritdoc
	 */
	public function toString() {
		$step = $this->step > 1 ? $this->step.' ' : '';
		return 'Every '.$step.' month from '.$this->base->toString();
	}
}
