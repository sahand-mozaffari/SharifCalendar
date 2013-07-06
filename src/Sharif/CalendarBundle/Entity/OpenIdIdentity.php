<?php
namespace Sharif\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fp\OpenIdBundle\Entity\UserIdentity as BaseUserIdentity;
use Fp\OpenIdBundle\Model\UserIdentityInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Represents an identity in OpenID standard.
 * @ORM\Entity
 */
class OpenIdIdentity extends BaseUserIdentity implements \Serializable {
	/**
	 * @var integer Unique key.
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	/**
	 * @var UserInterface User instance who owns this ID.
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="openIds", fetch="EAGER")
	 * @ORM\JoinColumns({@ORM\JoinColumn(name="user_id",
	 *                                   referencedColumnName="id")})
	 */
	protected $user;

	public function __construct($identity, $user) {
		parent::__construct();
		$this->setUser($user);
		$this->setIdentity($identity);
	}

	/**
	 * Getter method for id attribute.
	 * @return integer id attribute.
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Getter method for user who owns this id.
	 * @return User User who owns this id.
	 */
	public function getUser() {
		return $this->user;
	}

	public function serialize() {
		return serialize(array($this->attributes, $this->id, $this->identity,
				$this->user));
	}

	/**
	 * Setter method for id attribute.
	 * @param integer $id New value for id attribute.
	 * @return OpenIdIdentity This.
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Setter method for the user who owns this id.
	 * @param UserInterface $user New value for $user attribute.
	 * @return OpenIdIdentity This
	 */
	public function setUser(UserInterface $user) {
		$this->user = $user;
		return $this;
	}

	public function unserialize($serialized) {
		$arr = unserialize($serialized);
		$this->attributes = $arr[0];
		$this->id = $arr[1];
		$this->identity = $arr[2];
		$this->user = $arr[3];
	}
}
