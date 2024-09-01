<?php
namespace ZacoSoft\ZacoBase\Http\Controllers;

use App\Models\User;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use ZacoSoft\ZacoBase\Http\Controllers\BaseController as Controller;
use PragmaRX\Google2FA\Google2FA;
use Validator;

class ProfileController extends Controller
{
    protected $i_group = 'profiles';

    protected $request;
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    public function index($code = 'basic')
    {
        $params = $this->request->all();
        list($form, $inputs, $menus) = formatInput($this->i_package, $this->i_group, $code);
        $others = [];

        switch ($code) {
            case 'password':
                $route = 'Profile::post_save_password';
                break;

            case 'security':
                if (!Auth::user()->two_factor_secret) {
                    if (!session('google2fa')) {
                        $google2fa = new Google2FA();
                        $secretKey = $google2fa->generateSecretKey();
                        $qrCodeUrl = $google2fa->getQRCodeUrl(
                            'ZacoSoft',
                            Auth::user()->email,
                            $secretKey
                        );

                        session(['google2fa' => [
                            'secret_key' => $secretKey,
                            'qr_code_url' => $qrCodeUrl,
                        ]]);
                    }
                    $others['google2fa'] = session('google2fa');
                    $fields['inputs'][] = [
                        'name' => 'google2fa_secret_key',
                        'type' => 'hidden',
                        'value' => $others['google2fa']['seNcret_key'],
                    ];
                }

                if ($params['isApi']) {
                    return $this->response
                        ->setData($others)
                        ->setMessage(__('common.success'));
                }

                $form['route'] = 'Profile::postSaveSecurity';
                return view('zaco-base::user.profiles.security', compact('form', 'inputs', 'menus', 'code', 'others'));
                break;

            default:
                $form['route'] = 'Profile::post_save';
                $user = Auth::user();
                foreach ($inputs as $key => &$field) {
                    if ($field['type'] != 'hidden') {
                        $field['value'] = $user[$field['name']];
                    }
                }

                break;
        }

        return view('zaco-base::user.profiles.index', compact('form', 'inputs', 'menus', 'code'));
    }

    public function post_save(Request $request)
    {

    }

    public function post_save_password(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'current_password' => 'required|between:6,255',
            'new_password' => 'required|between:6,255',
            'confirm_new_password' => 'required|same:new_password',
        ], [
            'current_password.required' => __('common.required', ['name' => __('auth.current_password')]),
            'current_password.between' => __('common.between', ['name' => __('auth.current_password'), 'min' => 6, 'max' => 255]),
            'new_password.required' => __('common.required', ['name' => __('auth.new_password')]),
            'new_password.between' => __('common.between', ['name' => __('auth.new_password'), 'min' => 6, 'max' => 255]),
            'confirmation_password.required' => __('common.required', ['name' => __('auth.confirmation_password')]),
            'confirmation_password.same' => __('auth.confirmation_password_same'),
        ]);
        die("------2022-12-17 12:14:45------");
        $isApi = isApi();
        if ($validator->fails()) {
            if ($isApi) {
                return $this->errorResponse(__('auth.user_valid_fail'), $validator);
            }
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        } else {
            if (!Hash::check($request->current_password, \Auth::user()->password)) {
                if ($isApi) {
                    return $this->errorResponse(__('auth.user_valid_fail'), $validator);
                }
                return redirect()
                    ->back()
                    ->withErrors(['current_password', __('auth.user_valid_fail')])
                    ->withInput();
            }

            try {
                DB::beginTransaction();
                User::find(auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);
                DB::commit();
                return redirect()
                    ->back()
                    ->with('message', __('common.success'));
            } catch (\Exception$ex) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }
    }
}
