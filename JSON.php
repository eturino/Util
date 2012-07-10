<?php

class EtuDev_Util_JSON {

	/**
	 * @static
	 *
	 * @param $text
	 *
	 * @return null|string
	 */
	static public function encode_to_attribute($text) {
		try{
			return json_encode((string) $text, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
		}catch (Exception $ex){
			return null;
		}
	}

	/**
	 * @static
	 *
	 * @param $text
	 *
	 * @return null|string
	 */
	static public function encode($text) {
		try{
			return json_encode((string) $text);
		}catch (Exception $ex){
			return null;
		}
	}
}