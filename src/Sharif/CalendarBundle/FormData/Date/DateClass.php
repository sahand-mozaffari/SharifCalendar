<?php
namespace Sharif\CalendarBundle\FormData\Date;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateClass extends AbstractType {
	protected $label;

	function __construct($label='نوع تاریخ') {
		$this->label = $label;
	}

	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'date_class';
	}

	/**
	 * @inheritdoc
	 */
	public function getParent() {
		return 'choice';
	}

	/**
	 * @inheritdoc
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'choices' =>
				array('single' => 'تک', 'annual' => 'سالانه',
					'monthly' => 'ماهانه', 'weekly' => 'هفتگی',
					'daily' => 'روزانه',
				), 'label' => $this->label
		));
	}
}
