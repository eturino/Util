<?php

/**
 * based on http://openexchangerates.org/
 */
class EtuDev_Util_Currency {

	/**
	 * @var string
	 */
	protected $api_key = null;

	/**
	 * @var int
	 */
	protected $seconds_ttl = 3600;

	/**
	 * @var array
	 */
	protected $currencies = array();

	/**
	 * @var array
	 */
	protected $rates = array();

	/**
	 * @var int
	 */
	protected $timestamp = null;

	/**
	 * @var bool
	 */
	protected $lookup_for_rates_if_needed = true;

	/**
	 * @param string $api_key
	 * @param int    $seconds_ttl
	 */
	public function __construct($api_key, $seconds_ttl = 3600) {
		$this->api_key     = $api_key;
		$this->seconds_ttl = $seconds_ttl;
	}

	/**
	 * @return $this
	 */
	public function disableLookupForRates() {
		$this->lookup_for_rates_if_needed = false;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function enableLookupForRates() {
		$this->lookup_for_rates_if_needed = true;
		return $this;
	}

	/**
	 * check if there are no rates or if the timestamp is more than $seconds_ttl seconds in the past
	 *
	 * @return bool
	 */
	public function isRatesTooOld() {
		return (!$this->rates || ($this->timestamp + $this->seconds_ttl) < time());
	}

	/**
	 * @return EtuDev_Util_Currency
	 */
	public function retrieveCurrenciesIfNotSet() {
		if (!$this->currencies) {
			$this->loadCurrencies();
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function needRetrieveRates() {
		return $this->lookup_for_rates_if_needed && (!$this->rates || $this->isRatesTooOld());
	}

	/**
	 * @return $this
	 */
	public function loadRates() {
		$this->load();
		return $this;
	}

	/**
	 * if there are no rates or if they are too old, load()
	 *
	 * @return EtuDev_Util_Currency
	 */
	public function retrieveRatesIfNeeded() {
		if ($this->needRetrieveRates()) {
			$this->load();
		}
		return $this;
	}

	/**
	 * public setter if it is already cached
	 *
	 * @param array $currencies associative array
	 *
	 * @return EtuDev_Util_Currency
	 */
	public function setCurrencies($currencies) {
		$this->currencies = (array) $currencies;
		return $this;
	}

	/**
	 * public setter if it is already cached
	 *
	 * @param array $rates
	 *
	 * @return EtuDev_Util_Currency
	 */
	public function setRates($rates) {
		$this->rates = $rates;
		return $this;
	}

	/**
	 * public setter if it is already cached
	 *
	 * @param int $timestamp
	 *
	 * @return EtuDev_Util_Currency
	 */
	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
		return $this;
	}

	protected function loadCurrencies() {
		$this->currencies = (array) $this->doRequest('currencies.json');
	}

	protected function load() {
		$changes = $this->doRequest('latest.json');
		if ($changes) {
			$this->rates     = $changes['rates'];
			$this->timestamp = (int) $changes['timestamp'];
		}
	}

	protected function doRequest($file) {
		$appId = $this->api_key;

		$ch = curl_init("http://openexchangerates.org/api/{$file}?app_id={$appId}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);

		$json = curl_exec($ch);
		curl_close($ch);

		return json_decode($json, true);
	}

	/**
	 * @return string
	 */
	public function getApiKey() {
		return $this->api_key;
	}

	/**
	 * @return array
	 */
	public function getCurrencies() {
		return $this->currencies;
	}

	/**
	 * @return array
	 */
	public function getRates() {
		return $this->rates;
	}

	/**
	 * @return int
	 */
	public function getSecondsTtl() {
		return $this->seconds_ttl;
	}

	/**
	 * @return int
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}


}
