<?php
namespace Sharif\CalendarBundle\FormData\Date;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class RecurringDateForm extends AbstractType {
	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$options['isNullLabel'] = isset($options['hasStartLabel']) ?
			$options['hasStartLabel'] : 'has_start';
		$builder->add('start', 'nullable_single_date', $options);
		$options['isNullLabel'] = isset($options['hasEndLabel']) ?
			$options['hasEndLabel'] : 'has_end';
		$builder->add('end', 'nullable_single_date', $options);
		$options['label'] = 'step';
		$builder->add('step', 'integer', $options);
	}
}
