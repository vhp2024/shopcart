<?php
namespace ZacoSoft\ZacoBase\Http\Controllers\PasswordWallet;

use App\Models\DefaultPasswordWallet;
use App\Models\DefaultPasswordWalletCategory;
use Illuminate\Http\Request;
use ZacoSoft\ZacoBase\Http\Controllers\BaseController as Controller;
use ZacoSoft\ZacoBase\Libraries\Repository;
use ZacoSoft\ZacoBase\Libraries\Validator;

class CategoryController extends Controller
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
        $passwordWalletCategoryModel = new DefaultPasswordWalletCategory();
        $repository = new Repository($passwordWalletCategoryModel);
        $where = [
            sprintf('(pwct_user_id = %s OR pwct_user_id is NULL)', \Auth::user()->id) => null,
            'deleted_at IS NULL' => null,
        ];
        if ($params['category'] != 'all') {
            $where['pwct_parent_id'] = $params['category'];
        }

        $data = $repository->allBy($where);
        $newData = $this->formatDataRecursive($data);

        return $this->response
            ->setData([
                'data' => $newData,
            ])
            ->setMessage(__('common.success'));
    }

    protected function formatDataRecursive($rows = array(), $parent = null)
    {
        $tree2 = array();
        foreach ($rows as $key => $item) {
            if ($item->pwct_parent_id == $parent) {
                $data = [
                    'pwctId' => $item->pwct_id,
                    'pwctParentId' => $item->pwct_parent_id,
                    'pwctCode' => $item->pwct_code,
                    'pwctName' => $item->pwct_name,
                    'time' => $item->updated_at,
                ];

                $children = $this->formatDataRecursive($rows, $item->pwct_id);
                if (count($children) > 0) {
                    foreach ($children as $child) {
                        $data['children'][] = $child;
                    }
                }
                $tree2[] = $data;
            }
        }
        return array_values($tree2);
    }

    public function get_group_recursive($rows = array(), $parent = '')
    {
        $tree2 = array();
        foreach ($rows as $key => $item) {
            $up_key = $item->pwct_parent_id;
            $folder_key = $item->pwct_id;
            if ($up_key == $parent) {
                $tree2[$folder_key] = [
                    'pwctId' => $item->pwct_id,
                    'pwctParentId' => $item->pwct_parent_id,
                    'pwctCode' => $item->pwct_code,
                    'pwctName' => $item->pwct_name,
                    'time' => $item->updated_at,
                ];

                $children = $this->get_group_recursive($rows, $folder_key);

                if (count($children) > 0) {
                    foreach ($children as $child) {
                        $child_folder_key = $item->pwct_id;
                        $child['parent_key'] = $item->pwct_id;
                        $tree2[$child_folder_key] = $child;
                    }
                }
            }
        }
        return $tree2;
    }

    public function postSave()
    {
        $params = $this->request->all();
        if (!isset($params['pwctParentId'])) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'pwctParentId' => __('common.missing_param'),
                ]);
        }
        $passwordWalletModel = new DefaultPasswordWalletCategory();
        $repository = new Repository($passwordWalletModel);
        $parentCategory = $repository->findById($params['pwctParentId']);

        if (isEmpty($parentCategory)) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'pwctParentId' => __('common.data_not_exist'),
                ]);
        }

        $validator = new Validator($params);
        $validator::checkDBInputs($this->i_package, $this->i_group, 'category');
        if ($validator::isFail()) {
            return $this->response
                ->setError(true)
                ->setErrorMessages($validator::getMessage());
        } else {
            try {
                $data = [
                    'pwct_parent_id' => $params['pwctParentId'],
                    'pwct_name' => $params['pwctName'],
                    'pwct_user_id' => \Auth::user()->id,
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

    public function postUpdate()
    {
        $params = $this->request->all();
        if (!isset($params['id']) || !isset($params['pwctName'])) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'id' => 'Missing param',
                ]);
        }

        $passwordWalletModel = new DefaultPasswordWalletCategory();
        $repository = new Repository($passwordWalletModel);
        $item = $repository->findById($params['id']);
        if (!$item) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'id' => __('common.data_not_exist'),
                ]);
        }

        $user = \Auth::user();
        $isAdmin = $user->hasRole('admin');
        if (!$isAdmin || $item->pwct_user_id != $user->id) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'id' => __('common.no_permission'),
                ]);
        }

        try {
            $item->pwct_name = $params['pwctName'];
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
        $passwordWalletModel = new DefaultPasswordWalletCategory();
        $repository = new Repository($passwordWalletModel);
        $item = $repository->findById($params['id']);
        if (!$item || in_array($params['id'], [1, 2, 3])) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'id' => __('common.data_not_exist'),
                ]);
        }

        $user = \Auth::user();
        $isAdmin = $user->hasRole('admin');
        if (!$isAdmin || $item->pwct_user_id != $user->id) {
            return $this->response
                ->setError(true)
                ->setErrorMessages([
                    'id' => __('common.no_permission'),
                ]);
        }

        try {
            $passwordWalletModel = new DefaultPasswordWallet();
            $passwordRepository = new Repository($passwordWalletModel);
            $passwordRepository->update(['pw_pwct_id' => $params['id']], ['pw_pwct_id' => $item->pwct_parent_id]);
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
