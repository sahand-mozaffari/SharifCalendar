<?php
namespace Sharif\CalendarBundle\Entity\Date;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AnnuallyDate
 * @package Sharif\CalendarBundle\Entity\Date
 * @ORM\Entity
 */
class AnnuallyDate extends RecurringDate {
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
}
