<?php

namespace App\Models;

use ZacoSoft\ZacoBase\Models\BaseModel;

/**
 * @property integer $id
 * @property string $queue
 * @property string $payload
 * @property boolean $attempts
 * @property integer $reserved_at
 * @property integer $available_at
 * @property integer $created_at
 */
class Job extends BaseModel
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
    protected $fillable = ['queue', 'payload', 'attempts', 'reserved_at', 'available_at', 'created_at'];
}
