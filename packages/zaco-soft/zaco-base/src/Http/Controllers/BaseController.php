<?php

namespace ZacoSoft\ZacoBase\Http\Controllers;

use Illuminate\Routing\Controller;
use ZacoSoft\ZacoBase\Http\Responses\BaseResponse;

class BaseController extends Controller
{
    /**
     * @BaseHttpResponse
     */
    public $response;

    protected $i_package = 'base';

    public function __construct()
    {
        $this->response = new BaseResponse();
    }
}
