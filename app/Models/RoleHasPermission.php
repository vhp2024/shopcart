<?php

namespace App\Models;

use ZacoSoft\ZacoBase\Models\BaseModel;

/**
 * @property integer $permission_id
 * @property integer $role_id
 * @property Role $role
 * @property Permission $permission
 */
class RoleHasPermission extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission()
    {
        return $this->belongsTo('App\Models\Permission');
    }
}
