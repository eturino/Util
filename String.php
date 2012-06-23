<?php

/**
 * string functions wrapper and util functions to String
 */
class EtuDev_Util_String {

	static protected $rewrite_string_to_void = array("&#039;", '<br>', '<br/>', '&quot');
	static protected $rewrite_words_to_dash = array('-be-',
													'-had-',
													'-it-',
													'-was-',
													'-its-',
													'-of-',
													'-we-',
													'-on-',
													'-but-',
													'-by-',
													'-her-',
													'-or-',
													'-an-',
													'-can-',
													'-mr-',
													'-the-',
													'-who-',
													'-any-',
													'-if-',
													'-mrs-',
													'-out-',
													'-and-',
													'-corp-',
													'-in-',
													'-are-',
													'-inc-',
													'-mz-',
													'-s-',
													'-as-',
													'-for-',
													'-so-',
													'-this-',
													'-up-',
													'-at-',
													'-from-',
													'-is-',
													'-says-',
													'-to-',);

	/**
	 * old function rewrite
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	static public function rewriteString($str) {
		return str_replace('--', '-', str_replace(static::$rewrite_words_to_dash, '-', preg_replace('/[\s]/', '-', preg_replace("/[^a-zA-Z0-9&]+/", "-", preg_replace('/[[:punct:]]/', '-', preg_replace('/[%\?"¿\.]/', '', str_replace(static::$rewrite_string_to_void, '', preg_replace('@\pM@u', '', Normalizer::normalize($str, Normalizer::FORM_D)))))))));

	}

	static public function getRandomStringOnlyChar($length, $filter = array()) {
		return self::getRandomString($length, array_merge(range(1, 9), $filter));
	}

	static public function getRandomStringOnlyNum($length, $filter = array()) {
		return self::getRandomString($length, array_merge(range('A', 'Z'), $filter));
	}

	static public function getRandomString($length, $filter = array()) {
		$array = array_diff(array_merge(range(1, 9), range('A', 'Z')), $filter);
		shuffle($array);
		return implode('', array_slice($array, 0, $length));
	}

	static public function toCamelCase($str, $capitaliseFirstChar = false) {
		if ($capitaliseFirstChar) {
			$str[0] = strtoupper($str[0]);
		}

		return preg_replace('/_([a-z])/e', "strtoupper('\\1')", $str);
	}

	static public function trimToNearestWordWithDefaultAllowTags($content, $chars, $append = '...') {
		return static::trimToNearestWord($content, $chars, $append, '<br><strong><b>');
	}

	/**
	 * @static
	 *
	 * @param string $content
	 * @param int    $chars
	 * @param string $append
	 * @param null   $allowedTags argument to strip_tags (ie: '<br><p>')
	 * @param bool   $repairHTML if true calls repairHTML() to the result
	 *
	 * @return string
	 */
	static public function trimToNearestWord($content, $chars, $append = '...', $allowedTags = null, $repairHTML = true) {
		if (static::strlen($content) > $chars) {
			$content = str_replace('&nbsp;', ' ', $content); //html space to space
			$content = str_replace("\n", ' ', $content); //new line to space
			$content = strip_tags(trim($content), $allowedTags);
			$c       = static::substr($content, 0, $chars, false);
			$content = preg_replace('/\s+?(\S+)?$/', '', $c);

			$content = trim($content) . $append;
			if ($repairHTML) {
				$content = static::repairHTML($content);
			}
			return $content;
		}

		return $content;
	}

	static public function multipleSpacesToSingleSpace($string) {
		return preg_replace('/[ \t\n\r]+/', ' ', $string);
	}

	static public function removeAllSpaces($string) {
		return preg_replace('/[ \t\n\r]+/', '', $string);
	}

	static public function removeAllButNumbers($string, $allowPlus = false) {
		$patron = $allowPlus ? '/[^0-9+]+/' : '/[^0-9]+/';
		return preg_replace($patron, '', $string);
	}

	/**
	 * @static
	 *
	 * @param string       $string
	 * @param string|array $needle string o array of strings => si es array, devuelve true si empieza por alguno de ellos, false si por ninguno
	 *
	 * @return bool
	 */
	static public function beginsWith($string, $needle) {
		if ($needle) {
			if (!is_array($needle)) {
				$needle = array($needle);
			}

			foreach ($needle as $n) {
				$s = self::substr($string, 0, self::strlen($n), false);
				if ($s === (string) $n) {
					return true;
				}
			}

			return false;
		}

		return true;
	}


	/**
	 * @static
	 *
	 * @param string       $haystack
	 * @param string|array $needle string o array of strings => si es array, devuelve true si termina por alguno de ellos, false si por ninguno
	 *
	 * @return bool
	 */
	static public function endsWith($haystack, $needle) {
		if ($needle) {
			if (!is_array($needle)) {
				$needle = array($needle);
			}
			foreach ($needle as $n) {
				$length = self::strlen($n);
				$start  = $length * -1; //negative
				$s      = self::substr($haystack, $start, $length);
				if ($s === (string) $n) {
					return true;
				}
			}

			return false;
		}

		return true;
	}

	static public function stripos($haystack, $needle, $offset = null) {
		return mb_stripos($haystack, $needle, $offset, 'UTF-8');
	}

	static public function strpos($haystack, $needle, $offset = null) {
		return mb_strpos($haystack, $needle, $offset, 'UTF-8');
	}

	static public function substr($string, $start, $length = null, $repair = true) {
		if ($length) {
			$str = mb_substr($string, $start, $length, 'UTF-8');
		} else {
			$str = mb_substr($string, $start, mb_strlen($string, 'UTF-8') + 1, 'UTF-8');
		}

		return $repair ? self::repairHTML($str) : $str;
	}


	/**
	 * returns mb_strlen
	 * @static
	 *
	 * @param string $str
	 *
	 * @return int
	 */
	static public function strlen($str) {
		return mb_strlen($str, 'utf-8');
	}

	static public $htmlEntitiesDefaultOptions = array('charset' => 'UTF-8', 'quote_style' => ENT_COMPAT, 'double_encode' => true);

	static public function htmlentities($string, $options = array()) {
		$ops = array_merge(self::$htmlEntitiesDefaultOptions, $options);

		return htmlentities($string, $ops['quote_style'], $ops['charset'], $ops['double_encode']);
	}

	/**
	 * replace chars with HTML entities and those with XML entities
	 *
	 * @static
	 *
	 * @param string $str
	 *
	 * @return string
	 * @uses html2xmlEntities()
	 */
	static public function xmlEntities($str) {
		return self::html2xmlEntities(self::htmlentities($str));
	}

	/**
	 * replaces HTML entities with XML entities
	 *
	 * @static
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	static public function html2xmlEntities($str) {
		$xml  = array('&#34;',
					  '&#38;',
					  '&#38;',
					  '&#60;',
					  '&#62;',
					  '&#160;',
					  '&#161;',
					  '&#162;',
					  '&#163;',
					  '&#164;',
					  '&#165;',
					  '&#166;',
					  '&#167;',
					  '&#168;',
					  '&#169;',
					  '&#170;',
					  '&#171;',
					  '&#172;',
					  '&#173;',
					  '&#174;',
					  '&#175;',
					  '&#176;',
					  '&#177;',
					  '&#178;',
					  '&#179;',
					  '&#180;',
					  '&#181;',
					  '&#182;',
					  '&#183;',
					  '&#184;',
					  '&#185;',
					  '&#186;',
					  '&#187;',
					  '&#188;',
					  '&#189;',
					  '&#190;',
					  '&#191;',
					  '&#192;',
					  '&#193;',
					  '&#194;',
					  '&#195;',
					  '&#196;',
					  '&#197;',
					  '&#198;',
					  '&#199;',
					  '&#200;',
					  '&#201;',
					  '&#202;',
					  '&#203;',
					  '&#204;',
					  '&#205;',
					  '&#206;',
					  '&#207;',
					  '&#208;',
					  '&#209;',
					  '&#210;',
					  '&#211;',
					  '&#212;',
					  '&#213;',
					  '&#214;',
					  '&#215;',
					  '&#216;',
					  '&#217;',
					  '&#218;',
					  '&#219;',
					  '&#220;',
					  '&#221;',
					  '&#222;',
					  '&#223;',
					  '&#224;',
					  '&#225;',
					  '&#226;',
					  '&#227;',
					  '&#228;',
					  '&#229;',
					  '&#230;',
					  '&#231;',
					  '&#232;',
					  '&#233;',
					  '&#234;',
					  '&#235;',
					  '&#236;',
					  '&#237;',
					  '&#238;',
					  '&#239;',
					  '&#240;',
					  '&#241;',
					  '&#242;',
					  '&#243;',
					  '&#244;',
					  '&#245;',
					  '&#246;',
					  '&#247;',
					  '&#248;',
					  '&#249;',
					  '&#250;',
					  '&#251;',
					  '&#252;',
					  '&#253;',
					  '&#254;',
					  '&#255;');
		$html = array('&quot;',
					  '&amp;',
					  '&amp;',
					  '&lt;',
					  '&gt;',
					  '&nbsp;',
					  '&iexcl;',
					  '&cent;',
					  '&pound;',
					  '&curren;',
					  '&yen;',
					  '&brvbar;',
					  '&sect;',
					  '&uml;',
					  '&copy;',
					  '&ordf;',
					  '&laquo;',
					  '&not;',
					  '&shy;',
					  '&reg;',
					  '&macr;',
					  '&deg;',
					  '&plusmn;',
					  '&sup2;',
					  '&sup3;',
					  '&acute;',
					  '&micro;',
					  '&para;',
					  '&middot;',
					  '&cedil;',
					  '&sup1;',
					  '&ordm;',
					  '&raquo;',
					  '&frac14;',
					  '&frac12;',
					  '&frac34;',
					  '&iquest;',
					  '&Agrave;',
					  '&Aacute;',
					  '&Acirc;',
					  '&Atilde;',
					  '&Auml;',
					  '&Aring;',
					  '&AElig;',
					  '&Ccedil;',
					  '&Egrave;',
					  '&Eacute;',
					  '&Ecirc;',
					  '&Euml;',
					  '&Igrave;',
					  '&Iacute;',
					  '&Icirc;',
					  '&Iuml;',
					  '&ETH;',
					  '&Ntilde;',
					  '&Ograve;',
					  '&Oacute;',
					  '&Ocirc;',
					  '&Otilde;',
					  '&Ouml;',
					  '&times;',
					  '&Oslash;',
					  '&Ugrave;',
					  '&Uacute;',
					  '&Ucirc;',
					  '&Uuml;',
					  '&Yacute;',
					  '&THORN;',
					  '&szlig;',
					  '&agrave;',
					  '&aacute;',
					  '&acirc;',
					  '&atilde;',
					  '&auml;',
					  '&aring;',
					  '&aelig;',
					  '&ccedil;',
					  '&egrave;',
					  '&eacute;',
					  '&ecirc;',
					  '&euml;',
					  '&igrave;',
					  '&iacute;',
					  '&icirc;',
					  '&iuml;',
					  '&eth;',
					  '&ntilde;',
					  '&ograve;',
					  '&oacute;',
					  '&ocirc;',
					  '&otilde;',
					  '&ouml;',
					  '&divide;',
					  '&oslash;',
					  '&ugrave;',
					  '&uacute;',
					  '&ucirc;',
					  '&uuml;',
					  '&yacute;',
					  '&thorn;',
					  '&yuml;');
		$str  = str_replace($html, $xml, $str);
		$str  = str_ireplace($html, $xml, $str);
		return $str;
	}

	/**
	 *
	 * @param string $str contains the complete raw name string
	 * @param array  $a_char is an array containing the characters we use as separators for capitalization. If you don't pass anything, there are three in there as default.
	 */
	static public function nameize($str, $a_char = array("'", "-", " ")) {

		$string = mb_convert_case(mb_convert_case($str, MB_CASE_LOWER, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
		$string = preg_replace_callback("/( [ a-zA-Z]{1}')([a-zA-Z0-9]{1})/s", create_function('$matches', 'return $matches[1].mb_convert_case($matches[2], MB_CASE_UPPER, "UTF-8");'), $string);
		return $string;
	}

	static public function normal($p) {
		$ts = array("/[À-Å]/",
					"/Æ/",
					"/Ç/",
					"/[È-Ë]/",
					"/[Ì-Ï]/",
					"/Ð/",
					"/Ñ/",
					"/[Ò-ÖØ]/",
					"/×/",
					"/[Ù-Ü]/",
					"/[Ý-ß]/",
					"/[à-å]/",
					"/æ/",
					"/ç/",
					"/[è-ë]/",
					"/[ì-ï]/",
					"/ð/",
					"/ñ/",
					"/[ò-öø]/",
					"/÷/",
					"/[ù-ü]/",
					"/[ý-ÿ]/");
		$tn = array("A", "AE", "C", "E", "I", "D", "N", "O", "X", "U", "Y", "a", "ae", "c", "e", "i", "d", "n", "o", "x", "u", "y");
		return preg_replace($ts, $tn, $p);
	}

	static public function ucfirst($string, $e = 'utf-8') {
		if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) {
			$string = mb_strtolower($string, $e);
			$upper  = mb_strtoupper($string, $e);
			preg_match('#(.)#us', $upper, $matches);
			$string = $matches[1] . mb_substr($string, 1, mb_strlen($string, $e), $e);
		} else {
			$string = ucfirst($string);
		}
		return $string;
	}

	static public function strtolower($str, $e = 'utf-8') {
		return function_exists('mb_strtolower') ? mb_strtolower((string) $str, $e) : strtolower((string) $str);
	}

	static public function strtoupper($str, $e = 'utf-8') {
		return function_exists('mb_strtoupper') ? mb_strtoupper((string) $str, $e) : strtoupper((string) $str);
	}

	static public function sentenceCase($string) {
		$sentences  = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$new_string = '';
		foreach ($sentences as $key => $sentence) {
			$new_string .= ($key & 1) == 0 ? self::ucfirst(self::strtolower(trim($sentence))) : $sentence . ' ';
		}
		return trim($new_string);
	}

	/**
	 * given a string (or an array of strings), it changes the <br/> to \n
	 *
	 * @param string|array $p
	 * @param string       $removeChar
	 *
	 * @return string|array
	 */
	static public function removeBR($p, $removeChar = "\n") {
		$returns = array("<br>", "<br />", "<br/>");
		$c       = str_ireplace($returns, $removeChar, $p);
		return $c;
	}


	static public function encryptDecrypt($Str_Message) {
		//Function : encrypt/decrypt a string message v.1.0  without a known key
		//Author   : Aitor Solozabal Merino (spain)
		//Email	: aitor-3@euskalnet.net
		//Date	 : 01-04-2005
		$Len_Str_Message       = strlen($Str_Message);
		$Str_Encrypted_Message = "";
		for ($Position = 0; $Position < $Len_Str_Message; $Position++) {
			// long code of the function to explain the algoritm
			//this function can be tailored by the programmer modifyng the formula
			//to calculate the key to use for every character in the string.
			$Key_To_Use = (($Len_Str_Message + $Position) + 1); // (+5 or *3 or ^2)
			//after that we need a module division because can´t be greater than 255
			$Key_To_Use                = (255 + $Key_To_Use) % 255;
			$Byte_To_Be_Encrypted      = substr($Str_Message, $Position, 1);
			$Ascii_Num_Byte_To_Encrypt = ord($Byte_To_Be_Encrypted);
			$Xored_Byte                = $Ascii_Num_Byte_To_Encrypt ^ $Key_To_Use; //xor operation
			$Encrypted_Byte            = chr($Xored_Byte);
			$Str_Encrypted_Message .= $Encrypted_Byte;

			//short code of  the function once explained
			//$str_encrypted_message .= chr((ord(substr($str_message, $position, 1))) ^ ((255+(($len_str_message+$position)+1)) % 255));
		}
		return $Str_Encrypted_Message;
	}

	static public function encode($ss, $ntime = null) {
		if (!$ntime) {
			$ntime = self::ENCODE_LENGTH;
		}
		for ($i = 0; $i < $ntime; $i++) {
			$ss = base64_encode($ss);
		}
		return $ss;
	}

	static public function decode($ss, $ntime = null) {
		if (!$ntime) {
			$ntime = self::ENCODE_LENGTH;
		}

		for ($i = 0; $i < $ntime; $i++) {
			$ss = base64_decode($ss);
		}
		return $ss;
	}

	const ENCODE_LENGTH = 5;

	static public function encode_encrypted($string) {
		return self::encode(self::encryptDecrypt($string), self::ENCODE_LENGTH);
	}

	static public function decode_encrypted($string) {
		return self::encryptDecrypt(self::decode($string, self::ENCODE_LENGTH));
	}

	const ALLOWED_TAGS_STANDARD_LIST = 'u,i,br,b,a,strong';

	static public function getAllowedTagsStandard() {
		return explode(',', self::ALLOWED_TAGS_STANDARD_LIST);
	}


	/**
	 * Return the first $amount chars of a string without splitting words unless $forced = true
	 *
	 * @param string  $string string to cut
	 * @param int     $amount characters to return
	 * @param boolean $includeLastWord include or not the last word
	 * @param boolean $forced force to return just 100 chars, splitting words
	 */
	static public function getChars($string, $amount, $includeLastWord = true, $forced = false) {
		if ($forced) {
			return self::substr($string, 0, $amount);
		} elseif ($includeLastWord) {
			if (strpos($string, ' ', $amount) !== false) {
				return self::substr($string, 0, strpos($string, ' ', $amount));
			} else {
				return $string;
			}

		} else {
			return self::substr($string, 0, strrpos($string, ' ', $amount));
		}
	}


	public static function htmlspecialchars($string, $quote_style = ENT_COMPAT, $charset = 'UTF-8', $double_encode = true) {
		return htmlspecialchars($string, $quote_style, $charset, $double_encode);
	}

	public static function removeSpacesDotsMinus($string) {
		return preg_replace('/[\s\.\-]/', '', $string);
	}


	public static function contains($string, $substr, $case_sensitive = true) {
		if ($case_sensitive) {
			return (strpos($string, $substr) !== FALSE);
		} else {
			return (stripos($string, $substr) !== FALSE);
		}
	}

	public static function firstToken($string, $separator = ',') {
		return substr($string, 0, strpos($string, $separator));
	}

	public static function isUtf8($string) {
		return (md5(iconv('utf-8', 'utf-8', $string)) == md5($string));
	}

	public static function lastToken($string, $token = ',') {
		return substr($string, strrpos($string, $token) + 1);
	}


	public static function hex2rbg($hex) {
		$hex = str_replace('#', '', $hex);
		$len = strlen($hex);
		$r   = substr($hex, 0, $len / 3);
		$g   = substr($hex, $len / 3, ($len / 3) * 2);
		$b   = substr($hex, ($len / 3) * 2);
		if ($len == 3) {
			$r .= $r;
			$g .= $g;
			$b .= $b;
		}
		return array('r' => hexdec($r), 'g' => hexdec($g), 'b' => hexdec($b));
	}


	public static function similarity($string1, $string2) {
		$nonSense = array(',', '.', '_', '#', '¿', '?', '¡', '!', '"');
		$p        = null;
		similar_text(str_replace($nonSense, '', self::strtolower($string1)), str_replace($nonSense, '', self::strtolower($string2)), $p);
		return $p;
	}


	public static $repairHTMLTidyOptions = array('wrap' => 0,
												 'doctype' => 'omit',
												 'tidy-mark' => '0',
												 'wrap-sections' => '0',
												 'char-encoding' => 'utf8',
												 'input-encoding' => 'utf8',
												 'show-body-only' => 1);

	public static function repairHTML($string, $encoding = 'utf8') {
		$tidy = new tidy();
		return $tidy->repairString($string, self::$repairHTMLTidyOptions, $encoding);
	}

	public static function stringContainsUrl($x) {
		return (preg_match('#(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?#i', $x)) > 0;
	}


}