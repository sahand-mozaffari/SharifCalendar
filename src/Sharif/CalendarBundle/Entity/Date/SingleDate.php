<?php
namespace Sharif\CalendarBundle\Entity\Date;
use \Date;
use \jCalendar;
use \Hijri_GregorianConvert;
use doctrine\Orm\Mapping as ORM;
use sharif\Calendarbundle\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Optionsresolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * class singledate
 * represents a single date in calendar
 * @package sharif\Calendarbundle\Entity\Date
 * @ORM\Entity
 */
class SingleDate extends AbstractDate {
	/**
	 * @var int day.
	 * @ORM\Column(type="integer", nullable=false)
	 * @Assert\NotBlank(message="year_cant_be_blank")
	 * @Assert\Type(type="integer", message="year_not_valid_integer")
	 */
	protected $day;
	/**
	 * @var int month.
	 * @ORM\Column(type="integer", nullable=false)
	 * @Assert\NotBlank(message="month_cant_be_blank")
	 * @Assert\Range(min=1, max=12, minMessage="month_at_least_1",
	 *      maxMessage="month_at_most_12",
	 *      invalidMessage="month_not_valid_integer")
	 */
	protected $month;
	/**
	 * @var int year.
	 * @ORM\Column(type="integer", nullable=false)
	 * @Assert\NotBlank(message="day_cant_be_blank")
	 * @Assert\Range(min=1, max=31, minMessage="day_at_least_1",
	 *      maxMessage="day_at_most_31",
	 *      invalidMessage="day_not_valid_integer")
	 */
	protected $year;

	/**
	 * constructor
	 * @param user $user user who owns this date.
	 * @param int $year year.
	 * @param int $month month.
	 * @param int $day day.
	 */
	public function __construct($year, $month, $day, $type='Gregorian') {
		$this->setYear($year);
		$this->setMonth($month);
		$this->setDay($day);
		$this->setType($type);
	}

	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('integer', array('precision' => 0, 'label' => 'year'));
		$builder->add('integer', array('precision' => 0, 'label' => 'month'));
		$builder->add('integer', array('precision' => 0, 'label' => 'day'));
	}

	public function castTo($type) {
		switch($this->type) {
			case 'Gregorian':
				switch($type) {
					case 'Gregorian':
						return clone $this;
					case 'Lunar-Hijri':
						return $this->gregorianToLunarHijri();
					case 'Jalali':
						return $this->gregorianToJalali();
					default:
						throw RuntimeException(
								'Invalid date type: ['.$type.']');
				}
				break;
			case 'Lunar-Hijri':
				switch($type) {
					case 'Gregorian':
						return $this->lunarHijriToGregorian();
						break;
					case 'Lunar-Hijri':
						return clone $this;
					case 'Jalali':
						return $this->lunarHijriToJalali();
					default:
						throw RuntimeException(
								'Invalid date type: ['.$type.']');
				}
				break;
			case 'Jalali':
				switch($type) {
					case 'Gregorian':
						return $this->jalaliToGregorian();
					case 'Lunar-Hijri':
						return $this->jalaliToLunarHijri();
					case 'Jalali':
						return clone $this;
					default:
						throw RuntimeException(
								'Invalid date type: ['.$type.']');
				}
				break;
			default:
				throw RuntimeException('Invalid date type: ['.$this->type.']');
		}
	}

	/**
	 * getter method for day field.
	 * @return int day.
	 */
	public function getDay() {
		return $this->day;
	}

	/**
	 * getter method for month field.
	 * @return int month
	 */
	public function getMonth() {
		return $this->month;
	}

	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'singledate';
	}

	/**
	 * getter method for year field.
	 * @return int year
	 */
	public function getYear() {
		return $this->year;
	}

	/**
	 * Casts this date from Gregorian to Jalali.
	 * @return SingleDate Jalali representation of this date.
	 */
	private function gregorianToJalali() {
		$jcal = new jCalendar();
		$arr = $jcal->getdate(mktime(null, null, null, $this->month, $this->day,
				$this->year));
		return new SingleDate($arr['year'], $arr['mon'], $arr['mday'],
				'Jalali');
	}

	/**
	 * Casts this date from Gregorian to Lunar Hijri.
	 * @return SingleDate Lunar Hijri representation of this date.
	 */
	private function gregorianToLunarHijri() {
		$DateConv = new Hijri_GregorianConvert();
		$str = $DateConv->GregorianToHijri(date('Y/m/d', $this->toTimeStamp()),
				'YYYY/MM/DD');
		$arr = explode('-', $str);
		return new SingleDate($arr[2], $arr[1], $arr[0], 'Lunar-Hijri');
	}

	/**
	 * Casts this date from Jalali to Gregorian.
	 * @return SingleDate Gregorian representation of this date.
	 */
	private function jalaliToGregorian() {
		$jcal = new jCalendar();
		$arr = getDate($jcal->mktime(0, 0, 0, $this->month, $this->day,
			$this->year));
		return new SingleDate($arr['year'], $arr['mon'], $arr['mday']);
	}

	/**
	 * Casts this date from Jalali to Lunar Hijri.
	 * @return SingleDate Lunar Hijri representation of this date.
	 */
	private function jalaliToLunarHijri() {
		return $this->jalaliToGregorian()->gregorianToLunarHijri();
	}

	/**
	 * Casts this date from Lunar Hijri to Gregorian.
	 * @return SingleDate Gregorian representation of this date.
	 */
	private function lunarHijriToGregorian() {
		$DateConv = new Hijri_GregorianConvert();
		$str = $DateConv->HijriToGregorian(
				sprintf("%04d/%02d/%02d", $this->getYear(), $this->getMonth(),
				$this->getDay()), 'YYYY/MM/DD');
		$arr = explode('-', $str);
		return new SingleDate($arr[0], $arr[1], $arr[2], 'Gregorian');
	}

	/**
	 * Casts this date from Lunar Hijri to Jalali.
	 * @return SingleDate Jalali representation of this date.
	 */
	private function lunarHijriToJalali() {
		return $this->lunarHijriToGregorian()->gregorianToJalali();
	}

	/**
	 * @inheritdoc
	 */
	public function matches(SingleDate $that) {
		$that = $that->castTo($this->type);
		return $this->year == $that->year && $this->month == $that->month &&
				$this->day == $that->day;
	}

	public function toTimeStamp() {
		return mktime(date("H"),date("i"),date("s"), $this->getMonth(),
				$this->getDay(), $this->getYear());
	}

	/**
	 * setter method for day field.
	 * @param int $year new value for year.
	 * @return singledate $this.
	 */
	public function setDay($day) {
		$this->day = $day;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		parent::setdefaultoptions($resolver);
		$resolver->setdefaults(array('data_class' =>
				'sharif\Calendarbundle\Entity\Date\Singledate'));
	}

	/**
	 * setter method for month field.
	 * @param int $month new value for month field.
	 * @return singledate this.
	 */
	public function setMonth($month) {
		$this->month = $month;
		return $this;
	}

	/**
	 * setter method for year field.
	 * @param int $year new value for year field.
	 * @return singledate this.
	 */
	public function setYear($year) {
		$this->year = $year;
		return $this;
	}

	/**
	 * Casts this date to PHP DateTime object.
	 * @return DateTime PHP DateTime object equivalent to this date.
	 */
	public function toDate() {
		$gregorian = $this->castTo('Gregorian');
		$date =  new \DateTime();
		return $date->setDate($gregorian->year, $gregorian->month,
				$gregorian->day);
	}
}
