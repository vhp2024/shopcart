<?php
namespace ZacoSoft\ZacoBase\Libraries\Common;

use Cache;
use App\Models\DefaultInput;

class FormData
{
    protected $package;
    protected $group;
    protected $code;
    protected $settings;
    protected $rules;
    protected $names;
    protected $errorMessages;

    public function __construct(string $package = '', string $group = '', string $code = '')
    {
        $this->package = $package;
        $this->group = $group;
        $this->code = $code;
        $this->settings = DefaultInput::where('i_package', $package)
            ->where('i_group', $group)
            ->where('i_code', $code)
            ->get();
        $this->initData();
    }

    public function initialize($config = array())
    {
        if (!empty($config)) {
            foreach ($config as $key => $value) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    public function getValue($valueName)
    {
        if(isset($this->{$valueName}))
            return $this->{$valueName};
        return false;
    }

    public function initData()
    {
        $rules = $names = [];
        foreach ($this->settings as $setting) {
            if ($setting['i_rules'] != '') {
                $rules[$setting['i_name']] = $setting['i_rules'];
                $names[$setting['i_name']] = !emptyCheck($setting['i_label']) ? $setting['i_label'] : $setting['i_name'];
            }
        }
        $this->rules = $rules;
        $this->names = $names;
        $this->errorMessages = $this->formatErrorValidator();
    }

    public function formatErrorValidator()
    {
        $results = [];
        foreach ($this->rules as $name => $rule) {
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
                        $arrLabels['name'] = __($this->names[$name]);
                        break;
                }
                $results[$key] = __($commonKey, $arrLabels);
            }
        }
        return $results;
    }

    public function getFormRender()
    {
        $menus = $this->getDefaultSetting($this->package, $this->group);
        $form = [];
        if(isset($menus[$this->code])) {
            $form = $menus[$this->code];
        }
        else {
            $form = $menus;
            $menus = [];
        }

        $inputs = [];
        foreach ($this->settings as $setting) {
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

            if (isset($form['type']) && $form['type'] == 'env') {
                $value = env($setting['i_name']);
            }

            $data['value'] = $value;
            $inputs[] = $data;
        }

        $inputs[] = [
            'name' => 'code',
            'type' => 'hidden',
            'label' => '',
            'value' => $this->code,
        ];

        return [$form, $inputs, $menus];
    }

    public function getDefaultSetting($package, $group, $isCache = true)
    {
        $cacheKey = sprintf('cache_input_%s_%s', $package, $group);
        $cacheE = Cache::get($cacheKey);
        if (!$cacheE || !$isCache) {
            $setting = DefaultInput::where('i_package', $package)
                ->where('i_group', $group)
                ->where('i_code', 'default')
                ->first();

            if(!$setting) {
                throw new Exception("Form does not have default setting");
            }

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
             *        multi array: normal
             */
            if (count($arr) == 1) {
                $key = array_key_first($arr);
                $arr = $arr[$key];
            }

            Cache::forever($cacheKey, $arr);
            $cacheE = $arr;
        }

        return $cacheE;
    }
}
