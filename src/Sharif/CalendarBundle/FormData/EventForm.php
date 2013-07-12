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
		$builder->add('title', 'text', array('required' => true,
			'max_length' => 200, 'label' => 'title', 'trim' => true ));
		$builder->add('description', 'textarea', array('max_length' => 2000,
			'label' => 'description', 'trim' => true));
		$builder->add($builder->create('labels', 'hidden')->
			addModelTransformer(new LabelsTransformer()));
		$builder->add('date', 'date_form');
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
			'data_class' => 'Sharif\CalendarBundle\Entity\Event',
			'translation_domain' => 'eventForm'));
	}
}
