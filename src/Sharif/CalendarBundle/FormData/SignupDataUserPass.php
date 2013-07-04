<?php
namespace Sharif\CalendarBundle\FormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SignupDataUserPass extends AbstractType {
	/**
	 * @var string First name
	 * @Assert\NotBlank(message="enter_first_name")
	 * @Assert\MaxLength(limit=50, message="first_name_too_long")
	 */
	protected $firstName;
	/**
	 * @var string First name
	 * @Assert\NotBlank(message="enter_last_name")
	 * @Assert\MaxLength(limit=50, message="last_name_too_long")
	 */
	protected $lastName;
	/**
	 * @var string Password.
	 * @Assert\MinLength(limit=6, message="password_too_short")
	 */
	protected $password;
	/**
	 * @var string User name.
	 * @Assert\MaxLength(limit=254, message="user_name_too_long")
	 * @Assert\Regex(pattern="/^[\w\d._]+$/", message="invalid_username")
	 */
	protected $username;

	public function buildForm(FormBuilderInterface $fbi, array $options) {
		$fbi->add('firstName', 'text', array('label' => 'first_name',
		        'required' => true, 'trim' => true, 'max_length' => 60));
		$fbi->add('lastName', 'text', array('label' => 'last_name',
		        'required' => true, 'trim' => true, 'max_length' => 60));
		$fbi->add('username', 'text', array('label' => 'username',
		        'required' => true, 'trim' => true, 'max_length' => 254));
		$fbi->add('password', 'repeated', array('type' => 'password',
		        'first_options' => array('label' => 'password'),
		        'second_options' => array('label' => 'repeat_password')));
	}

	/**
	 * Getter method for first name field.
	 * @return string First name
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * Getter method for last name.
	 * @return string Last name.
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * Returns name of this type.
	 *
	 * @return string Name of this type.
	 */
	public function getName() {
		return "signupUserPass";
	}

	/**
	 * Getter method for password field.
	 * @return string password.
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * Getter method for user name field.
	 * @return string User name.
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Setter method for first name field.
	 * @param string $firstName New value for first name.
	 * @return SignupDdataOpenId This.
	 */
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
		return $this;
	}

	/**
	 * Setter method for last name.
	 * @param string $lastName New value for last name.
	 * @return SignupDataOpenId This.
	 */
	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	/**
	 * Setter method for password field.
	 * @param string $password New password.
	 * @return SignupDataUserPass This.
	 */
	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}

	/**
	 * Setter method for user name field.
	 * @param string $username New user name.
	 * @return SignupDataUserPass This.
	 */
	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	/**
	 * Letting Symfony know about which type of data this form represents.
	 *
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array('data_class' =>
		        'Sharif\CalendarBundle\FormData\SignUpDataUserPass',
		                'translation_domain' => 'signup'));
	}
}
