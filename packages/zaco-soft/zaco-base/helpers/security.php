<?php
use Illuminate\Encryption\Encrypter;

if (!function_exists('getKeyEncrypt')) {
    function getKeyEncrypt()
    {
        return [
            'SALT' => 'salt-pimo',
            'ITERATIONS' => 1293,
            'IV' => 'zaco-soft20232031',
        ];
    }
}

if (!function_exists('getSoftwareCode')) {
    function getSoftwareCode()
    {
        return '641e6c38-c282-4b5e-8a52-f6d519f75f5e';
    }
}

if (!function_exists('getPurchaseCode')) {
    function getPurchaseCode()
    {
        return '829c0588-8f92-408f-af2d-e651e6f01cf0';
    }
}

if (!function_exists('generateSoftwareKey')) {
    function generateSoftwareKey()
    {
        return substr(sprintf('%s_%s_%s_zaco-soft20232031', substr(getPurchaseCode(), 0, 10), substr(getSoftwareCode(), 0, 10), getDomain()), 0, 32);
    }
}

if (!function_exists('encryptLayer1')) {
    function encryptLayer1($string, $key = 'ZacoSoft')
    {
        $info = getKeyEncrypt();
        $key = \hash_pbkdf2("sha256", $key, $info['SALT'], $info['ITERATIONS'], 64);
        $encryptedData = \openssl_encrypt($string, 'AES-256-CBC', \hex2bin($key), OPENSSL_RAW_DATA, $info['IV']);
        return \base64_encode($encryptedData);
    }
}

if (!function_exists('decryptLayer1')) {
    function decryptLayer1($string, $key = 'ZacoSoft')
    {
        $info = getKeyEncrypt();
        $encryptedText = \base64_decode($string);
        $key = \hash_pbkdf2("sha256", $key, $info['SALT'], $info['ITERATIONS'], 64);
        $decryptedText = \openssl_decrypt($encryptedText, 'AES-256-CBC', \hex2bin($key), OPENSSL_RAW_DATA, $info['IV']);
        return $decryptedText;
    }
}

if (!function_exists('encryptLayer2')) {
    function encryptLayer2($value, $key = 'ZacoSoft')
    {
        $passphrase = encryptLayer1($key);
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $passphrase . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
        $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
        return json_encode($data);
    }
}

if (!function_exists('decryptLayer2')) {
    function decryptLayer2($value, $key = 'ZacoSoft')
    {
        $jsonData = json_decode($value, true);
        $salt = hex2bin($jsonData["s"]);
        $ct = base64_decode($jsonData["ct"]);
        $iv = hex2bin($jsonData["iv"]);
        $concatPassphrase = encryptLayer1($key) . $salt;
        $md5 = array();
        $md5[0] = md5($concatPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1] . $concatPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return json_decode($data, true);
    }
}

if (!function_exists('encryptLevel')) {
    function encryptLevel($level = 1, $string, $key = 'ZacoSoft')
    {
        if ($level == 3) {
            $data = encryptLayer2($string, $key);
            return encryptLayer1($data, $key);
        }

        if ($level == 2) {
            return encryptLayer2($string, $key);
        }

        return encryptLayer1($string, $key);
    }
}

if (!function_exists('decryptLevel')) {
    function decryptLevel($level = 1, $string, $key = 'ZacoSoft')
    {
        if ($level == 3) {
            $data = decryptLayer1($string, $key);
            return decryptLayer2($data, $key);
        }

        if ($level == 2) {
            return decryptLayer2($string, $key);
        }

        return decryptLayer1($string, $key);
    }
}

if (!function_exists('getKeyEncryptServer')) {
    function getKeyEncryptServer()
    {
        $full_key = sprintf('%s_%s_%s', getDomain(), getSoftwareCode(), 'b5cf93c6-6f94-4282-98d3-9f894766a4cd');
        $full_key = substr($full_key, 0, 32);
        $info = getKeyEncrypt();
        $info['full_key'] = $full_key;
        return $info;
    }
}

if (!function_exists('encryptServerLayer1')) {

    function encryptServerLayer1($string, $key = 'ZacoSoft')
    {
        $result = '';
        for ($i = 0, $k = strlen($string); $i < $k; $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }
        return base64_encode($result);
    }
}

if (!function_exists('decryptServerLayer1')) {
    function decryptServerLayer1($string, $key = 'ZacoSoft')
    {
        $result = '';
        $string = base64_decode($string);
        for ($i = 0, $k = strlen($string); $i < $k; $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }
        return $result;
    }
}

if (!function_exists('encryptServerLayer2')) {
    function encryptServerLayer2($string, $key = '')
    {
        $keyEncryptServer = getKeyEncryptServer();
        $keyEncryptServer = $keyEncryptServer['full_key'];
        $key = substr($key . $keyEncryptServer, 0, 32);
        $encrypter = new Encrypter($key, "AES-256-CBC");
        return $encrypter->encryptString($string);
    }
}

if (!function_exists('decryptServerLayer2')) {
    function decryptServerLayer2($string, $key = '')
    {
        try {
            $keyEncryptServer = getKeyEncryptServer();
            $keyEncryptServer = $keyEncryptServer['full_key'];
            $key = substr($key . $keyEncryptServer, 0, 32);
            $encrypter = new Encrypter($key, "AES-256-CBC");
            return $encrypter->decryptString($string);
        } catch (\Throwable$th) {
            throw new \Exception('error_decrypt_fail');
        }
    }
}

if (!function_exists('encryptServerLevel')) {
    function encryptServerLevel($level = 1, $string, $key = 'ZacoSoft')
    {
        if ($level == 3) {
            $data = encryptServerLayer1($string, $key);
            return encryptServerLayer2($data, $key);
        }

        if ($level == 2) {
            return encryptServerLayer2($string, $key);
        }

        return encryptServerLayer1($string, $key);
    }
}

if (!function_exists('decryptServerLevel')) {
    function decryptServerLevel($level = 1, $string, $key = 'ZacoSoft')
    {
        if ($level == 3) {
            $data = decryptServerLayer2($string, $key);
            return decryptServerLayer1($data, $key);
        }

        if ($level == 2) {
            return decryptServerLayer2($string, $key);
        }

        return decryptServerLayer1($string, $key);
    }
}
