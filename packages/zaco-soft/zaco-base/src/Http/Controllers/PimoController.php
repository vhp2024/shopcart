<?php

namespace ZacoSoft\ZacoBase\Http\Controllers;

use Illuminate\Http\Request;
use ZacoSoft\ZacoBase\Http\Controllers\BaseController as Controller;

class PimoController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    public function getConfig()
    {
        $array = [
            'softwareCode' => getSoftwareCode(),
            'merchantCode' => 'd7bd7cd1-63da-407c-8c05-d0bacf86b03d',
            'purchaseCode' => '829c0588-8f92-408f-af2d-e651e6f01cf0',
            'numberCode' => '20230524',
        ];
        $encryptData = encryptLayer2(json_encode($array), getDomain());
        $encryptData = encryptLayer1($encryptData);

        return $this->response
            ->setData(['encryptData' => $encryptData])
            ->setMessage(__('common.success'));
    }
}
