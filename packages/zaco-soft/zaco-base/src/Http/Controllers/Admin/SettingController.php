<?php
namespace ZacoSoft\ZacoBase\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use ZacoSoft\ZacoBase\Http\Controllers\BaseController as Controller;
use Validator;

class SettingController extends Controller
{
    protected $i_group = 'settings';
    /**
     * Display the settings page
     *
     * @return \Illuminate\View\View
     */
    public function index($code = 'common')
    {
        list($form, $inputs, $menus) = formatInput($this->i_package, $this->i_group, $code);
        $form['route'] = 'Setting::post_save';
        return view('zaco-base::admin.settings.index', compact('form', 'inputs', 'menus'));
    }

    public function post_save(Request $request)
    {
        $params = $request->all();
        $settings = getSettings($this->i_group, $params['code']);
        $formRules = [];
        foreach ($settings as $setting) {
            $formRules[$setting->i_name] = $setting->i_rules;
        }

        $validator = Validator::make($params, $formRules);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {
                $currentSetting = cacheInputRender($this->i_group, $params['code']);
                if ($currentSetting['type'] != 'env') {
                    DB::beginTransaction();
                    foreach ($settings as $setting) {
                        $setting->i_value = $params[$setting->i_name];
                        $setting->save();
                    }
                    DB::commit();
                } else {
                    $data = [];
                    foreach ($settings as $setting) {
                        $update[$setting->i_name] = $params[$setting->i_name];
                    }
                    setEnv($update);
                }
                return redirect()->back()
                    ->with('message', __('common.success'));
            } catch (\Exception$ex) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }
    }
}
