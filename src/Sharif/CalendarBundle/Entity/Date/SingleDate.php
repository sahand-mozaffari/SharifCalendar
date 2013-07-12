<?php
namespace Sharif\CalendarBundle\Entity\Date;
use \Date;
use \jCalendar;
use \Hijri_GregorianConvert;
use doctrine\Orm\Mapping as ORM;
use sharif\Calendarbundle\Entity\User;
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
	 * @Assert\Range(min=1, max=3000, minMessage="year_at_least_1",
	 *      maxMessage="year_at_most_3000",
	 *      invalidMessage="year_not_valid_integer")
	 */
	protected $year;

	/**
	 * constructor
	 * @param user $user user who owns this date.
	 * @param int $year year.
	 * @param int $month month.
	 * @param int $day day.
	 */
	public function __construct($year = null, $month = null, $day = null,
	                            $type='Gregorian') {
		if($year == null) {
			$year = intval(date("Y"));
		}
		if($month == null) {
			$month = intval(date("n"));
		}
		if($day == null) {
			$day = intval(date("j"));
		}
		$this->setYear($year);
		$this->setMonth($month);
		$this->setDay($day);
		$this->setType($type);
	}

	/**
	 * Casts this date to a date of another type.
	 * @param $type string The other type. This argument can be any of
	 * 'Gregorian', 'Jalai' or 'Lunar-Hijri'.
	 * @return SingleDate The casted date.
	 * @throws RuntimeException When invalid argument is supplied.
	 */
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
						throw new \RuntimeException(
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
	 * Determines whether or not a given date happens after this day.
	 * @param SingleDate $that The other date.
	 * @return bool Whether or not a given date happens after this day.
	 */
	public function isGreaterThan(SingleDate $that) {
		return $this->year > $that->year ||
			$this->year == $that->year && $this->month > $that->month ||
			$this->year == $that->year && $this->month == $that->month &&
				$this->day > $that->day;
	}

	/**
	 * Determines whether or not a given date happens before this day.
	 * @param SingleDate $that The other date.
	 * @return bool Whether or not a given date happens before this day.
	 */
	public function isLessThan(SingleDate $that) {
		return $this->year < $that->year ||
			$this->year == $that->year && $this->month < $that->month ||
			$this->year == $that->year && $this->month == $that->month &&
				$this->day < $that->day;
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
		return new SingleDate($arr[2], $arr[1], $arr[0], 'Gregorian');
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
	 * @return SingleDate $this.
	 */
	public function setDay($day) {
		$this->day = $day;
		return $this;
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
