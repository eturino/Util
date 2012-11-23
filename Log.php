<?php

class EtuDev_Util_Log {


	const MODULE_WEB           = 'app';
	const MODULE_API_SERVER    = 'api';
	const MODULE_API_CALLS     = 'calls';
	const MODULE_LANG          = 'lang';
	const MODULE_404           = '404';
	const MODULE_DB            = 'db';
	const MODULE_REDIRECT      = 'redirect';
	const MODULE_SMS_API       = 'sms';
	const MODULE_SUR           = 'sur';
	const MODULE_DIALOGA_CALLS = 'dcall';
	const MODULE_DATA          = 'data';


	const FORCED = -1; // FORCED to be logged: system is unusable
	const EMERG = Zend_Log::EMERG; // Emergency: system is unusable
	const ALERT = Zend_Log::ALERT; // Alert: action must be taken immediately
	const CRIT = Zend_Log::CRIT; // Critical: critical conditions
	const ERR = Zend_Log::ERR; // Error: error conditions
	const WARN = Zend_Log::WARN; // Warning: warning conditions
	const NOTICE = Zend_Log::NOTICE; // Notice: normal but significant condition
	const INFO = Zend_Log::INFO; // Informational: informational messages
	const DEBUG = Zend_Log::DEBUG; // Debug: debug messages

	static protected $pns = array(self::EMERG => 'EMERG',
								  self::ALERT => 'ALERT',
								  self::CRIT => 'CRIT',
								  self::ERR => 'ERR',
								  self::WARN => 'WARN',
								  self::NOTICE => 'NOTICE',
								  self::INFO => 'INFO',
								  self::DEBUG => 'DEBUG');

	static protected function getLevelName($level) {
		return @static::$pns[$level] ? : $level;
	}


	static public function log404($message = null) {
		if ($message) {
			$message .= '. ';
		}
		$message .= 'URL pedida:' . EtuDev_Util_URL::get_page_url() . ', GET:' . json_encode($_GET);
		return self::logCustom($_SERVER['REQUEST_URI'], $message, self::ERR, self::MODULE_404);
	}

	static public function logException(Exception $exception, $caller = null, $module = null) {
		self::log($caller ? : 'EXCEPTION', $exception->getMessage() . " \n trace: " . $exception->getTraceAsString(), self::ERR, $module);
	}


	/**
	 * log
	 *
	 * Static method for loggin from whereever, without taking care if is a table or whatever.
	 * Useful for try catch exceptions
	 *
	 */
	static public function log($caller, $message, $level, $module = null) {
		if (defined('APP_LOG_PRIORITY') && is_numeric(APP_LOG_PRIORITY) && APP_LOG_PRIORITY < $level) {
			return true;
		}

		$logdir = defined('APP_LOG_DIRECTORY') ? APP_LOG_DIRECTORY : './applog';

		if (!$logdir) {
			throw new Zend_Log_Exception('not valid logdir');
		}

		$module = trim($module) ? : 'error';

		$file_uri = rtrim($logdir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $module . '.log';

		return static::logCustom($caller, $message, $level, $file_uri);
	}

	static protected function logZend($caller, $message, $level, $file_uri) {

		try {
			$logger    = new Zend_Log();
			$writter   = new Zend_Log_Writer_Stream($file_uri);
			$format    = '[ %timestamp% ] caller: %caller% [ %priorityName% ] [ %requestUri% ] from: [ %visitorHost%  / %visitorIp% ]:' . PHP_EOL . ' %message%' . PHP_EOL;
			$formatter = new Zend_Log_Formatter_Simple($format);
			$logger->addWriter($writter);
			$writter->setFormatter($formatter);
			$string = '';
			if (isset($_SERVER['HTTP_REFERER'])) {
				$string .= ' Referer: ' . $_SERVER['HTTP_REFERER'] . ' ';
			}
			$string .= $message;

			$logger->setEventItem('caller', $caller);
			$logger->setEventItem('requestUri', $_SERVER['REQUEST_URI']);
			$logger->setEventItem('visitorHost', @gethostbyaddr($_SERVER['REMOTE_ADDR']));
			$logger->setEventItem('visitorIp', $_SERVER['REMOTE_ADDR']);
			$logger->setEventItem('timestamp', date('d-m-Y H:i:s', time()));

			$logger->log($string, $level);

			return true;
		} catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}


	/**
	 * log
	 *
	 * Static method for loggin from whereever, without taking care if is a table or whatever.
	 * Useful for try catch exceptions
	 *
	 */
	static protected function logCustom($caller, $message, $level, $file_uri) {

		$string = '';

		if (isset($_SERVER['HTTP_REFERER'])) {
			$string .= ' Referer: ' . $_SERVER['HTTP_REFERER'] . ' ';
		}

		$string .= "\n" . $message;

		$requestUri = $_SERVER['REQUEST_URI'];
		$visitorIp  = $_SERVER['REMOTE_ADDR'];
		$timestamp  = date('d-m-Y H:i:s', time()) . ' UTC';

		$priorityName = static::getLevelName($level);

		$logstring = "[ $timestamp ] caller: $caller [ $priorityName ] [ $requestUri ] from: [$visitorIp ]:" . PHP_EOL . " $string" . PHP_EOL;

		try {
			$file_handler = fopen($file_uri, 'a+');
			fwrite($file_handler, $logstring);
			fclose($file_handler);

			return true;
		} catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}


}
