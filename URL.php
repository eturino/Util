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

	static public function change_url_with_query_params($url, $queryParamsToMerge) {
		if (!$queryParamsToMerge) {
			return $url;
		}
		$url_parts = parse_url($url);

		if (@$url_parts['query']) {
			$oldQP = array();
			parse_str($url_parts['query'], $oldQP);
			$queryParts = array_merge($oldQP, $queryParamsToMerge);
		} else {
			$queryParts = $queryParamsToMerge;
		}

		$new_url_parts = $url_parts;
		if ($queryParts) {
			$new_url_parts['query'] = http_build_query($queryParts);
		} else {
			unset($new_url_parts['query']);
		}

		return static::unparse_url($new_url_parts);
	}

	static public function unparse_url($parsed_url) {
		$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
		$user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
		$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
		$pass     = ($user || $pass) ? "$pass@" : '';
		$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
	}

}