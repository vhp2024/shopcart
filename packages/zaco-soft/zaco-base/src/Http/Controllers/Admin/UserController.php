<?php
namespace ZacoSoft\ZacoBase\Http\Controllers\Admin;

use DB;
use Hash;
use App\Models\User;
use App\Models\Role;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Http\Request;
use ZacoSoft\ZacoBase\Http\Controllers\BaseController as Controller;
use ZacoSoft\ZacoBase\Libraries\Common\FormData;
use ZacoSoft\ZacoBase\Libraries\Common\Repository;
use ZacoSoft\ZacoBase\Libraries\Common\ZacoValidator;

class UserController extends Controller
{
    protected $i_group = 'user';

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    public function getList()
    {
        $params = $this->request->all();
        $userModel = new User();
        $repository = new Repository($userModel);
        $where = [];
        $data = $repository->getList([]);

        $result = [];
        foreach ($data['rows'] as $item) {
            $result[] = [
                'id' => $item->id,
                'status' => $item->status,
                'avatar' => $item->avatar,
                'full_name' => $item->full_name,
                'email' => $item->email,
                'username' => $item->username,
                'isOtp' => isEmpty($item->two_factor_secret),
            ];
        }

        return $this->response
            ->setIsReturnResponse(true)
            ->setDataTable([
                'draw' => time(),
                'recordsTotal' => 2,
                'recordsFiltered' => 2,
            ])
            ->setData(
                $result
            )
            ->setMessage(__('common.success'));
    }

    public function postStatus()
    {
        $params = $this->request->all();
        $userModel = new User();
        $repository = new Repository($userModel);
    }

    public function viewCreateUser()
    {
        $params = $this->request->all();
        $code = 'create';
        $formDataLib = new FormData($this->i_package, 'user_create', $code);
        list($form, $inputs, $menus) = $formDataLib->getFormRender();

        // Set option for select
        $roleModel = new Role();
        $roleRepository = new Repository($roleModel);
        $roles =  $roleRepository->all();
        $roleOptions = [];
        foreach ($roles as $role) {
            $roleOptions[] = [
                'id' => $role->id,
                'text' => $role->name
            ];
        }

        foreach ($inputs as &$input) {
            if(isset($input['name']) && $input['name'] === 'role') {
                $input['options'] = $roleOptions;
            }
        }

        $form['route'] =  'Admin-user::postCreateUser';
        return view('zaco-base::admin.users.create_user', compact('form', 'inputs', 'menus', 'code'));
    }

    public function postCreateUser()
    {
        $params = $this->request->all();

        $validator = new ZacoValidator($params);
        $validator::checkDBInputs($this->i_package, 'user_create', 'create');
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

                $user = User::create($data);
                $userRole = SpatieRole::where('id', $params['role'])->first();
                $user->assignRole($userRole);
                DB::commit();

                return redirect()
                    ->route('Admin-user::index')
                    ->with('message', __('common.success'));
            } catch (\Throwable$th) {
                return $this->response
                    ->setError(true)
                    ->setErrorMessages($th->getMessage());
            }
        }
    }
}
