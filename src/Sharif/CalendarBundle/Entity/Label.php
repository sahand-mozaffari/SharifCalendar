<?php
namespace Sharif\CalendarBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sharif\CalendarBundle\Twig\TreeInterface;

/**
 * Class Label
 * @package Sharif\CalendarBundle\Entity
 * @ORM\Entity
 */
class Label implements \Serializable, \JsonSerializable {
	/**
	 * @var Label[] Children of this label.
	 * @ORM\OneToMany(targetEntity="Label", mappedBy="parent", cascade="all")
	 */
	protected $children;
	/**
	 * @var integer Color code.
	 * @ORM\Column(type="integer")
	 */
	protected $color;
	/**
	 * @var string Description of this label.
	 * @ORM\Column(type="string", length=200)
	 */
	protected $description;
	/**
	 * @var Event[] events having this label.
	 * @ORM\ManyToMany(targetEntity="Event", inversedBy="events")
	 * @ORM\JoinTable(name="labels_events")
	 */
	protected $events;
	/**
	 * @var int ID.
	 * @ORM\Column(type="integer", nullable=false, unique=true)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	/**
	 * @var User Owner of this label.
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="labels")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $owner;
	/**
	 * @var string Name.
	 * @ORM\Column(type="string", length=100)
	 */
	protected $name;
	/**
	 * @var Label Parent label.
	 * @ORM\ManyToOne(targetEntity="Label", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	protected $parent;
	/**
	 * @var boolean Publicity of this label.
	 * @ORM\Column(type="boolean")
	 */
	protected $public;

	/**
	 * Constructor
	 * @param $owner User who owns this label.
	 * @param $name string Name.
	 * @param int $color Color.
	 * @param $parent Label Parent.
	 * @param $description string Description
	 * @param $publicity boolean Publicity
	 */
	public function __construct($owner, $name, $color=0xCCCCCC, $parent=null,
	                            $description="", $publicity=false) {
		$this->children = new ArrayCollection();
		$this->events = new ArrayCollection();
		$this->subscribers = new ArrayCollection();
		$this->color = $color;
		$this->name = $name;
		$this->owner = $owner;
		$this->parent = $parent;
		$this->description =$description;
		$this->setPublic($publicity);
	}

	/**
	 * Add children
	 * @param Label $children Children to be added.
	 * @return Label $this.
	 */
	public function addChild(Label $children) {
		$this->children[] = $children;
		return $this;
	}

	/**
	 * Add event
	 * @param Event $event event to be added.
	 * @return Label $this.
	 */
	public function addEvent(Event $event) {
		$this->events[] = $event;
		return $this;
	}

	/**
	 * Removes all the children.
	 */
	public function clearChildren() {
		$this->children->clear();
	}

	/**
	 * Get children
	 * @return \Doctrine\Common\Collections\Collection Children
	 */
	public function getChildren() {
		return $this->children;
	}

	/**
	 * Getter method for color field.
	 * @return int Color field.
	 */
	public function getColor() {
		return $this->color;
	}

	/**
	 * Getter method for this label's description.
	 * @return string Description of this label.
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Getter method for this label's events.
	 * @return Event[] events.
	 */
	public function getEvents() {
		return $this->events;
	}

	/**
	 * Computes the full name of this label. e.g. grandpa/daddy/me
	 * @return string Full name of this label.
	 */
	public function getFullName() {
		if($this->parent == null) {
			return $this->name;
		} else {
			return $this->parent->getFullName().' / '.$this->name;
		}
	}

	/**
	 * Getter method for ID field.
	 * @return int Unique ID
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get owner
	 * @return User User who owns this label.
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * Getter method for name field.
	 * @return string name field.
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Getter method for parent field.
	 * @return Label parent field.
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * Getter method for this label's publicity
	 * @return bool Whether or not this method is public.
	 */
	public function isPublic() {
		return $this->public;
	}

	/**
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		return array('color' => $this->color, 'name' => $this->name,
			'id' => $this->id, 'description' => $this->description);
	}

	/**
	 * Remove children
	 * @param Label $children Children to be removed.
	 */
	public function removeChild(Label $children) {
		$this->children->removeElement($children);
	}

	/**
	 * Remove event
	 * @param Event $event Event to be removed.
	 */
	public function removeEvent(Event $event) {
		$this->events->removeElement($event);
	}

	/**
	 * @inheritdoc
	 */
	public function serialize() {
		return urlencode(serialize(array($this->children, $this->color, $this->id,
			$this->name, $this->owner, $this->parent, $this->public,
			$this->description, $this->events)));
	}

	/**
	 * Setter method for color field.
	 * @param int $color New value for color field.
	 * @return $this
	 */
	public function setColor($color) {
		$this->color = $color;
		return $this;
	}

	/**
	 * Setter method for this label's description.
	 * @param $description string New value for this label's description.
	 * @return $this
	 */
	public function setDescription($description)  {
		$this->description = $description;
		return $this;
	}

	/**
	 * Setter method for id field.
	 * @param $id int Unique ID.
	 * @return $this
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Setter method for name field.
	 * @param string $name New value for name field.
	 * @return $this
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * Set owner
	 * @param \Sharif\CalendarBundle\Entity\User $owner User who owns this
	 *  label.
	 * @return Label $this.
	 */
	public function setOwner(User $owner = null) {
		$this->owner = $owner;
		return $this;
	}

	/**
	 * Setter method for parent field.
	 * @param Label $parent parent label.
	 * @return $this
	 */
	public function setParent($parent) {
		$this->parent = $parent;
		return $this;
	}

	/**
	 * Setter method for this label's publicity.
	 * @param $public boolean New value for this field's publicity.
	 * @return $this
	 */
	public function setPublic($public) {
		$this->public = $public;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function unserialize($serialized) {
		$arr = unserialize(urldecode($serialized));
		$this->children = $arr[0];
		$this->color = $arr[1];
		$this->id = $arr[2];
		$this->name = $arr[3];
		$this->owner = $arr[4];
		$this->parent = $arr[5];
		$this->public = $arr[6];
		$this->description = $arr[7];
		$this->events = $arr[8];
	}
}
