<?php
namespace ZacoSoft\ZacoBase\Traits;

use Route;

trait LoadAndPublishDataTrait
{

    use LighthouseTrait;
    /**
     * @var string
     */
    protected $namespace = null;

    /**
     * @var string
     */
    protected $basePath = null;

    /**
     * @param $namespace
     * @return $this
     */
    public function setNamespace($namespace): self
    {
        $this->namespace = ltrim(rtrim($namespace, '/'), '/');
        $this->shortNameSpace = str_replace('zaco-soft/', '', $this->namespace);
        $this->setBasePath(base_path() . '/packages/');
        $this->addPackageToConfig();

        return $this;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setBasePath($path): self
    {
        $this->basePath = !$this->isReleased() ? $path : $this->platformPath();
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath ?? $this->platformPath();
    }

    /**
     * Publish the given configuration file name (without extension) and the given module
     * @param $fileNames
     * @return $this
     */
    public function loadAndPublishConfigurations($fileNames): self
    {
        if (!is_array($fileNames)) {
            $fileNames = [$fileNames];
        }
        foreach ($fileNames as $fileName) {
            $this->mergeConfigFrom($this->getConfigFilePath($fileName), $this->getDotedNamespace() . '.' . $fileName);
            if ($this->app->runningInConsole()) {
                $this->publishes([
                    $this->getConfigFilePath($fileName) => config_path($this->getDashedNamespace() . '/' . $fileName . '.php'),
                ], 'zaco-soft-config');
            }
        }

        return $this;
    }

    /**
     * @param array $fileNames
     * @return $this
     */
    public function loadGraphQL($fileNames = []): self
    {
        if (!is_array($fileNames)) {
            $fileNames = [$fileNames];
        }

        $this->app->events->listen(
            \Nuwave\Lighthouse\Events\BuildSchemaString::class,
            function () use ($fileNames): string {
                $content = '';
                foreach ($fileNames as $fileName) {
                    $filePath = $this->getGraphQLFilePath($fileName);

                    $content .= get_file_data($filePath, false);
                }

                return $content;
            }
        );

        $this->registerLightHouseNamespace();

        return $this;
    }

    public function loadHelpers($fileNames): self
    {
        if (!is_array($fileNames)) {
            $fileNames = [$fileNames];
        }

        foreach ($fileNames as $fileName) {
            $filePath = $this->getBasePath() . $this->getDashedNamespacePath() . '/helpers/' . $fileName . '.php';
            if (file_exists($filePath)) {
                require_once $filePath;
            }
        }

        return $this;
    }

    /**
     * Publish the given configuration file name (without extension) and the given module
     * @param $fileNames
     * @return $this
     */
    public function loadRoutes($fileNames = ['web']): self
    {
        if (!is_array($fileNames)) {
            $fileNames = [$fileNames];
        }
        foreach ($fileNames as $fileName) {
            $filePath = $this->getRouteFilePath($fileName);
            switch ($fileName) {
                case 'channels':
                case 'api':
                    Route::prefix($fileName)
                        ->middleware('api')
                        ->group(function () use ($filePath) {
                            $this->loadRoutesFrom($filePath);
                        });
                    break;

                default:
                    $this->loadRoutesFrom($filePath);

            }

        }

        return $this;
    }

    /**
     * @return $this
     */
    public function loadAndPublishViews(): self
    {
        $this->loadViewsFrom($this->getViewsPath(), $this->getShortDashedNamespace());
        return $this;
    }

    /**
     * @return $this
     */
    public function loadAndPublishTranslations(): self
    {
        $this->loadTranslationsFrom($this->getTranslationsPath(), $this->getDashedNamespace());
        if ($this->app->runningInConsole()) {
            $this->publishes([$this->getTranslationsPath() => resource_path('lang/vendor/' . $this->getDashedNamespace())],
                'han-lang');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function loadMigrations(): self
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom($this->getMigrationsPath());
        }
        return $this;
    }

    /**
     * @param null $path
     * @return $this
     */
    public function publishPublicFolder($path = null): self
    {
        return $this->publishAssets($path);
    }

    /**
     * @return $this
     */
    public function publishAssetsFolder(): self
    {
        return $this->publishAssets();
    }

    /**
     * @param null $path
     * @return $this
     */
    public function publishAssets($path = null): self
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([$this->getAssetsPath('build') => public_path($path)], 'han-public');

        }

        return $this;
    }

    /**
     * @param $name
     * @param $connectionData
     * @return $this
     */
    public function addMoreConnection($name, $connectionData): self
    {
        config(['database.connections.' . $name => $connectionData]);

        return $this;
    }

    /**
     * @param $name
     * @param $dbName
     * @param array $config
     * @return $this
     */
    public function addMoreDatabase($name, $dbName, $config = []): self
    {
        $currentDbConfig = $this->getCurrentDatabaseConfig();

        $connections = $currentDbConfig['connections'];
        $currentConnection = $connections[$currentDbConfig['driver']];

        return $this->addMoreConnection($name, array_merge($currentConnection, $config, [
            'database' => $dbName,
        ]));
    }

    /**
     * Get path of the give file name in the given module
     * @param string $file
     * @return string
     */
    protected function getConfigFilePath($file): string
    {
        return $this->getBasePath() . $this->getDashedNamespacePath() . '/config/' . $file . '.php';
    }

    /**
     * @param $file
     * @return string
     */
    protected function getRouteFilePath($file): string
    {
        return $this->getBasePath() . $this->getDashedPackagePath() . '/Routes/' . $file . '.php';
    }

    /**
     * @param $file
     * @return string
     */
    protected function getGraphQLFilePath($file): string
    {
        return $this->getBasePath() . $this->getDashedNamespacePath() . '/graphql/' . $file . '.graphql';
    }

    /**
     * @return string
     */
    protected function getViewsPath(): string
    {
        return $this->getBasePath() . $this->getDashedPackagePath() . '/../resources/views/';
    }

    /**
     * @return string
     */
    protected function getTranslationsPath(): string
    {
        return $this->getBasePath() . $this->getDashedPackagePath() . '/resources/lang/';
    }

    /**
     * @return string
     */
    protected function getMigrationsPath(): string
    {
        return $this->getBasePath() . $this->getDashedNamespacePath() . '/database/migrations/';
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getAssetsPath($path = ''): string
    {
        return $this->getBasePath() . $this->getDashedNamespacePath() . '/public/' . $path;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getPublicPath($path = ''): string
    {
        return $this->getBasePath() . $this->getDashedNamespacePath() . '/public/' . $path;
    }

    /**
     * @return string
     */
    protected function getDotedNamespace(): string
    {
        return str_replace('/', '.', $this->namespace);
    }

    /**
     * @return string
     */
    protected function getDashedPackagePath(): string
    {
        return (!$this->isReleased() ? $this->getDashedNamespace() : str_replace('/', '-', $this->namespace)) . '/src';
    }

    /**
     * @return string
     */
    protected function getDashedNamespace(): string
    {
        return str_replace('.', '/', $this->namespace);
    }

    /**
     * @return string
     */
    protected function getShortDashedNamespace(): string
    {
        return str_replace('.', '/', $this->shortNameSpace);
    }

    /**
     * @return string
     */
    protected function getDashedNamespacePath(): string
    {
        return !$this->isReleased() ? $this->getDashedNamespace() : str_replace('/', '-', $this->namespace);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getCurrentNamespace($path = ''): string
    {
        $current = self::class;

        return explode('\\Providers\\', $current)[0] . ($path !== '' ? '\\' . $path : '');
    }

    /**
     * @return bool
     */
    protected function isReleased(): bool
    {
        return (env('RELEASED', false) || !file_exists(base_path('packages')))
        && !preg_match("/base|core|acl|dev-tool/", $this->namespace);
    }

    /**
     * @return array
     */
    protected function getCurrentDatabaseConfig(): array
    {
        $dbString = 'database';
        $dbCurrent = config($dbString);

        return [
            'connections' => $dbCurrent['connections'],
            'driver' => $dbCurrent['default'],
        ];
    }

    /**
     * define packages path
     */
    protected function addPackageToConfig()
    {
        $config = \App::make('config');
        $packageName = strpos($this->namespace, '/') !== false ? explode('/', $this->namespace)[1] : $this->namespace;
        $packagesPath = $config->get('packages_path') ?? [];
        $packagesPath[$packageName] = strpos($this->namespace, 'base') !== false ? $this->platformPath($packageName) : $this->basePath . $this->namespace;
        $config->set('packages_path', $packagesPath);

        $packagesNames = $config->get('packages_namespace') ?? [];
        $packagesNames[$packageName] = $this->namespace;
        $config->set('packages_namespace', $packagesNames);
    }

    protected function platformPath($path = null)
    {
        return base_path('packages/zaco-soft/' . $path);
    }

    protected function loadHooks()
    {
        $key = sprintf('zaco-soft.%s.hooks', $this->shortNameSpace);
        $config = config($key);
        $list = [];
        foreach ($config as $item) {
            $new = $this->shortNameSpace . '.' . $item;
            $list[$new] = $this->shortNameSpace;
        }
        \Hook::register($list);
    }
}
