<?php

namespace ZacoSoft\ZacoBase\Http\Controllers;

use App\Mail\NotifyMail;
use App\Models\PasswordReset;
use App\Models\User as User;
use Auth;
use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Http\Request;
use Mail;
use ZacoSoft\ZacoBase\Http\Controllers\BaseController as Controller;
use ZacoSoft\ZacoBase\Libraries\Validator;
use PragmaRX\Google2FA\Google2FA;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    protected $request;
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    public function postLogin()
    {
        $params = $this->request->all();
        $validator = new Validator($params);

        $validator::checkInputs([
            'username_email' => 'required',
            'password' => 'required',
        ], [
            'username_email.required' => __('auth.username_or_email_required'),
            'password.required' => __('auth.password_required'),
        ]);

        if ($validator::isFail()) {
            return $this->response
                ->setError(true)
                ->setCode(401)
                ->setErrorMessages($validator::getMessage());
        } else {
            try {
                $username_email = $params['username_email'];
                $password = $params['password'];

                // if (emptyCheck(env('RAW_DATA'))) {
                //     $key = generateSoftwareKey();
                //     $username_email = decryptLevel(1, $username_email, $key);
                //     $password = decryptLevel(3, $password, $key);
                // }

                $user = User::where('email', '=', $username_email)
                    ->orWhere('username', '=', $username_email)->first();

                if (!$user || !Hash::check($password, $user->password)) {
                    return $this->response
                        ->setError(true)
                        ->setErrorMessages([
                            'username_email' => __('auth.user_valid_fail'),
                        ]);
                }

                if ($user->status != 'ACTIVE') {
                    return $this->response
                        ->setError(true)
                        ->setErrorMessages([
                            'username_email' => 'Your account is not active',
                        ]);
                }

                if (!isEmpty($user->two_factor_secret)) {
                    if (!isset($params['otp'])) {
                        return $this->response
                            ->setError(true)
                            ->setData([
                                'otp' => true,
                            ]);
                    }
                    $google2fa = new Google2FA();
                    $valid = $google2fa->verifyKey(decryptLayer1(decryptServerLayer2($user->two_factor_secret)), $params['otp'], 2);
                    if (!$valid) {
                        return $this->response
                            ->setError(true)
                            ->setErrorMessages([
                                'otp' => __('auth.otp_fail'),
                            ]);
                    }
                }
                if($this->response->isResponseApi()) {
                    $token = $user->createToken('auth');
                    return $this->response
                        ->setData([
                            'accessToken' => $token->plainTextToken,
                            'userData' => [
                                'email' => $user->email,
                                'fullName' => $user->full_name,
                                'id' => $user->id,
                                'role' => "admin",
                                'username' => $user->username,
                                'is_otp' => is_null($user->two_factor_secret),
                            ],
                        ])
                        ->setMessage(__('common.success'));
                }
                Auth::login($user);

                $nextUrl = '/';
                if(isset($params['last-url'])) {
                    $nextUrl = base64_decode($params['last-url']);
                }

                return $this->response
                    ->setNextUrl($nextUrl)
                    ->setMessage(__('common.success'));
            } catch (\Exception$e) {
                return $this->response
                    ->setError(true)
                    ->setErrorMessages($e->getMessage());
            }
        }

    }

    public function postRegister()
    {
        $params = $this->request->all();
        if (!emptyCheck(env('RAW_DATA'))) {
            $key = generateSoftwareKey();
            $params['username'] = decryptLevel(1, $params['username'], $key);
            $params['email'] = decryptLevel(1, $params['email'], $key);
            $params['password'] = decryptLevel(3, $params['password'], $key);
            $params['confirmation_password'] = decryptLevel(3, $params['confirmation_password'], $key);
        }
        $validator = new Validator($params);
        $validator::checkInputs([
            'name' => 'required|between:5,255',
            'username' => 'required|between:5,255|unique:users,username|min:3',
            'email' => 'required|between:5,255|unique:users,email|min:10',
            'password' => 'required|between:6,255',
            'confirmation_password' => 'required|same:password',
        ], [
            'name.required' => __('auth.name_required'),
            'name.max' => __('auth.name_max', ['max' => 255]),
            'username.required' => __('auth.username_required'),
            'username.min' => __('auth.username_min', ['max' => 3]),
            'username.max' => __('auth.username_max', ['max' => 255]),
            'username.unique' => __('auth.username_unique'),
            'email.required' => __('auth.email_required'),
            'email.min' => __('auth.email_min', ['max' => 10]),
            'email.max' => __('auth.email_max', ['max' => 255]),
            'email.unique' => __('auth.email_unique'),
            'password.required' => __('auth.password_required'),
            'password.between' => __('auth.password_between', ['min' => 6, 'max' => 255]),
            'confirmation_password.required' => __('auth.confirmation_password_required'),
            'confirmation_password.same' => __('auth.confirmation_password_same'),
        ]);

        if ($validator::isFail()) {
            return $this->response
                ->setError(true)
                ->setErrorMessages($validator::getMessage());
        } else {
            try {
                DB::beginTransaction();
                $data = [
                    'full_name' => $params['name'],
                    'username' => $params['username'],
                    'email' => $params['email'],
                    'password' => Hash::make($params['password']),
                ];

                // if (isset($params['referral_code']) && !isEmpty($params['referral_code'])) {
                //     User::where('username', $params['referral_code'])->orWhere('id', $params['referral_code'])->first();
                //     if ($presenter) {
                //         $data['presenter_id'] = $presenter->id;
                //     }
                // }

                $user = User::create($data);
                if (!session()->get('install_finish')) {
                    $userRole = Role::where('name', '=', 'user')->first();
                } else {
                    $userRole = Role::where('name', '=', 'admin')->first();
                }

                $user->assignRole($userRole);
                DB::commit();

                if (isset($params['isApi'])) {
                    $token = $user->createToken('auth');
                    return $this->response
                        ->setData(['accessToken' => $token->plainTextToken, 'userData' => [
                            'email' => "admin@vuexy.com",
                            'fullName' => "John Doe",
                            'id' => 1,
                            'role' => "admin",
                            'username' => "johndoe",
                        ]])
                        ->setMessage('common.success');
                }

                return redirect()
                    ->route('Auth::login')
                    ->with('message', __('common.success'));
            } catch (\Exception$ex) {
                DB::rollBack();
                return redirect()
                    ->route('Auth::register')
                    ->withErrors($validator)
                    ->withInput();
            }
        }
    }

    public function postForgetPassword()
    {
        $params = $this->request->all();
        $validator = new Validator($params);
        $validator::checkInputs([
            'email' => 'required',
        ], [
            'email.required' => __('auth.email_required'),
        ]);
        if ($validator::isFail()) {
            if ($params['isApi']) {
                return $this->response
                    ->setError(true)
                    ->setErrorMessages($validator::getMessage());
            }
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput($this->request->all());
        } else {
            try {
                $user = User::where('email', '=', $params['email'])->first();
                if (!$user) {
                    if ($params['isApi']) {
                        return $this->response
                            ->setError(true)
                            ->setErrorMessages(['email' => __('auth.user_valid_reset_password_fail')]);
                    }
                    return redirect()
                        ->back()
                        ->withErrors(['email' => __('auth.user_valid_reset_password_fail')])
                        ->withInput($this->request->all());
                }

                $passwordReset = PasswordReset::where('email', $params['email'])->first();

                if (is_null($passwordReset) || Carbon::parse($passwordReset->updated_at)->addMinute(5) < Carbon::now()) {
                    $token = strtoupper(\Str::random(35));
                    if ($passwordReset) {
                        $passwordReset->updated_at = Carbon::now();
                        $passwordReset->token = $token;
                        $passwordReset->save();
                    } else {
                        $data = [
                            'email' => $params['email'],
                            'token' => $token,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                        $passwordReset = PasswordReset::create($data);
                    }

                    $data = [
                        'from' => $params['email'],
                        'to' => $params['email'],
                        'subject' => 'Reset password',
                        'content' => $token,
                    ];

                    Mail::to($params['email'])->send(new NotifyMail([
                        'subject' => 'Reset password',
                        'content' => "token: {$token}",
                    ]));

                    if ($params['isApi']) {
                        return $this->response
                            ->setMessage('common.success');
                    }

                    return redirect()->back()
                        ->with('message', __('common.success'));
                } else {
                    if ($params['isApi']) {
                        return $this->response
                            ->setError(true)
                            ->setErrorMessages(['email' => __('auth.forget_password_try_later', ['minute' => 5])]);
                    }
                    return redirect()
                        ->back()
                        ->withErrors(['email' => __('auth.forget_password_try_later', ['minute' => 5])])
                        ->withInput();
                }
            } catch (\Exception$e) {
                if ($params['isApi']) {
                    return $this->response
                        ->setError(true)
                        ->setErrorMessages($e->getMessage());
                }

                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }
    }

    public function postResetNewPassword()
    {
        $params = $this->request->all();
        $validator = new Validator($params);

        $validator::checkInputs([
            'token' => 'required',
            'password' => 'required|between:6,255',
            'confirmation_password' => 'required|same:password',
        ], [
            'token.required' => __('common.required', ['name' => __('auth.token')]),
            'password.required' => __('common.required', ['name' => __('auth.new_password')]),
            'password.between' => __('common.between', ['name' => __('auth.new_password'), 'min' => 6, 'max' => 255]),
            'confirmation_password.required' => __('common.required', ['name' => __('auth.confirmation_password')]),
            'confirmation_password.same' => __('auth.confirmation_password_same'),
        ]);

        if ($validator::isFail()) {
            if ($params['isApi']) {
                return $this->response
                    ->setError(true)
                    ->setErrorMessages($validator::getMessage());
            }
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput($this->request->all());
        } else {

            try {
                $passwordReset = PasswordReset::where('token', strtoupper($params['token']))->first();
                if (is_null($passwordReset)) {
                    return $this->response
                        ->setError(true)
                        ->setErrorMessages([
                            'token' => __('auth.no_token_exist'),
                        ]);
                }

                if (Carbon::parse($passwordReset->updated_at)->addMinute(30) < Carbon::now()) {
                    return $this->response
                        ->setError(true)
                        ->setErrorMessages([
                            'token' => __('auth.your_token_has_expired'),
                        ]);
                }

                $user = User::where('email', $passwordReset->email)->first();
                if (!$user) {
                    if ($params['isApi']) {
                        return $this->response
                            ->setError(true)
                            ->setErrorMessages([
                                'token' => __('auth.confirm_new_password_fail'),
                            ]);
                    }
                    return redirect()
                        ->back()
                        ->withErrors(['token' => __('auth.confirm_new_password_fail')])
                        ->withInput($this->request->all());
                }

                try {
                    DB::beginTransaction();
                    $user->password = Hash::make($params['new_password']);
                    $user->save();
                    $passwordReset->delete();
                    DB::commit();

                    if ($params['isApi']) {
                        return $this->response
                            ->setMessage('common.success');
                    }

                    return redirect()
                        ->route('Auth::login')
                        ->with('message', __('common.success'));
                } catch (\Exception$ex) {
                    DB::rollBack();
                    if ($params['isApi']) {
                        return $this->response
                            ->setError(true)
                            ->setErrorMessages($e->getMessage());
                    }
                    return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput();
                }
            } catch (\Exception$ex) {
                if ($params['isApi']) {
                    return $this->response
                        ->setError(true)
                        ->setErrorMessages($e->getMessage());
                }
                return redirect()
                    ->back();
            }
        }
    }

    public function getMe()
    {
        $params = $this->request->all();
        try {
            $user = Auth::user();
            if ($params['isApi']) {
                return $this->response
                    ->setData(['userData' => [
                        'email' => "admin@vuexy.com",
                        'fullName' => "John Doe",
                        'id' => 1,
                        'role' => "admin",
                        'username' => "johndoe",
                        'otp' => [
                            'isActive' => isEmpty($user->two_factor_secret) ? false : true,
                        ],
                    ]])
                    ->setMessage(__('common.success'));
            }
        } catch (\Exception$e) {
            if ($params['isApi']) {
                return $this->response
                    ->setError(true)
                    ->setMessage($e->getMessage());
            }
            return redirect()
                ->back();
        }

    }

    public function test()
    {

        // \Hook::callHook('auth.register', ['test']);
        // if (class_exists('\ZacoSoft\Hook\Hook')) {
        //     die("------2022-04-10 14:15:12------");
        // }

        die("------2022-04-10 13:56:28------");
    }

    public function test2()
    {
        Hook::listen('testing', function ($callback, $output, $otherString) {
            if ($output === 'test string') {
                $output = "{$output} yeeeaaaayyy!";
            }
            if ($otherString === 'other_string') {
                // other string is good too
            }
            return $output; // 'test string yeeeaaaayyy!'
        });
    }
}
