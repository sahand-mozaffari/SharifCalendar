<?php
namespace Sharif\CalendarBundle\FormData\Date;
use Sharif\CalendarBundle\Entity\Date\SingleDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

class NullableSingleDateForm extends AbstractType
	implements DataTransformerInterface {
	private $hasValueLabel;
	private $valueLabel;

	function __construct($hasValueLabel='hasValue', $valueLabel='value') {
		$this->hasValueLabel = $hasValueLabel;
		$this->valueLabel = $valueLabel;
	}

	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->
			add('hasValue', 'checkbox', array('required' => false,
				'label' => $this->hasValueLabel))->
			add('dateValue', 'single_date', array('label' => $this->valueLabel))->
			addModelTransformer($this);
	}

	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'nullable_single_date';
	}

	/**
	 * @inheritdoc
	 */
	public function reverseTransform($value) {
		if($value['hasValue']) {
			return $value['dateValue'];
		} else {
			return null;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function transform($value) {
		if($value == null) {
			return array('dateValue' => new SingleDate(), 'hasValue' => false);
		} else {
			return array('dateValue' => $value, 'hasValue' => true);
		}
	}
}
