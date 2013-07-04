<?php
namespace Sharif\CalendarBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * Represents a user.
 * @ORM\Entity
 */
class User implements UserInterface, \Serializable {
	/**
	 * @var string This User's first name.
	 * @ORM\Column(type="string",length=50, nullable=false)
	 */
	protected $firstName;
	/**
	 * @var integer Unique key
	 * @ORM\Column(type="integer", nullable=false, unique=true)
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Id
	 */
	protected $id;
	/**
	 * @var string This User's last name.
	 * @ORM\Column(type="string", length=50, nullable=false)
	 */
	protected $lastName;
	/**
	 * @var OpenIdIdentity This User's open IDs.
	 * @ORM\OneToMany(targetEntity="OpenIdIdentity", mappedBy="user",
	 * cascade={"all"})
	 */
	protected $openIds;
	/**
	 * @var string Hash value of this user's password.
	 * @ORM\Column(type="string", length=64, nullable=true)
	 */
	protected $password;
	/**
	 * @var string The string used to salt the password before hashing.
	 * @ORM\Column(type="string", length=20, nullable=true)
	 */
	protected $salt;
	/**
	 * @var string This User's id.
	 * @ORM\Column(type="string", length=254, nullable=true, unique=true)
	 */
	protected $username;

	/**
	 * Constructor
	 */
	public function __construct($firstName, $lastName, $username=null,
	        $password=null, $openId=null) {
		$this->openIds = new ArrayCollection();
		$this->setFirstName($firstName);
		$this->setLastName($lastName);

		if (null != $username && null != $password) {
			$this->setUserName($username);
			$this->setPasswordUnhashed($password);
		} else if ($openId != null) {
			$this->addOpenId(new OpenIdIdentity($openId, $this));
		} else {
			throw new RuntimeException(
			        'You have to provide either user name and password, or an open ID.');
		}
	}

	/**
	 * Add openIds
	 * @param OpenIdIdentity OpenID to be added.
	 * @return User
	 */
	public function addOpenId(OpenIdIdentity $openIds) {
		$this->openIds[] = $openIds;
		return $this;
	}

	/**
	 * Removes sensitive data from the user.
	 */
	public function eraseCredentials() {
		// nothing here...
	}

	/**
	 * Getter method for this user's first name.
	 * @return string This user's first name.
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * Getter method for this user's ID.
	 * @return string This user's ID
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Getter method for this user's last name.
	 * @return string This user's last name.
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * Getter method for this user's open IDs.
	 * @return ArrayCollection This user's open IDs.
	 */
	public function getOpenIds() {
		return $this->openIds;
	}

	/**
	 * Getter method for this user's hashed password. On authentication, a
	 * plain-text password will be salted, encoded, and then compared to this
	 * value.
	 * @return string This user's hashed password.
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * Returns the roles granted to the user.
	 *
	 * <code>
	 * public function getRoles()
	 * {
	 *     return array('ROLE_USER');
	 * }
	 * </code>
	 *
	 * @return Role[] The user roles
	 */
	public function getRoles() {
		return array('ROLE_USER');
	}

	/**
	 * Getter method for this user's salt value.
	 * @return string This user's salt value.
	 */
	public function getSalt() {
		return $this->salt;
	}

	/**
	 * Getter method for this user's user name.
	 * @return string This user's user name.
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Remove openIds
	 * @param OpenIdIdentity OpenID to be removed.
	 */
	public function removeOpenId(OpenIdIdentity $openIds) {
		$this->openIds->removeElement($openIds);
	}

	public function serialize() {
		return serialize(
		        array($this->firstName, $this->id, $this->lastName,
		                $this->openIds, $this->password, $this->salt,
		                $this->username));
	}

	/**
	 * Setter method for this user's first name.
	 * @param string This user's first name.
	 * @return User This.
	 */
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
		return $this;
	}

	/**
	 * Setter method for this user's id value.
	 * @param integer This user's id value.
	 * @return User This.
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Setter method for this user's last name.
	 * @param string This user's last name.
	 * @return User This.
	 */
	public function setLastName($lastName) {
		$this->lastName = $lastName;
		return $this;
	}

	/**
	 * Setter method for this user's hashed password.
	 * @param  $password string New value for This user's hashed password.
	 * @return User This.
	 */
	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}

	/**
	 * Setter method for this user's password. Takes the unhashed password, and
	 * sets the new value for hashed password. The unhashed value will not be
	 * saved anywhere.
	 * @param string $unhashed Unhashed password to be set to this user.
	 * @return User This.
	 */
	private function setPasswordUnhashed($unhashed) {
		$generator = new SecureRandom();
		$this->setSalt(base64_encode($generator->nextBytes(15)));

		global $kernel;
		if ('AppCache' == get_class($kernel)) {
			$kernel = $kernel->getKernel();
		}
		$factory = $kernel->getContainer()->get('security.encoder_factory');
		$encoder = $factory->getEncoder($this);
		$this->setPassword(
		        $encoder->encodePassword($unhashed, $this->getSalt()));
		return $this;
	}

	/**
	 * Setter method for this user's salting string.
	 * @param  $salt string This user's salting string.
	 * @return User This.
	 */
	public function setSalt($salt) {
		$this->salt = $salt;
		return $this;
	}

	/**
	 * Setter method for this user's user name value.
	 * @param string This user's user name value.
	 * @return User This.
	 */
	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	public function unserialize($serialized) {
		$arr = unserialize($serialized);
		$this->firstName = $arr[0];
		$this->id = $arr[1];
		$this->lastName = $arr[2];
		$this->openIds = $arr[3];
		$this->password = $arr[4];
		$this->salt = $arr[5];
		$this->username = $arr[6];
	}
}
