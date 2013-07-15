<?php
namespace Sharif\CalendarBundle\Entity\Time;
use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * Class TimeRange
 * @package Sharif\CalendarBundle\Entity\Time
 * @ORM\Entity
 */
class TimeRange implements \JsonSerializable {
	/**
	 * @var \DateTime End time.
	 * @ORM\Column(type="time", nullable=true)
	 */
	protected $endTime;
	/**
	 * @var int Unique ID
	 * @ORM\Id
	 * @ORM\Column(type="integer", unique=true)
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	/**
	 * @var \DateTime Start time.
	 * @ORM\Column(type="time", nullable=true)
	 */
	protected $startTime;

	function __construct($startTime=null, $endTime=null) {
		if($startTime == null) {
			$startTime = new DateTime('today');
		}
		if($endTime == null) {
			$endTime = new DateTime('today');
		}

		$this->startTime = $startTime;
		$this->endTime = $endTime;
	}


	/**
	 * Getter method for this time range's end time.
	 * @return \DateTime This time range's end time.
	 */
	public function getEndTime() {
		return $this->endTime;
	}

	/**
	 * Getter method for this time range's ID.
	 * @return int ID.
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Getter method for this time range's start time.
	 * @return \DateTime Start time.
	 */
	public function getStartTime() {
		return $this->startTime;
	}

	/**
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		return array('start' => $this->startTime, 'end' => $this->endTime);
	}

	/**
	 * Setter method for this time range's end time.
	 * @param \DateTime $endTime New value for end time.
	 * @return $this
	 */
	public function setEndTime(\DateTime $endTime) {
		$this->endTime = $endTime;
		return $this;
	}

	/**
	 * Setter method for this time range's id.
	 * @param $id New value for id.
	 * @return $this
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Setter method for this time range's start time.
	 * @param \DateTime $startTime New value for start time.
	 * @return $this
	 */
	public function setStartTime(\DateTime $startTime) {
		$this->startTime = $startTime;
		return $this;
	}
}
