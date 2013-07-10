<?php
namespace Sharif\CalendarBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sharif\CalendarBundle\Entity\Date\AbstractDate;

/**
 * Class Event
 * @package Sharif\CalendarBundle\Entity
 * @ORM\Entity
 */
class Event implements \Serializable {
	/**
	 * @var AbstractDate Date.
	 * @ORM\OneToOne(
	 *      targetEntity="Sharif\CalendarBundle\Entity\Date\AbstractDate",
	 *      cascade="all", orphanRemoval=true)
	 */
	protected $date;
	/**
	 * @var string Description.
	 * @ORM\Column(type="string", length=2000)
	 */
	protected $description;
	/**
	 * @var integer Unique key.
	 * @ORM\Column(type="integer", unique=true, nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	/**
	 * @var Label[] Labels associated to this event.
	 * @ORM\ManyToMany(targetEntity="Label")
	 * @ORM\JoinTable(name="users_groups",
	 *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
	 *      )
	 * @ORM\JoinTable(name="events_labels",
	 *      joinColumns={
	 *          @ORM\JoinColumn(name="event_id", referencedColumnName="id")
	 *      }, inverseJoinColumns={
	 *          @ORM\JoinColumn(name="label_id", referencedColumnName="id")}
	 *      ))
	 */
	protected $labels;
	/**
	 * @var User owner.
	 * @ORM\ManyToOne(targetEntity="User")
	 */
	protected $owner;
	/**
	 * @var string Title
	 * @ORM\Column(type="string", length=200)
	 */
	protected $title;

	/**
	 * Constructor
	 * @param $owner User User who owns this label.
	 * @param $title string Title of this event.
	 * @param $description string Description of this event.
	 * @param $date AbstractDate Date
	 * @param array $labels Label[] Labels to be associated to this Event.
	 */
	function __construct($owner, $title, $description, $date, $labels=array()) {
		$this->labels = new ArrayCollection();
		$this->owner = $owner;
		$this->title = $title;
		$this->date = $date;
		$this->description = $description;

		foreach($labels as $label) {
			$this->addLabel($label);
		}
	}

	/**
	 * Add labels.
	 * @param Label $labels New labels to be added.
	 * @return Event $this
	 */
	public function addLabel(Label $labels) {
		$this->labels[] = $labels;
		return $this;
	}

	/**
	 * Getter method for date field.
	 * @return AbstractDate Date field.
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * Getter method for description field.
	 * @return string Description field.
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Getter method for ID field.
	 * @return int ID.
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get labels
	 * @return \Doctrine\Common\Collections\Collection Labels associated to
	 * this field.
	 */
	public function getLabels() {
		return $this->labels;
	}

	/**
	 * Getter method for owner field.
	 * @return User Owner of this event.
	 */
	public function getOwner() {
		return $this->user;
	}

	/**
	 * Getter method for title field.
	 * @return string Title field.
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Remove labels
	 * @param Label $labels Labels to be removed from this event.
	 */
	public function removeLabel(Label $labels) {
		$this->labels->removeElement($labels);
	}

	/**
	 * Setter method for date field.
	 * @param AbstractDate $date New value for date field.
	 * @return $this
	 */
	public function setDate($date) {
		$this->date = $date;
		return $this;
	}

	/**
	 * Setter method for description.
	 * @param string $description New value for description field.
	 * @return Event $this.
	 */
	public function setDescription($description) {
		$this->description = $description;

		return $this;
	}

	/**
	 * Setter method for id field.s
	 * @param int $id New value for id.
	 * @return $this.
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Setter method for owner field.
	 * @param mixed $owner
	 * @return $this
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
		return $this;
	}

	/**
	 * Setter method for title field.
	 * @param string $title New value for title field.
	 * @return $this
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @inheritdoc
	 */
	public function serialize() {
		serialize(array($this->date, $this->description, $this->id,
			$this->labels, $this->owner, $this->title));
	}

	/**
	 * @inheritdoc
	 */
	public function unserialize($serialized) {
		$arr = unserialize($serialized);
		$this->date = $arr[0];
		$this->description = $arr[1];
		$this->id = $arr[2];
		$this->labels = $arr[3];
		$this->owner = $arr[4];
		$this->title = $arr[5];
	}


}
