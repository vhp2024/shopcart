<?php

namespace App\Models;

use ZacoSoft\ZacoBase\Models\BaseModel;

/**
 * @property integer $i_id
 * @property string $i_package
 * @property string $i_code
 * @property string $i_group
 * @property string $i_name
 * @property string $i_label
 * @property string $i_type
 * @property string $i_value
 * @property string $i_rules
 * @property array $i_disable_edit
 * @property string $i_active
 * @property string $i_user_type
 * @property string $i_placeholder
 * @property string $created_at
 * @property integer $created_user_id
 * @property string $updated_at
 * @property integer $updated_user_id
 * @property string $deleted_at
 * @property integer $deleted_user_id
 */
class DefaultInput extends BaseModel
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'i_id';

    /**
     * @var array
     */
    protected $fillable = ['i_package', 'i_code', 'i_group', 'i_name', 'i_label', 'i_type', 'i_value', 'i_rules', 'i_disable_edit', 'i_active', 'i_user_type', 'i_placeholder', 'created_at', 'created_user_id', 'updated_at', 'updated_user_id', 'deleted_at', 'deleted_user_id'];
}
