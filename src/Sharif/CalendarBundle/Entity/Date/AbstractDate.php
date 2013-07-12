<?php
namespace Sharif\CalendarBundle\Entity\Date;
use Sharif\CalendarBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a single or recurring date.
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"single"="SingleDate", "annual"="AnnualDate",
 *      "daily"="DailyDate", "monthly"="MonthlyDate"})
 */
abstract class AbstractDate {
	/**
	 * @var integer Unique key.
	 * @ORM\Column(type="integer", nullable=false, unique=true)
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Id
	 */
	protected $id;
	/**
	 * @var string Type of the date. Takes values Jalali, Georgian and
	 * Lunar-Hijri.
	 * @ORM\Column(type="string", length=11, nullable=false)
	 * @Assert\Choice(choices={"Jalali", "Gregorian", "Lunar-Hijri"},
	 *      message="selected_value_not_valid", strict=true)
	 */
	protected $type;

	/**
	 * Getter method for id field.
	 * @return integer Id.
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Getter method for type field.
	 * @return string Type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Setter method for id field.
	 * @param integer $id New value for id field.
	 * @return AbstractDate This.
	 */
	public function setId($id) {
		$this->id = $id;
		return $id;
	}

	/**
	 * Setter method for type field.
	 * @param $type string New value for type field.
	 * @return AbstractDate This.
	 */
	public function setType($type) {
		$this->type = $type;
		return $this;
	}

	/**
	 * Determines whether or not a given single date matches this date.
	 * @param SingleDate $date Single date to be matched.
	 * @return boolean Whether or not a given single date matches this date.
	 */
	public abstract function matches(SingleDate $date);
}
