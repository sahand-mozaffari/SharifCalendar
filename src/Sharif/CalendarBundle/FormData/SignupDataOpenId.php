<?php
namespace Sharif\CalendarBundle\FormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SignupDataOpenId extends AbstractType {
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
	 * @var string URL of the open ID provider.
	 * @Assert\Url(message="not_valid_url")
	 */
	protected $openId;

	public function buildForm(FormBuilderInterface $fbi, array $options) {
		$fbi->add('firstName', 'text', array('label' => 'first_name',
		        'required' => true, 'trim' => true, 'max_length' => 60));
		$fbi->add('lastName', 'text', array('label' => 'last_name',
		        'required' => true, 'trim' => true, 'max_length' => 60));
		$fbi->add('openId', 'text', array('label' => 'open_id',
		        'required' => true, 'trim' => true));
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
	 * @return string Name of this type.
	 */
	public function getName() {
		return "signupOpenId";
	}

	/**
	 * Getter method for $openId field.
	 * @return string $openId field.
	 */
	public function getOpenId() {
		return $this->openId;
	}

	/**
	 * Letting Symfony know about which type of data this form represents.
	 *
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
				'data_class' => 'Sharif\CalendarBundle\FormData\SignUpDataOpenId',
				'translation_domain' => 'signup'));
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
	 * Setter method for $opendId filed.
	 * @param string $openId New value for $opendId field.
	 * @return SignupDataOpenId This.
	 */
	public function setOpenId($openId) {
		$this->openId = $openId;
		return $this;
	}
}
