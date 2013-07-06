<?php
namespace Sharif\CalendarBundle\FormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SignupDataOpenId extends AbstractType {
	/**
	 * @var string URL of the open ID provider.
	 * @Assert\NotBlank(message="please_provide_openid")
	 */
	protected $openId;

	public function buildForm(FormBuilderInterface $fbi, array $options) {
		$fbi->add('openId', 'text', array('label' => 'open_id',
		        'required' => true, 'trim' => true));
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
	 * Setter method for $opendId filed.
	 * @param string $openId New value for $opendId field.
	 * @return SignupDataOpenId This.
	 */
	public function setOpenId($openId) {
		$this->openId = $openId;
		return $this;
	}
}
