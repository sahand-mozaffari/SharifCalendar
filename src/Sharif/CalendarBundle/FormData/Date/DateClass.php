<?php
namespace Sharif\CalendarBundle\FormData\Date;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateClass extends AbstractType {
	protected $label;

	function __construct($label='class') {
		$this->label = $label;
	}

	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'date_class';
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
		global $kernel;
		if ('AppCache' == get_class($kernel)) {
			$kernel = $kernel->getKernel();
		}
		$translator = $kernel->getContainer()->get('translator');

		$resolver->setDefaults(array(
			'choices' =>
				array(
					'single' =>
						$translator->trans('single', array(), 'dateClass'),
					'annual' =>
						$translator->trans('annual', array(), 'dateClass'),
					'monthly' =>
						$translator->trans('monthly', array(), 'dateClass'),
					'weekly' =>
						$translator->trans('weekly', array(), 'dateClass'),
					'daily' =>
						$translator->trans('daily', array(), 'dateClass'),
				), 'label' => $this->label
		));
	}
}
