<?php


class EtuDev_Util_Phone
{

    static public function isSpanishStrictPhoneNumber($value, $accept_intel_phone = false, $accept_mobile = true)
    {
        if ($accept_mobile) {
            $pattern        = '/^[5,6,7,8,9]{1}\d{8}$/';
            $pattern_prefix = '/^\+34[5,6,7,8,9]{1}\d{8}$/';
        } else {
            $pattern        = '/^[8,9]{1}\d{8}$/';
            $pattern_prefix = '/^\+34[8,9]{1}\d{8}$/';
        }

        if (preg_match($pattern, $value) || preg_match($pattern_prefix, $value) || $value == '') {
            if (!$accept_intel_phone && preg_match('/^[9,8]0\d{7}$/', $value)) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    static public function formatSpanishPhone($lPhone)
    {

        // 93 345 31 45
        // 983 47 18 21

        $prefix = '';
        $lPhone = trim($lPhone);
        if ($lPhone && self::isSpanishStrictPhoneNumber($lPhone, true, true)) {
            if (EtuDev_Util_String::beginsWith($lPhone, '+34')) {
                $lPhone = substr($lPhone, 3);
                $prefix = '(+34) ';
            }

            if (EtuDev_Util_String::strlen($lPhone) == 9) {
                if (EtuDev_Util_String::beginsWith($lPhone, '93') || EtuDev_Util_String::beginsWith($lPhone, '91')) {
                    return $prefix . substr($lPhone, 0, 2) . ' ' . substr($lPhone, 2, 3) . ' ' . substr($lPhone, 5, 2) . ' ' . substr(
                        $lPhone,
                        7
                    );
                } else {
                    return $prefix . substr($lPhone, 0, 3) . ' ' . substr($lPhone, 3, 2) . ' ' . substr($lPhone, 5, 2) . ' ' . substr(
                        $lPhone,
                        7
                    );
                }
            }

        }

        return $lPhone;
    }
}