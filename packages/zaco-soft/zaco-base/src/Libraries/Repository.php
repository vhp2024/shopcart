<?php
namespace ZacoSoft\ZacoBase\Libraries;

use ZacoSoft\ZacoBase\Repositories\Eloquent\RepositoriesAbstract;

class Repository extends RepositoriesAbstract
{
    protected $model;
    public function __construct($model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function prepareData($params = [], $action = 'store')
    {
        if (isset($params['isApi'])) {
            unset($params['isApi']);
        }
        $configPrefix = 'zaco-soft.zaco-base.table.' . $this->model->getTableName();
        $prefix = config($configPrefix);
        if (!isEmpty($prefix)) {
            $oldParam = $params;
            $params = [];
            foreach ($oldParam as $key => $value) {
                $newKey = $prefix . $key;
                $params[$newKey] = $value;
            }
        }

        // if($action == 'store') {
        //     $fields =  array_merge($this->model->getFillable(), $this->model->getHidden());
        //     if(in_array('created_user_id', $fields)) {
        //         $params['created_user_id'] = \Auth::user()->id;
        //     }
        // }
        return $params;
    }
}
