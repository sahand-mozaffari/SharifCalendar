<?php
namespace Sharif\CalendarBundle\FormData\Date;
use Sharif\CalendarBundle\Entity\Date\AnnualDate;
use Sharif\CalendarBundle\Entity\Date\DailyDate;
use Sharif\CalendarBundle\Entity\Date\MonthlyDate;
use Sharif\CalendarBundle\Entity\Date\SingleDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateForm extends AbstractType implements DataTransformerInterface {
	protected $hasEndLabel;
	protected $hasStartLabel;
	protected $baseDateLabel;
	protected $singleDateLabel;
	protected $stepLabel;
	protected $dateTypeLabel;

	function __construct($hasStartLabel='تاریخ آغاز دارد؟',
	                     $hasEndLabel='تاریخ پایان دارد؟',
	                     $dateTypeLabel='نوع تقویم',
	                     $baseDateLabel='تاریخ پایه',
	                     $singleDateLabel='تاریخ تک', $stepLabel='گام') {
		$this->baseDateLabel = $baseDateLabel;
		$this->hasEndLabel = $hasEndLabel;
		$this->hasStartLabel = $hasStartLabel;
		$this->singleDateLabel = $singleDateLabel;
		$this->stepLabel = $stepLabel;
		$this->dateTypeLabel = $dateTypeLabel;
	}


	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('date_class', new DateClass($this->dateTypeLabel))->
			add('single_date', 'single_date',
				array('label' => $this->singleDateLabel))->
			add('base_date', 'single_date',
				array('label' => $this->baseDateLabel))->
			add('step', 'integer', array('label' => $this->stepLabel,
				'required' => true, 'invalid_message' => 'invalid_number'))->
			add('start_date', new NullableSingleDateForm($this->hasStartLabel,
				'تاریخ شروع'), array('label' => ' '))->
			add('end_date', new NullableSingleDateForm($this->hasEndLabel,
				'تاریخ پایان'), array('label' => ' '))->
			addModelTransformer($this);
	}

	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'date_form';
	}

	/**
	 * @inheritdoc
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'translation_domain' => 'dateForm'));
	}

	/**
	 * @inheritdoc
	 */
	public function reverseTransform($value) {
		switch($value['date_class']) {
			case 'single' :
				return $value['single_date'];
			case 'annual' :
				return new AnnualDate($value['base_date'], $value['start_date'],
					$value['end_date'], $value['step']);
			case 'monthly' :
				return new MonthlyDate($value['base_date'], $value['start_date'],
					$value['end_date'], $value['step']);
			case 'weekly' :
				return new DailyDate($value['base_date'], $value['start_date'],
					$value['end_date'], $value['step'] * 7);
			case 'daily' :
				return new DailyDate($value['base_date'], $value['start_date'],
					$value['end_date'], $value['step']);
		}
		return null;
	}

	/**
	 * @inheritdoc
	 */
	public function transform($value) {
		if($value == null) {
			return array('date_class' => 'single',
				'single_date' => new SingleDate(),
				'base_date' => new SingleDate(),
				'start_date' => null,
				'step' => 1,
				'end_date' => null
			);
		} elseif($value instanceof SingleDate) {
			return array('date_class' => 'single',
				'single_date' => $value,
				'base_date' => new SingleDate(),
				'start_date' => null,
				'step' => 1,
				'end_date' => null
			);
		} elseif($value instanceof AnnualDate) {
			return array('date_class' => 'annual',
				'single_date' => new SingleDate(),
				'base_date' => $value->getBase(),
				'start_date' => $value->getStart(),
				'step' => $value->getStep(),
				'end_date' => $value->getEnd()
			);
		} elseif($value instanceof MonthlyDate) {
			return array('date_class' => 'monthly',
				'single_date' => new SingleDate(),
				'base_date' => $value->getBase(),
				'start_date' => $value->getStart(),
				'step' => $value->getStep(),
				'end_date' => $value->getEnd()
			);
		} else { // ($value instanceof DailyDate)
			if($value->getStep() % 7 == 0) {
				return array('date_class' => 'weekly',
					'single_date' => new SingleDate(),
					'base_date' => $value->getBase(),
					'start_date' => $value->getStart(),
					'step' => $value->getStep() / 7,
					'end_date' => $value->getEnd()
				);
			} else {
				return array('date_class' => 'daily',
					'single_date' => new SingleDate(),
					'base_date' => $value->getBase(),
					'start_date' => $value->getStart(),
					'step' => $value->getStep(),
					'end_date' => $value->getEnd()
				);
			}
		}
	}
}
