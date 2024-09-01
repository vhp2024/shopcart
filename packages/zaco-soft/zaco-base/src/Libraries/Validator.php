<?php

namespace ZacoSoft\ZacoBase\Libraries;

use Illuminate\Support\Facades\Validator as laraValidator;
use PragmaRX\Google2FA\Google2FA;

class Validator
{
    protected static $params = [];

    protected static $validator;

    protected static $isTwoFactor = false;
    /**
     * @contructor
     */
    public function __construct($params = [])
    {
        static::$params = $params;
    }

    public static function checkInputs($formRules = [], $translate = [])
    {
        static::$validator = laraValidator::make(static::$params, $formRules, $translate);
    }

    public static function checkDBInputs($package, $group, $code)
    {
        $settings = getInputs($package, $group, $code);
        $formRules = [];
        $isTwoFactor = false;
        foreach ($settings as $setting) {
            if ($setting->i_name == 'two_factor_code') {
                static::$isTwoFactor = true;
            }

            $formRules[$setting->i_name] = $setting->i_rules;
        }
        static::$validator = laraValidator::make(static::$params, $formRules);
    }

    public static function isFail()
    {
        static::$validator->fails();
        return static::$validator->fails() && static::verify2FA();
    }

    public static function verify2FA()
    {
        if (static::$isTwoFactor) {
            $google2fa = new Google2FA();
            $secretKey = \Auth::user()->two_factor_secret;
            return !$google2fa->verifyKey(decryptServerLayer1($secretKey), static::$params['two_factor_code'] ?? '', 2);
        }
        return true;
    }

    public static function getMessage()
    {
        return static::$validator->messages()->get('*');
    }
}
