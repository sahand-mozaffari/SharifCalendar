<?php
namespace Sharif\CalendarBundle\FormData;
use Sharif\CalendarBundle\FormData\Transformer\LabelsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class EventForm  extends AbstractType {
	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$opts = array('required' => true, 'max_length' => 200,
			'label' => 'عنوان', 'trim' => true);
		if(isset($options['data'])) {
			$opts = array_merge($opts,
				array('data' => $options['data']->getTitle()));
		}
		$builder->add('title', 'text', $opts);

		$opts = array('max_length' => 2000, 'label' => 'توضیحات',
			'trim' => true);
		if(isset($options['data'])) {
			$opts = array_merge($opts,
				array('data' => $options['data']->getDescription()));
		}
		$builder->add('description', 'textarea', $opts);

		$arr = array();
		if(isset($options['data'])) {
			foreach($options['data']->getLabels() as $label) {
				$arr[] = $label->getId();
			}
		}
		$builder->add($builder->create('labels', 'hidden',
			array('label' => 'برچسب‌ها', 'data' => json_encode($arr)))->
			addModelTransformer(new LabelsTransformer()));

		$builder->add('date', 'date_form');
		$builder->add('timeRange', 'nullable_time_range',
			array('label' => ' '));
	}

	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'event';
	}

	/**
	 * @inheritdoc
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'Sharif\CalendarBundle\Entity\Event'));
	}
}
