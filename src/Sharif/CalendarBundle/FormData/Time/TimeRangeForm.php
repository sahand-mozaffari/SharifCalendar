<?php
namespace Sharif\CalendarBundle\FormData\Time;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TimeRangeForm extends AbstractType {
	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('startTime', 'time', array('widget' => 'single_text',
			'input' => 'datetime', 'label' => 'start_time'));
		$builder->add('endTime', 'time', array('widget' => 'single_text',
			'input' => 'datetime', 'label' => 'end_time'));
	}

	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'time_range';
	}

	/**
	 * @inheritdoc
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'Sharif\CalendarBundle\Entity\Time\TimeRange'));
	}
}
