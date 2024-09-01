<?php
namespace ZacoSoft\ZacoBase\Http\Controllers\AccountSettings;

use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use ZacoSoft\ZacoBase\Http\Controllers\BaseController as Controller;
use PragmaRX\Google2FA\Google2FA;

class SecurityController extends Controller
{
    protected $i_group = 'profiles';

    protected $request;
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    public function getOtp()
    {
        $params = $this->request->all();
        if (!Auth::user()->two_factor_secret) {
            $key = Auth::user()->email . 'two_factor_secret2';
            $google2fa = getCacheData($key);
            if (!$google2fa) {
                $google2fa = new Google2FA();
                $secretKey = $google2fa->generateSecretKey();
                $qrCodeUrl = $google2fa->getQRCodeUrl(
                    'ZacoSoft',
                    Auth::user()->email,
                    $secretKey
                );
                $google2fa = [
                    'secretKey' => encryptLayer1($secretKey),
                    'qrCodeUrl' => encryptLayer1($qrCodeUrl),
                ];
                setCacheData($key, $google2fa, 3600);
            }
        }

        if ($params['isApi']) {
            return $this->response
                ->setData($google2fa)
                ->setMessage(__('common.success'));
        }
    }

    public function postOtp()
    {
        $params = $this->request->all();
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey(decryptLayer1($params['secretKey']), $params['code'], 2);
        if (!$valid) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'code' => __('common.valid_fail'),
                ]);
        } else {
            try {
                DB::beginTransaction();
                $user = \Auth::user();
                $user->two_factor_secret = encryptServerLayer2($params['secretKey']);
                $user->save();
                DB::commit();
                return $this->response
                    ->setMessage(__('common.success'));
            } catch (\Exception$ex) {
                DB::rollBack();
                return $this->response
                    ->setError(true)
                    ->setMessage($ex->getMessage());
            }

        }
    }

    public function postCheckOtp()
    {
        $params = $this->request->all();
        $google2fa = new Google2FA();
        $user = \Auth::user();
        $valid = $google2fa->verifyKey(decryptLayer1(decryptServerLayer2($user->two_factor_secret)), $params['code'], 2);
        if (!$valid) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'code' => __('common.valid_fail'),
                ]);
        } else {
            try {
                return $this->response
                    ->setMessage(__('common.success'));
            } catch (\Exception$ex) {
                DB::rollBack();
                return $this->response
                    ->setError(true)
                    ->setMessage($ex->getMessage());
            }

        }
    }

    public function postDeleteOtp()
    {
        $params = $this->request->all();
        $google2fa = new Google2FA();
        $user = \Auth::user();
        $valid = $google2fa->verifyKey(decryptLayer1(decryptServerLayer2($user->two_factor_secret)), $params['code'], 2);
        if (!$valid) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'code' => __('common.valid_fail'),
                ]);
        } else {
            try {
                DB::beginTransaction();
                $user->two_factor_secret = null;
                $user->save();
                DB::commit();
                return $this->response
                    ->setMessage(__('common.success'));
            } catch (\Exception$ex) {
                DB::rollBack();
                return $this->response
                    ->setError(true)
                    ->setMessage($ex->getMessage());
            }

        }
    }
}
