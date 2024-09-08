<?php

namespace App\Models;

use ZacoSoft\ZacoBase\Models\BaseModel;

/**
 * @property integer $c_id
 * @property string $c_code
 * @property string $c_name
 * @property string $c_content
 * @property string $c_type
 * @property string $created_at
 * @property string $updated_at
 */
class DefaultConstant extends BaseModel
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'c_id';

    /**
     * @var array
     */
    protected $fillable = ['c_code', 'c_name', 'c_content', 'c_type', 'created_at', 'updated_at'];
}
