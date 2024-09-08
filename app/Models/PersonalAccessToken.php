<?php

namespace App\Models;

use ZacoSoft\ZacoBase\Models\BaseModel;

/**
 * @property integer $id
 * @property string $tokenable_type
 * @property integer $tokenable_id
 * @property string $name
 * @property string $token
 * @property string $abilities
 * @property string $last_used_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $expires_at
 */
class PersonalAccessToken extends BaseModel
{
    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['tokenable_type', 'tokenable_id', 'name', 'token', 'abilities', 'last_used_at', 'created_at', 'updated_at', 'expires_at'];
}
