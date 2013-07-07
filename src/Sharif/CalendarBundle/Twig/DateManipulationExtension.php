<?php
	namespace Sharif\CalendarBundle\Twig;

	use \DateTime;
	use \jCalendar;
	use \Twig_Extension;
	use \Twig_SimpleFilter;
	use \Hijri_GregorianConvert;

	/**
	 * Twig extension for manipulation of DateTime objects.
	 */
	class DateManipulationExtension extends Twig_Extension
	{
		public function getFilters() {
			return array(
				new \Twig_SimpleFilter('gregorian',
				                       array($this, 'toGregorianString')),
				new \Twig_SimpleFilter('jalali',
				                       array($this, 'toJalaliString')),
				new \Twig_SimpleFilter('lunarHijri',
				                       array($this, 'toLunarHijriString')),
			);
		}

		/**
		 * Returns the name of the extension.
		 *
		 * @return string The extension name
		 */
		public function getName() {
			return 'date_manipulation_extension';
		}

		/**
		 * Formats a DateTime object into a given format, in Georgian calendar.
		 *
		 * @param DateTime $date The date to be formatted.
		 * @param string $format The output format. The formatting spec
		 * follows that of PHP's standard DateTime.format method.
		 *
		 * @return string Formatted output.
		 */
		public function toGregorianString($date, $format="Y-m-d") {
			return $date->format($format);
		}

		/**
		 * Formats a DateTime object into a given format, in Jalali calendar.
		 *
		 * @param DateTime $date The date to be formatted.
		 * @param string $format The output format. The formatting spec
		 * follows that of PHP's standard DateTime.format method.
		 *
		 * @return string Formatted output.
		 */
		public function toJalaliString($date, $format="Y/m/d") {
			$jcal = new jCalendar();
			return $jcal->date($format, $date->getTimestamp());
		}

		/**
		 * Formats a DateTime object into a given format, in Hijri calendar.
		 *
		 * @param DateTime $date The date to be formatted.
		 * @param string $format The output format. The formatting spec
		 * follows that of PHP's standard DateTime.format method.
		 *
		 * @return string Formatted output.
		 */
		public function toLunarHijriString($date, $format="YYYY/MM/DD") {
			$DateConverter = new Hijri_GregorianConvert;
			return $DateConverter->GregorianToHijri($date->format("Y-m-d"),$format);
		}
	}
