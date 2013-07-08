<?php
namespace Sharif\CalendarBundle\FormData\Date;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Optionsresolver\OptionsResolverInterface;

class SingleDateForm extends AbstractType{
	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('year', 'integer', array('label' => 'year'));
		$builder->add('month', 'integer', array('label' => 'month'));
		$builder->add('day', 'integer', array('label' => 'day'));
		$builder->add('type', 'datetype');
	}

	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'single_date';
	}

	/**
	 * @inheritdoc
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		parent::setdefaultoptions($resolver);
		$resolver->setdefaults(array(
			'data_class' => 'Sharif\CalendarBundle\Entity\Date\SingleDate',
			'compound' => true));
	}
}
