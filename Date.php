<?php


/**
 * date utils
 */
class EtuDev_Util_Date {


	/**
	 * set DateTime to TODAY, but keeping the time data
	 *
	 * @param DateTime $dt
	 * @param bool     $mutable if false, create a new DateTime using clone($dt); if else then use same object
	 *
	 * @return DateTime
	 */
	static public function dateTimeSetToday(DateTime $dt, $mutable = true) {
		$a = getdate();
		if ($mutable) {
			$x = $dt;
		} else {
			$x = clone($dt);
		}
		$x->setDate($a['year'], $a['mon'], $a['mday']);
		return $x;
	}


	/**
	 * @static
	 * @throws Exception
	 *
	 * @param int|DateTime             $minHour
	 * @param DateTimeZone|string|null $timezone
	 *
	 * @return DateTime|null
	 */
	static public function getDateTimeNowPlusHours($minHour, $timezone = null) {
		$tz = self::getDateTimeZone($timezone);
		/** @var $mh DateTime */
		if ($minHour instanceof DateTime) {
			$mh = $minHour;
			static::dateTimeSetToday($mh);
			return $mh;
		}

		if (is_numeric($minHour) && $minHour > 0) {
			$mh_entero  = floor($minHour);
			$mh_decimal = $minHour - $mh_entero;

			if ($mh_decimal) {
				$mh_minutos = floor($mh_decimal * 60);
			} else {
				$mh_minutos = 0;
			}

			$mh = static::getDateTimeToday(true, $tz);
			$mh->add(new DateInterval('PT' . $mh_entero . 'H' . ($mh_minutos ? $mh_minutos . 'M' : '')));

			unset($mh_entero);
			unset($mh_decimal);
			unset($mh_minutos);

			return $mh;
		}

		if ($minHour) {
			$mh = DateTime::createFromFormat('H:i', $minHour, $tz);
			static::dateTimeSetToday($mh);
			if (!$mh) {
				throw new Exception('invalid minhour, needs to be in H:i format');
			}

			return $mh;
		}

		return null;
	}


	/**
	 * @static
	 *
	 * @param bool         $with_time
	 * @param DateTimeZone $timezone
	 *
	 * @return DateTime
	 */
	static public function getDateTimeToday($with_time = false, DateTimeZone $timezone = null) {
		$d = new DateTime('now', $timezone ? : static::getDateTimeZone());
		if (!$with_time) {
			$d->setTime(0, 0, 0);
		}
		return $d;
	}


	/**
	 * @static
	 *
	 * @param string|DateTimeZone $tz the actual TimeZone, or a TimeZone description to use in the DateTimeZone constructor, if there is none, use UTC
	 *
	 * @return DateTimeZone
	 */
	static public function getDateTimeZone($tz = null) {
		if ($tz instanceof DateTimeZone) {
			return $tz;
		}

		if (!is_string($tz) && $tz['timezone']) {
			$tz = $tz['timezone'];
		}

		if (!$tz || !is_string($tz)) {
			$tz = EtuDev_Zend_Util_App::getZFConfig('timezone') ? : 'UTC';
		}

		return new DateTimeZone($tz);
	}

	/**
	 * Get the current datetime string
	 *
	 * @param string $now
	 *
	 * @return string
	 */
	static public function getDateTimeNowNoOffsetString($now = 'Y-m-d H:i:s') {
		return date($now, time());
	}

	static public function isZeroDate($date_string) {
		return (!$date_string || $date_string == '0000-00-00' || $date_string == '0000-00-00 00:00:00');
	}

	/**
	 * @static
	 *
	 * @param string                   $date Date in Y-m-d format
	 * @param string                   $time Time in H:i format, without seconds
	 * @param string|DateTimeZone|null $timezone the timezone to use (the actual object or its descriptor), if void uses the default one, given by getDateTimeZone()
	 *
	 * @return DateTime|null
	 * @uses getDateTimeZone()
	 *
	 */
	static public function createDateTimeWithDateAndTime($date, $time, $timezone = null) {
		if (is_string($date)) { //quitamos las horas en caso de que por error se pasen como 0s
			$y = preg_match('/\d{4}-\d{2}-\d{2}/', $date, $matches);
			if ($y) {
				$date = $matches[0];
			}
		}

		try {
			return DateTime::createFromFormat('Y-m-d H:i', trim($date) . ' ' . trim($time), self::getDateTimeZone($timezone)) ? : null;
		} catch (Exception $e) {
			//try with seconds
			try {
				return DateTime::createFromFormat('Y-m-d H:i:s', trim($date) . ' ' . trim($time), self::getDateTimeZone($timezone)) ? : null;
			} catch (Exception $e) {
				//assume the time is in an incorrect format
				return null;
			}
		}
	}


	/**
	 * checks if the given string represents a date in the format "YYYY-MM-DD" or "YYYY-MM-DD HH:MM:SS"
	 *
	 * @param string $string
	 *
	 * @return bool
	 */
	static public function isDate($string) {
		if (!$string) {
			return false;
		}

		if (preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}$/', $string)) {
			return true;
		}

		if (preg_match('/^[\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2}$/', $string)) {
			return true;
		}

		if (preg_match('/^[\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2}:[\d]{2}$/', $string)) {
			return true;
		}

		return false;
	}


	static public function getDateSection($string) {
		$matches = array();
		if (preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}$/', $string, $matches)) {
			return $string;
		}

		if (preg_match('/^[\d]{4}-[\d]{2}-[\d]{2} /', $string, $matches)) {
			if ($matches) {
				return trim($matches[0]);
			}
		}

		return null;
	}


	/**
	 * Format a date from the database making it ready for use in input fields (matches input format)
	 *
	 * @param string $date_string Date string to format
	 *
	 * @return string Formatted date
	 */
	static public function formatDateOutput($date_string) {
		if (trim($date_string) == '') {
			return '';
		}
		// If we have a zero date, properly replace its values since strtotime will fail on this
		if (static::isZeroDate($date_string)) {
			return str_replace(array(' ', 'm', 'd', 'Y'), array(static::getFormatInputSeparator(), '00', '00', '0000'), static::getFormatInput());
		} else {
			return date(str_replace('-', static::getFormatInputSeparator(), static::getFormatInput()), strtotime($date_string));
		}
	}

	/**
	 * Format a date from input fields so its ready to go into the DB.
	 *
	 * @param $date_string
	 *
	 * @return string
	 */
	static public function formatDateInput($date_string) {
		if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $date_string)) {
			return $date_string;
		}

		// Split the date by anything non numerical
		$date_string_parts = preg_split('/[^0-9]{1}/', $date_string, 3);

		// If we have a zero in any part date return the 000-00-00 date/time
		if (in_array($date_string_parts[0], array('0', '00', '0000')) OR $date_string == '') {
			return '0000-00-00 00:00:00';
		}

		$date_array = array();
		// Match the pieces to the day, month, and year parts
		foreach (explode('-', static::getFormatInput()) as $key => $date_part) {
			$date_array[$date_part] = $date_string_parts[$key];
		}
		// Return a MySQL date/time compatible string
		return date('Y-m-d H:i:s', mktime(0, 0, 0, $date_array['m'], $date_array['d'], $date_array['Y']));
	}

	const FORMAT_ES = 'es';
	const FORMAT_EN = 'en';

	static protected $format_type = static::FORMAT_EN;

	static protected $formats = array(static::FORMAT_ES => array('format' => '%d/%m/%Y', 'input' => 'd-m-Y', 'separator' => '/'),
									  static::FORMAT_EN => array('format' => '%m/%d/%Y', 'input' => 'm-d-Y', 'separator' => '/'),);

	static protected $dateFormat = '%m/%d/%Y';
	static protected $dateFormatInput = 'm-d-Y';
	static protected $dateFormatInputSeparator = '/';


	static public function changeFormat($format) {
		if (array_key_exists($format, static::$formats)) {
			static::$format_type = $format;
		}

		return static::$format_type;
	}

	static public function getFormatInput() {
		return static::$formats[static::$format_type]['input'];
	}

	static public function getFormat() {
		return static::$formats[static::$format_type]['format'];
	}

	static public function getFormatInputSeparator() {
		return static::$formats[static::$format_type]['separator'];
	}

}