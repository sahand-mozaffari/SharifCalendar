<?php
namespace Sharif\CalendarBundle\FormData\Transformer;

use Sharif\CalendarBundle\Entity\Date\SingleDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

class NullableSingleDateForm extends AbstractType
	implements DataTransformerInterface {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		parent::buildForm($builder, $options);
		$isNullLabel = isset($options['isNullLabel']) != null ?
			$options['isNullLabel'] : 'is_null';
		$builder->
			add('isNull', 'checkbox', array('required' => false,
				'label' => $isNullLabel))->
			add('start', 'single_date', $options)->
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
	public function transform($value) {
		if($value == null) {
			return array('date' => new SingleDate(), 'isNull' => false);
		} else {
			return array('date' => $value, 'isNull' => true);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function reverseTransform($value) {
		if($value['isNull']) {
			return $value['date'];
		} else {
			return null;
		}
	}
}
