<?php
namespace Sharif\CalendarBundle\FormData\Date;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateType extends AbstractType {
	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'date_type';
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
				array('Gregorian' => 'میلادی', 'Jalali' => 'جلالی',
					'Lunar-Hijri' => 'هجری قمری'
				), 'label' => 'نوع تقویم'
		));
	}
}
