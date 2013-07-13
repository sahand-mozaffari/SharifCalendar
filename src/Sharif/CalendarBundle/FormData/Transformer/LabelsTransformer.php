<?php
namespace Sharif\CalendarBundle\FormData\Transformer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;

class LabelsTransformer implements DataTransformerInterface {
	/**
	 * @inheritdoc
	 */
	public function reverseTransform($value) {
		if($value == null) {
			return new ArrayCollection();
		} else {
			$arr = json_decode($value);
			global $kernel;
			if ('AppCache' == get_class($kernel)) {
				$kernel = $kernel->getKernel();
			}
			$doctrine = $kernel->getContainer()->get('doctrine');
			$repository =
				$doctrine->getRepository('SharifCalendarBundle:Label');

			$result = new ArrayCollection();
			foreach($arr as $id) {
				$result[] = $repository->findOneById($id);
			}
			return $result;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function transform($value) {
		if($value == null) {
			return "";
		}
		return $value;
//		$arr = array();
//		foreach($value as $label) {
//			$arr[] = $label->getId();
//		}
//		return json_encode($arr);
	}
}
