<?php

if (!function_exists('getDomain')) {
    function getDomain()
    {
        $domain = $_SERVER['SERVER_NAME'];
        if ($domain == 'pimo82.local') {
            $domain = 'zaco-soft.com';
        }
        return $domain;
    }
}

if (!function_exists('setEnv')) {
    function setEnv($data)
    {
        $path = app()->environmentFilePath();
        $env = file_get_contents($path);

        foreach ($data as $key => $value) {
            $old_value = env($key);

            if (!str_contains($env, $key . '=')) {
                $env .= sprintf("\n%s=%s", $key, $value);
            } else if ($old_value) {
                $env = str_replace(sprintf('%s=%s', $key, $old_value), sprintf('%s=%s', $key, $value), $env);
            } else {
                $env = str_replace(sprintf('%s=', $key), sprintf('%s=%s', $key, $value), $env);
            }
        }

        file_put_contents($path, $env);
    }
}

if (!function_exists('setConfig')) {
    function setConfig($config_key, $data)
    {
        formatSetDataConfig($config_key, $data);
        $fp = fopen(base_path() . sprintf('/config/%s.php', $config_key), 'w');
        fwrite($fp, '<?php return ' . var_export(config($config_key), true) . ';');
        fclose($fp);
    }

    function formatSetDataConfig($config_key, $value)
    {
        $data = config($config_key);
        if (is_null($data)) {
            config([$config_key => $value]);
        } else {
            if (is_array($value)) {
                foreach ($value as $key => $item) {
                    $new_config_key = sprintf('%s.%s', $config_key, $key);
                    formatSetDataConfig($new_config_key, $item);
                }
            }
        }
    }
}

if (!function_exists('sendMail')) {
    function sendMail($data)
    {
        \Mail::send([], [], function ($message) use ($data) {
            $message->from($data['from']);
            $message->to($data['to']);
            $message->subject($data['subject']);
            $message->setBody($data['content'], 'text/html');
        });
    }
}

if (!function_exists('emptyCheck')) {
    function emptyCheck($string)
    {
        if (empty($string)) {
            return true;
        }

        if (empty($string) && $string !== '0') {
            return true;
        }
        if (!is_string($string)) {
            return false;
        } else if (is_array($string) && sizeof($string) == 0) {
            return false;
        }

        $data = trim($string);
        for ($i = 0; $i < strlen($data); $i++) {
            $c = substr($data, $i, 1);
            if (($c != "\r") && ($c != " ") && ($c != "\n") && ($c != "\t")) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('isEmpty')) {
    function isEmpty($object)
    {
        if (empty($object)) {
            return true;
        }

        $type = gettype($object);
        if ($type != 'string') {
            return false;
        }
        $string = trim($object);

        for ($i = 0; $i < strlen($string); $i++) {
            $c = substr($string, $i, 1);
            if (($c != "\r") && ($c != " ") && ($c != "\n") && ($c != "\t")) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('convertUnderscore')) {
    function convertUnderscore($str, $separator = "_")
    {
        if (isEmpty($str)) {
            return $str;
        }
        $str = lcfirst($str);
        $str = preg_replace("/[A-Z]/", $separator . "$0", $str);
        return strtolower($str);
    }
}
