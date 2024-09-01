<?php
namespace ZacoSoft\ZacoBase\Http\Controllers\Admin;

use Illuminate\Http\Request;
use ZacoSoft\ZacoBase\Http\Controllers\BaseController as Controller;

class RolePermissionController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    public function index()
    {
        // $role = Role::create(['name' => 'user']);
    }

    public function postSaveRole()
    {

    }
}
