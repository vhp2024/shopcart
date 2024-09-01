<?php

namespace App\Models;

use Pimosoft\PimoBase\Models\BaseModel;

/**
 * @property integer $permission_id
 * @property string $model_type
 * @property integer $model_id
 * @property Permission $permission
 */
class ModelHasPermission extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission()
    {
        return $this->belongsTo('App\Models\Permission');
    }
}
