<?php
namespace Sharif\CalendarBundle\Entity\Date;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class DailyDate
 * @package Sharif\CalendarBundle\Entity\Date
 * @ORM\Entity
 */
class DailyDate extends RecurringDate {
	/**
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		return array_merge(parent::jsonSerialize(), array('class' => 'daily'));
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

		return intval(
			$this->getBase()->toDate()->diff($that->toDate())->format("%d")) %
			$this->getStep() == 0;
	}
}
