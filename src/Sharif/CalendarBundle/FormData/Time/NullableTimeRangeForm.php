<?php
namespace Sharif\CalendarBundle\FormData\Time;
use Sharif\CalendarBundle\Entity\Time\TimeRange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

class NullableTimeRangeForm extends AbstractType
        implements DataTransformerInterface {
	private $hasValueLabel;

	function __construct($hasValueLabel='has_time') {
		$this->hasValueLabel = $hasValueLabel;
	}

	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('hasValue', 'checkbox',
			array('label' => $this->hasValueLabel, 'required' => false));
		$builder->add('time_range', 'time_range',
			array('label' => 'time_range', 'required' => false));
		$builder->addModelTransformer($this);
	}

	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'nullable_time_range';
	}

	/**
	 * @inheritdoc
	 */
	public function reverseTransform($value) {
		if($value['hasValue']) {
			return $value['time_range'];
		} else {
			return null;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function transform($value) {
		if($value == null) {
			return array('time_range' => new TimeRange(), 'hasValue' => false);
		} else {
			return array('time_range' => $value, 'hasValue' => true);
		}
	}
}
