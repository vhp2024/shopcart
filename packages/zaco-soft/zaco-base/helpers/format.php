<?php

use App\Models\DefaultInput as DefaultInput;

if (!function_exists('getInputs')) {
    function getInputs($package, $group, $code)
    {
        $settings = DefaultInput::where('i_code', $code)
            ->where('i_group', $group)
            ->where('i_package', $package)
            ->get();

        return $settings;
    }
}

if (!function_exists('formatGetInputs')) {
    function formatGetInputs($package, $group, $code, $groupRules = [])
    {
        $settings = DefaultInput::where('s_package', $package)
            ->where('i_code', $code)
            ->where('i_group', $group);

        if (count($groupRules)) {
            $settings->whereIn('s_group_rules', $groupRules);
        }

        return $settings->get();
    }
}

if (!function_exists('formatGetValidator')) {
    function formatGetValidator($package, $group, $code, $groupRules = [])
    {
        $settings = formatGetInputs($package, $group, $code, $groupRules);
        $rules = $names = [];
        foreach ($settings as $setting) {
            if ($setting['i_rules'] != '') {
                $rules[$setting['i_name']] = $setting['i_rules'];
                $names[$setting['i_name']] = $setting['i_label'];
            }
        }

        $errorMessages = formatErrorValidator($rules, $names);
        return [$rules, $errorMessages];
    }
}

if (!function_exists('formatErrorValidator')) {
    function formatErrorValidator($rules, $names)
    {
        $results = [];
        foreach ($rules as $name => $rule) {
            $arrRules = explode(',', $rule);
            foreach ($arrRules as $validate) {
                $arrLabels = [];
                $key = sprintf('%s.%s', $name, $validate);
                $commonKey = sprintf('common.%s', $validate);
                switch ($validate) {
                    case 'value':
                        # code...
                        break;

                    default:
                        $arrLabels['name'] = __($names[$name]);
                        break;
                }
                $results[$key] = __($commonKey, $arrLabels);
            }
        }
        return $results;
    }
}

if (!function_exists('formatInput')) {
    function formatInput($package, $group, $code)
    {
        $info = cacheInputRender($package, $group, $code);
        $settings = getInputs($package, $group, $code);
        $inputs = [];
        foreach ($settings as $setting) {
            $data = [
                'name' => $setting['i_name'],
                'type' => $setting['i_type'],
                'label' => $setting['i_label'],
            ];
            $value = $setting['i_value'];
            if ($setting['i_type'] == 'boolean') {
                $extra = json_decode($setting['i_value'], true);
                if (isset($extra['value'])) {
                    $value = $extra['value'];
                }

                $data['true_value'] = $extra['true_value'];
                $data['false_value'] = $extra['false_value'];
            }

            if (isset($info['type']) && $info['type'] == 'env') {
                $value = env($setting['i_name']);
            }

            $data['value'] = $value;
            $inputs[] = $data;
        }

        $inputs[] = [
            'name' => 'code',
            'type' => 'hidden',
            'label' => '',
            'value' => $code,
        ];

        $menus = cacheInputRender($package, $group);
        return [$info, $inputs, $menus, $settings];
    }
}

if (!function_exists('formatInputData')) {
    function formatInputData($inputs, $settings, $initData = [])
    {
        $initialValues = [];
        foreach ($settings as $setting) {
            $data = [
                'name' => $setting['i_name'],
                'type' => $setting['i_type'],
                'label' => $setting['i_label'],
            ];
            $value = $setting['i_value'];
            if ($setting['i_type'] == 'boolean') {
                $extra = json_decode($setting['i_value'], true);
                if (isset($extra['value'])) {
                    $value = $extra['value'];
                }

                $data['true_value'] = $extra['true_value'];
                $data['false_value'] = $extra['false_value'];
            }

            if (isset($inputs['type']) && $inputs['type'] == 'env') {
                $value = env($setting['i_name']);
            }

            $data['value'] = $value;
            if (isset($initData[$setting['i_name']])) {
                $init = $initData[$setting['i_name']];
                foreach ($init as $key => $value) {
                    $data[$key] = $value;
                }
            }
            $initialValues[$setting['i_name']] = $data['value'];
            $data['rules'] = $setting['i_rules'];
            $inputs[] = $data;
        }

        return [$inputs, $initialValues];
    }
}

if (!function_exists('cacheInputRender')) {
    function cacheInputRender($package, $group, $code = '')
    {
        $cacheKey = sprintf('cache_input_%s_%s', $package, $group);
        $cacheE = Cache::get($cacheKey);
        if (!$cacheE) {
            $setting = DB::table('default_inputs')
                ->where('i_package', $package)
                ->where('i_group', $group)
                ->where('i_code', 'default')
                ->first();

            $menus = json_decode($setting->i_value, true);
            $arr = [];

            foreach ($menus as $menu) {
                if (isset($menu['code'])) {
                    $m_key = $menu['code'];
                } else {
                    list($m_group, $m_key) = explode('.', $menu['title']);
                }
                $menu['code'] = $m_key;
                $arr[$m_key] = $menu;
            }

            /*
             * 2022-06-26
             * return single array: if data only has 1 item
             *        multi array: if data only has more 1 item
             */
            if (count($arr) == 1) {
                $key = array_key_first($arr);
                $arr = $arr[$key];
            }

            Cache::forever($cacheKey, $arr);
            $cacheE = $arr;
        }

        if ($code) {
            return $cacheE[$code];
        }

        return $cacheE;
    }
}

if (!function_exists('getCacheData')) {
    function getCacheData($cacheKey)
    {
        return Cache::get($cacheKey);
    }
}

if (!function_exists('setCacheData')) {
    function setCacheData($cacheKey, $data, $seconds)
    {
        if ($seconds) {
            return Cache::put($cacheKey, $data, $seconds);
        }
        Cache::forever($cacheKey, $data);
    }
}

if (!function_exists('deleteCacheData')) {
    function deleteCacheData($cacheKey)
    {
        Cache::forget($cacheKey);
    }
}

if (!function_exists('convertToFieldDB')) {
    function convertToFieldDB($str)
    {
        die("------2023-05-01 08:38:13------");
    }
}

if (!function_exists('convertToCamelCase')) {
    function convertCamelCase($str)
    {
        die("------2023-05-01 08:38:13------");
    }
}
