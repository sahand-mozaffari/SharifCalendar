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
	 * @var string Email address;
	 * @ORM\Column(type="string", length=254, nullable=true)
	 */
	protected $email;
	/**
	 * @var Event Events subscribed by this user.
	 * @ORM\ManyToMany(targetEntity="Event")
	 * @ORM\JoinTable(name="users_events",
	 *      joinColumns={
	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *      },
	 *      inverseJoinColumns={
	 *          @ORM\JoinColumn(name="event_id", referencedColumnName="id")
	 *      }
	 * )
	 */
	protected $events;
	/**
	 * @var integer Unique key
	 * @ORM\Column(type="integer", nullable=false, unique=true)
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Id
	 */
	protected $id;
	/**
	 * @var Label[] Labels held by this user.
	 * @ORM\OneToMany(targetEntity="Label", mappedBy="owner", cascade="all", orphanRemoval=true)
	 */
	protected $labels;
	/**
	 * @var string This User's last name.
	 * @ORM\Column(type="string", length=100, nullable=false)
	 */
	protected $name;
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
	 * @var Label[] Subscribed labels.
	 * @ORM\ManyToMany(targetEntity="Label", inversedBy="subscribers")
	 * @ORM\JoinTable(name="labels_subscribers")
	 */
	protected $subscribedLabels;
	/**
	 * @var string This User's unique name.
	 * @ORM\Column(type="string", length=254, nullable=true, unique=true)
	 */
	protected $username;

	/**
	 * Constructor
	 */
	public function __construct($name, $email, $username=null, $password=null) {
		$this->openIds = new ArrayCollection();
		$this->labels = new ArrayCollection();
		$this->subscribedLabels = new ArrayCollection();
		$this->setName($name);
		$this->setEmail($email);
		$this->setUserName($username);
		$this->setPasswordUnhashed($password);
	}

	/**
	 * Add events
	 * @param Label $events Events to be added.
	 * @return User $this
	 */
	public function addEvent(Event $events) {
		$this->events[] = $events;
		return $this;
	}

	/**
	 * Add labels
	 * @param Label $labels Labels to be added.
	 * @return User $this
	 */
	public function addLabel(Label $labels) {
		$this->labels[] = $labels;
		return $this;
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
	 * Add label to subscribed label's list.
	 * @param Label $label Label to be subscribed to.
	 * @return $this.
	 */
	public function addSubscribedLabel(Label $label) {
		$this->subscribedLabels[] = $label;
		return $this;
	}

	/**
	 * Clears user's labels.
	 */
	public function clearLabels() {
		$this->labels = new ArrayCollection();
	}

	/**
	 * Removes sensitive data from the user.
	 */
	public function eraseCredentials() {
		// nothing here...
	}

	/**
	 * Getter method for all events.
	 * @return Event[] all events.
	 */
	public function getAllEvents() {
		return array_merge($this->getEvents()->toArray(), $this->getSubscribedEvents());
	}

	/**
	 * Getter method for owned events.
	 * @return Event[] Events.
	 */
	public function getEvents() {
		return $this->events;
	}

	/**
	 * Getter method for email field.
	 * @return string Email address.
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Getter method for this user's ID.
	 * @return string This user's ID
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get labels
	 * @return \Doctrine\Common\Collections\Collection Labels
	 */
	public function getLabels() {
		return $this->labels;
	}

	/**
	 * Getter method for this user's last name.
	 * @return string This user's last name.
	 */
	public function getName() {
		return $this->name;
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
	 * Getter method for subscribed event.
	 * @return Event[] Events.
	 */
	public function getSubscribedEvents() {
		$result = array();
		foreach($this->subscribedLabels as $label) {
			$result = array_merge($result, $label->getEvents()->toArray());
		}

		return $result;
	}

	/**
	 * Getter method for subscribed labels.
	 * @return Label[] subscribed labels
	 */
	public function getSubscribedLabels() {
		return $this->subscribedLabels;
	}

	/**
	 * Getter method for this user's user name.
	 * @return string This user's user name.
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Remove events
	 * @param Event $event Event to be removed.
	 */
	public function removeEvent(Event $event) {
		$this->events->removeElement($event);
	}

	/**
	 * Remove labels
	 * @param Label $labels Labels to be removed.
	 */
	public function removeLabel(Label $labels) {
		$this->labels->removeElement($labels);
	}

	/**
	 * Remove openIds
	 * @param OpenIdIdentity OpenID to be removed.
	 */
	public function removeOpenId(OpenIdIdentity $openIds) {
		$this->openIds->removeElement($openIds);
	}

	/**
	 * Remove labels from list of subscribed.
	 * @param Label $labels Label to be removed.
	 */
	public function removeSubscribedLabel(Label $label) {
		$this->subscribedLabels->removeElement($label);
	}

	public function serialize() {
		return urlencode(serialize(array($this->id, $this->name, $this->openIds,
			$this->password, $this->salt, $this->username, $this->email,
			$this->labels, $this->events, $this->subscribedLabels)));
	}

	/**
	 * Setter method for email field.
	 * @param $email string New value for email field.
	 * @return User This.
	 */
	public function setEmail($email) {
		$this->email = $email;
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
	public function setName($name) {
		$this->name = $name;
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
		$arr = unserialize(urldecode($serialized));
		$this->id = $arr[0];
		$this->name = $arr[1];
		$this->openIds = $arr[2];
		$this->password = $arr[3];
		$this->salt = $arr[4];
		$this->username = $arr[5];
		$this->email = $arr[6];
		$this->labels = $arr[7];
		$this->events = $arr[8];
		$this->subscribedLabels = $arr[9];
	}
}
