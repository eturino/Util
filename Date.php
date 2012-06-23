<?php


/**
 * date utils
 */
class EtuDev_Util_Date {

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
			$tz = defined('BR_DEFAULT_TIMEZONE') ? BR_DEFAULT_TIMEZONE : 'UTC';
		}

		return new DateTimeZone($tz);
	}
}