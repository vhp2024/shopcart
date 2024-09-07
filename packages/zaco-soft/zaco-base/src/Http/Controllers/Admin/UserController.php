<?php
namespace ZacoSoft\ZacoBase\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use ZacoSoft\ZacoBase\Http\Controllers\BaseController as Controller;
use ZacoSoft\ZacoBase\Libraries\Repository;

class UserController extends Controller
{
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
}
