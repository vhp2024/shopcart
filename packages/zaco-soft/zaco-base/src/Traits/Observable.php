<?php

namespace ZacoSoft\ZacoBase\Traits;

use Illuminate\Database\Eloquent\Model;
trait Observable
{
    public static function bootObservable()
    {
        static::updating(function (Model $model) {
            // $model->updated_user_id = \Auth::user()->id;
            // $model->updated_at = Carbon::now();
        });

        // DB::listen(function($query) {
        //     File::append(
        //         storage_path('/logs/query.log'),
        //         '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL . PHP_EOL
        //     );
        // });
    }
}
