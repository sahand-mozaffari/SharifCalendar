<?php
namespace Sharif\CalendarBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Label
 * @package Sharif\CalendarBundle\Entity
 * @ORM\Entity
 */
class Label {
	/**
	 * @var Label[] Children of this label.
	 * @ORM\OneToMany(targetEntity="Label", mappedBy="parent")
	 */
	protected $children;
	/**
	 * @var integer Color code.
	 * @ORM\Column(type="integer")
	 */
	protected $color;
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
	 * @ORM\ManyToOne(targetEntity="Label")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	protected $parent;

	/**
	 * Constructor
	 * @param $owner User who owns this label.
	 * @param $name Name.
	 * @param int $color Color.
	 * @param $parent Parent.
	 */
	public function __construct($owner, $name, $color=0xCCCCCC, $parent) {
		$this->children = new ArrayCollection();
		$this->color = $color;
		$this->name = $name;
		$this->owner = $owner;
		$this->parent = $parent;
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
	 * Remove children
	 * @param Label $children Children to be removed.
	 */
	public function removeChild(\Sharif\CalendarBundle\Entity\Label $children) {
		$this->children->removeElement($children);
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
	 * Setter method for name field.
	 * @param string $name New value for name field.
	 * @return $this
	 */
	public function setName($name) {
		$this->name = $name;
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
}
