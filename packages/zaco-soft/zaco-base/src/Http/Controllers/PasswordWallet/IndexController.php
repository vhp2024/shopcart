<?php
namespace ZacoSoft\ZacoBase\Http\Controllers\PasswordWallet;

use App\Models\DefaultPasswordWallet;
use App\Models\DefaultPasswordWalletCategory;
use Illuminate\Http\Request;
use ZacoSoft\ZacoBase\Http\Controllers\BaseController as Controller;
use ZacoSoft\ZacoBase\Libraries\PasswordWallet\Cache;
use ZacoSoft\ZacoBase\Libraries\Repository;
use ZacoSoft\ZacoBase\Libraries\Validator;

class IndexController extends Controller
{
    protected $request;
    protected $i_group = 'password-wallet';
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    public function getList()
    {
        $params = $this->request->all();
        $passwordWalletModel = new DefaultPasswordWallet();
        $repository = new Repository($passwordWalletModel);

        $where = [
            'pw_user_id' => \Auth::user()->id,
            'deleted_at IS NULL' => null,
        ];
        if ($params['category'] != 'all') {
            $where['pw_pwct_id'] = $params['category'];
        } else {
            $where['pw_pwct_id != 4'] = null;
        }
        $data = $repository->getList(['*'], $where);
        if (!isEmpty($data['data'])) {
            $newData = [];
            foreach ($data['data'] as $item) {
                $newData[] = [
                    'pdId' => $item->pw_id,
                    'pwName' => $item->pw_name,
                    'pwCode' => $item->pw_code,
                    'time' => $item->updated_at,
                ];
            }
            $data['data'] = $newData;
        }

        return $this->response
            ->setData([
                'pagination' => $data['pagination'],
                'data' => $data['data'],
            ])
            ->setMessage(__('common.success'));
    }

    public function getDetail()
    {
        $params = $this->request->all();
        if (!isset($params['id'])) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'id' => __('common.missing_param'),
                ]);
        }
        $passwordWalletModel = new DefaultPasswordWallet();
        $repository = new Repository($passwordWalletModel);
        $item = $repository->findById($params['id']);
        if (!$item) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'id' => __('common.data_not_exist'),
                ]);
        }
        $content = json_decode($item->pw_value, true);
        $keyEncrypt = generateSoftwareKey();
        $value = encryptLevel(3, decryptServerLayer2($content['data']), $keyEncrypt);
        unset($content['data']);
        $data = [
            'pwId' => $item->pw_id,
            'pwPwctId' => $item->pw_pwct_id,
            'pwName' => $item->pw_name,
            'pwCode' => $item->pw_code,
            'pwValue' => $value,
            'time' => $item->updated_at,
        ];
        if ($item->pw_code == 'PASSWORD') {
            $data['pwUserMail'] = encryptLevel(3, decryptServerLayer2($content['usernameMail']), $keyEncrypt);
            unset($content['usernameMail']);
        }
        $data['pwContent'] = $content;

        return $this->response
            ->setData($data)
            ->setMessage(__('common.success'));
    }

    public function postSave()
    {
        $params = $this->request->all();
        if (!isset($params['pwCategoryId'])) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'pwType' => __('common.missing_param'),
                ]);
        }

        $passwordWalletCategoryModel = new DefaultPasswordWalletCategory();
        $passwordWalletCategoryRepository = new Repository($passwordWalletCategoryModel);
        $category = $passwordWalletCategoryRepository->findById($params['pwCategoryId']);
        if (isEmpty($category)) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'pwCategoryId' => __('common.data_not_exist'),
                ]);
        }

        $cacheLib = new Cache();
        $cacheCategoryData = $cacheLib->getCacheCategoryCommon();
        if (isset($cacheCategoryData[$category->pwct_parent_id])) {
            $code = $cacheCategoryData[$category->pwct_parent_id];
        } else {
            $code = $cacheCategoryData[$category->pwct_id];
        }

        $validator = new Validator($params);
        $validator::checkDBInputs($this->i_package, $this->i_group, strtolower($code));
        if ($validator::isFail()) {
            return $this->response
                ->setError(true)
                ->setErrorMessages($validator::getMessage());
        } else {
            try {
                $passwordWalletModel = new DefaultPasswordWallet();
                $repository = new Repository($passwordWalletModel);
                $pwValue = [
                    'note' => isset($params['pwNote']) ? $params['pwNote'] : '',
                    'link' => isset($params['pwLink']) ? $params['pwLink'] : '',
                    'data' => encryptServerLayer2($params['pwValue']),
                ];

                if ($code == 'PASSWORD') {
                    $pwValue['usernameMail'] = encryptServerLayer2($params['pwUsernameMail']);
                }

                $data = [
                    'pw_pwct_id' => $params['pwCategoryId'],
                    'pw_code' => $code,
                    'pw_name' => $params['pwName'],
                    'pw_active' => 'ACTIVE',
                    'pw_user_id' => \Auth::user()->id,
                    'pw_sort' => 0,
                    'pw_value' => json_encode($pwValue),
                ];
                $repository->create($data);

                return $this->response
                    ->setMessage(__('common.success'));
            } catch (\Throwable$th) {
                return $this->response
                    ->setError(true)
                    ->setErrorMessages($th->getMessage());
            }
        }
    }

    public function putUpdate()
    {
        $params = $this->request->all();
        $params = $this->request->all();
        if (!isset($params['pwCategoryId']) || !isset($params['pwId'])) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'pwType' => __('common.missing_param'),
                ]);
        }
        $passwordWalletModel = new DefaultPasswordWallet();
        $repository = new Repository($passwordWalletModel);
        $item = $repository->findById($params['pwId']);
        if (!$item) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'id' => __('common.data_not_exist'),
                ]);
        }

        $validator = new Validator($params);
        $validator::checkDBInputs($this->i_package, $this->i_group, strtolower($item->pw_code));
        if ($validator::isFail()) {
            return $this->response
                ->setError(true)
                ->setErrorMessages($validator::getMessage());
        } else {
            try {

                $pwValue = [
                    'note' => isset($params['pwNote']) ? $params['pwNote'] : '',
                    'link' => isset($params['pwLink']) ? $params['pwLink'] : '',
                    'data' => encryptServerLayer2($params['pwValue']),
                ];

                if ($item->pw_code) {
                    $pwValue['usernameMail'] = encryptServerLayer2($params['pwUsernameMail']);
                    unset($params['pwUsernameMail']);
                }

                $item->pw_value = json_encode($pwValue);
                $item->pw_pwct_id = $params['pwCategoryId'];
                $item->pw_name = $params['pwName'];
                $item->save();
                return $this->response
                    ->setMessage(__('common.success'));
            } catch (\Throwable$th) {
                return $this->response
                    ->setError(true)
                    ->setErrorMessages($th->getMessage());
            }
        }
    }

    public function postMoveToTrash()
    {
        $params = $this->request->all();
        if (!isset($params['id'])) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'id' => __('common.missing_param'),
                ]);
        }
        $passwordWalletModel = new DefaultPasswordWallet();
        $repository = new Repository($passwordWalletModel);
        $item = $repository->findById($params['id']);
        if (!$item) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'id' => __('common.data_not_exist'),
                ]);
        }

        try {
            $item->pw_pwct_id = 4;
            $item->save();
            return $this->response
                ->setMessage(__('common.success'));
        } catch (\Throwable$th) {
            return $this->response
                ->setError(true)
                ->setErrorMessages($th->getMessage());
        }
    }

    public function postDelete()
    {
        $params = $this->request->all();
        if (!isset($params['id'])) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'id' => 'Missing param',
                ]);
        }
        $passwordWalletModel = new DefaultPasswordWallet();
        $repository = new Repository($passwordWalletModel);
        $item = $repository->findById($params['id']);
        if (!$item) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'id' => __('common.data_not_exist'),
                ]);
        }

        try {
            $result = $repository->delete($item);
            return $this->response
                ->setMessage(__('common.success'));
        } catch (\Throwable$th) {
            return $this->response
                ->setError(true)
                ->setErrorMessages($th->getMessage());
        }
    }
}
