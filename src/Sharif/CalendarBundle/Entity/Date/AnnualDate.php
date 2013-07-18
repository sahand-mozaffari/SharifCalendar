<?php
namespace Sharif\CalendarBundle\Entity\Date;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AnnuallyDate
 * @package Sharif\CalendarBundle\Entity\Date
 * @ORM\Entity
 */
class AnnualDate extends RecurringDate {
	/**
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		return array_merge(parent::jsonSerialize(), array('class' => 'annual'));
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
			$this->getBase()->getMonth() == $that->getMonth() &&
			($this->getBase()->getYear() - $that->getYear()) %
				$this->getStep() == 0;
	}

	/**
	 * @inheritdoc
	 */
	public function toString() {
		$step = $this->step > 1 ? $this->step.' ' : '';
		return 'هر '.$step.' سال، از '.$this->base->toString();
	}
}
