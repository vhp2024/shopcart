<?php
namespace ZacoSoft\ZacoBase\Libraries\Default;

use App\Models\DefaultInput;

class Input
{
    protected $settings;
    protected $rules;
    protected $names;
    protected $errorMessages;

    public function __construct(string $package = '', string $category = '', string $group = '')
    {
        $this->settings = DefaultInput::where('i_package', $package)
            ->where('i_category', $category)
            ->where('i_group', $group)
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
}
