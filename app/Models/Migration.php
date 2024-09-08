<?php

namespace App\Models;

use ZacoSoft\ZacoBase\Models\BaseModel;

/**
 * @property integer $id
 * @property string $migration
 * @property integer $batch
 */
class Migration extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = ['migration', 'batch'];
}
