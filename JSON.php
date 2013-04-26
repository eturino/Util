<?php

class EtuDev_Util_JSON
{

    /**
     * use single quote (') in the attribute
     * ie: <input data-myjson='$ENCODED' />
     *
     * @static
     *
     * @param mixed $text
     *
     * @return null|string
     */
    static public function encodetoHTMLAttribute($text)
    {
        try {
            return static::jsonenc($text);
        } catch (Exception $ex) {
            return null;
        }
    }

    static protected function jsonenc($text)
    {
        if (defined('JSON_UNESCAPED_UNICODE')) {
            return json_encode($text, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode($text, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        }
    }

    /**
     * use single quote (') in the attribute
     * ie: <input data-myjson='$ENCODED' />
     *
     * @static
     *
     * @param $text
     *
     * @return null|string
     */
    static public function encodetoHTMLAttributeString($text)
    {
        try {
            return static::jsonenc((string) $text);
        } catch (Exception $ex) {
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
    static public function encode($text)
    {
        try {
            return json_encode($text);
        } catch (Exception $ex) {
            return null;
        }
    }
}