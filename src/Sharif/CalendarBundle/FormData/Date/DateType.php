<?php
namespace Sharif\CalendarBundle\FormData\Date;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateType extends AbstractType {
	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'datetype';
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
					'Gregorian' =>
						$translator->trans('gregorian', array(), 'submitFrom'),
					'Jalali' =>
						$translator->trans('jalali', array(), 'submitFrom'),
					'Lunar-Hijri' =>
						$translator->trans('lunar_hijri', array(), 'submitFrom')
				), 'label' => 'type'
		));
	}
}
