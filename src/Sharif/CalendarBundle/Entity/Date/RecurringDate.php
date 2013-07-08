<?php
namespace Sharif\CalendarBundle\Entity\Date;
use doctrine\Orm\Mapping as ORM;

/**
 * Class RecurringDate
 * @package Sharif\CalendarBundle\Entity\Date
 * Base class for recurring dates.
 */
abstract class RecurringDate extends AbstractDate {
	/**
	 * @var SingleDate Base date.
	 * @ORM\OneToOne(targetEntity="SingleDate", orphanRemoval=true)
	 */
	protected $base;
	/**
	 * @var SingleDate ending date of this recurrence.
	 * @ORM\OneToOne(targetEntity="SingleDate", orphanRemoval=true)
	 */
	protected $end;
	/**
	 * @var SingleDate Starting date of this recurrence.
	 * @ORM\OneToOne(targetEntity="SingleDate", orphanRemoval=true)
	 */
	protected $start;
	/**
	 * @var integer Every ?th period.
	 * @ORM\Column(type="integer", nullable=false)
	 */
	protected $step;

	public function __construct($base=null, $start=null, $end=null, $step = 1) {
		if($base == null) {
			$base = new SingleDate();
		}

		$this->type = $base->type;
		$this->base = $base;
		$this->end = $end;
		$this->start = $start;
		$this->step = $step;
	}

	/**
	 * Getter method for base field.
	 * @return SingleDate Base field.
	 */
	public function getBase() {
		return $this->base;
	}

	/**
	 * Getter method for end field.
	 * @return SingleDate End date.
	 */
	public function getEnd() {
		return $this->end;
	}

	/**
	 * Getter method for start field.
	 * @return SingleDate Start date.
	 */
	public function getStart() {
		return $this->start;
	}

	/**
	 * Getter method for step field.
	 * @return integer Step field.
	 */
	public function getStep() {
		return $this->step;
	}

	public function setBase(SingleDate $base) {
		$this->base = $base->castTo($this->type);
	}

	/**
	 * Setter method for end field.
	 * @param SingleDate $end New value for end field.
	 * @return $this.
	 */
	public function setEnd(SingleDate $end) {
		$this->end = $end;
		return $this;
	}

	/**
	 * Setter method for start field.
	 * @param SingleDate $start New value for start field.
	 * @return $this
	 */
	public function setStart(SingleDate $start) {
		$this->start = $start;
		return $this;
	}

	/**
	 * Setter method for step field.
	 * @param $step integer New value for step field.
	 * @return $this
	 */
	public function setStep($step) {
		$this->step = $step;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setType($type) {
		parent::setType($type);
		$this->base = $this->base->castTo($type);
		return $this;
	}
}
