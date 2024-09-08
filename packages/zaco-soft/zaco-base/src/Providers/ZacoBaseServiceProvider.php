<?php
namespace ZacoSoft\ZacoBase\Providers;

use Illuminate\Support\ServiceProvider;
use ZacoSoft\ZacoBase\Traits\LoadAndPublishDataTrait;

class ZacoBaseServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('guzzle', function ($app) {
            $guzzle = $app->make('ZacoSoft\ZacoBase\Libraries\Common\Guzzle');
            return $guzzle;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setNamespace('zaco-soft/zaco-base')
            ->loadHelpers(['common', 'format', 'security'])
            ->loadRoutes(['api', 'web', 'admin'])
            ->loadAndPublishConfigurations(['table'])
            ->loadAndPublishViews();

        // DB::listen(function($query) {
        //     File::append(
        //         storage_path('/logs/query.log'),
        //         '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL . PHP_EOL
        //     );
        // });
    }
}
