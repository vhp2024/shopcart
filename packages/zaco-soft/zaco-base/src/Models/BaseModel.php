<?php
namespace ZacoSoft\ZacoBase\Models;

use Eloquent;
use ZacoSoft\ZacoBase\Traits\Observable;

class BaseModel extends Eloquent
{
    use Observable;

    public static function getTableName()
    {
        return with(new static )->getTable();
    }
}
