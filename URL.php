<?php

/**
 * url utils
 */
class EtuDev_Util_URL {

	static public function get_page_url_without_server($querystring = true) {
		$url = '';
		if ($querystring) {
			$url .= $_SERVER["REQUEST_URI"];
		} else {
			$parts = parse_url($_SERVER["REQUEST_URI"]);
			if ($parts['path']) {
				$url .= $parts['path'];
			} else {
				$url .= $_SERVER['SCRIPT_NAME'];
			}
		}
		return $url;
	}

	static public function get_page_url($querystring = true) {
		$url = 'http';
		if (isset($_SERVER) && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$url .= 's';
		}
		$url .= '://' . $_SERVER['SERVER_NAME'];
		if (isset($_SERVER) && isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
			$url .= ':' . $_SERVER['SERVER_PORT'];
		}
		if ($querystring) {
			$url .= $_SERVER["REQUEST_URI"];
		} else {
			$parts = parse_url($_SERVER["REQUEST_URI"]);
			if ($parts['path']) {
				$url .= $parts['path'];
			} else {
				$url .= $_SERVER['SCRIPT_NAME'];
			}
		}
		return $url;
	}

	static public function build_path_url($path = null, $parameters = array()) {
		if (is_null($path)) {
			$path = self::get_page_url(false);
		}
		if (count($parameters) > 0) {
			$path .= '?';
			$path .= http_build_query($parameters);
		}
		return $path;
	}

	static public function get_ip_address() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}


}