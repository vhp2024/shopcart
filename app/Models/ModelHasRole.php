<?php

namespace App\Models;

use Pimosoft\PimoBase\Models\BaseModel;

/**
 * @property integer $role_id
 * @property string $model_type
 * @property integer $model_id
 * @property Role $role
 */
class ModelHasRole extends BaseModel
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
}
