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
		$d = new DateTime('now', $timezone ?: static::getDateTimeZone());
		if (!$with_time) {
			$d->setTime(0, 0, 0);
		}
		return $d;
	}


	/**
	 * @static
	 * @param string|DateTimeZone $tz the actual TimeZone, or a TimeZone description to use in the DateTimeZone constructor, if there is none, use UTC
	 * @return DateTimeZone
	 */
	static public function getDateTimeZone($tz = null){
		if($tz instanceof DateTimeZone){
			return $tz;
		}

		if(!is_string($tz) && $tz['timezone']){
			$tz = $tz['timezone'];
		}

		if(!$tz || !is_string($tz)){
			$tz = EtuDev_Zend_Util_App::getZFConfig('timezone') ?: 'UTC';
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
	 * @param string $date Date in Y-m-d format
	 * @param string $time Time in H:i format, without seconds
	 * @param string|DateTimeZone|null $timezone the timezone to use (the actual object or its descriptor), if void uses the default one, given by getDateTimeZone()
	 * @return DateTime|null
	 * @uses getDateTimeZone()
	 *
	 */
	static public function createDateTimeWithDateAndTime($date, $time, $timezone = null){
		if(is_string($date)){ //quitamos las horas en caso de que por error se pasen como 0s
			$y = preg_match('/\d{4}-\d{2}-\d{2}/', $date, $matches);
			if($y){
				$date = $matches[0];
			}
		}

		try{
			return DateTime::createFromFormat('Y-m-d H:i', trim($date). ' ' . trim($time), self::getDateTimeZone($timezone)) ?: null;
		}catch(Exception $e){
			//try with seconds
			try{
				return DateTime::createFromFormat('Y-m-d H:i:s', trim($date) . ' ' . trim($time), self::getDateTimeZone($timezone)) ?: null;
			}catch(Exception $e){
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

}