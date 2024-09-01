<?php
namespace ZacoSoft\ZacoBase\Traits;

use File;

trait LighthouseTrait
{
    /**
     * @param string $key
     * @param array $namespaces
     * @return $this
     * @author: Hung <hung@hanbiro.com>
     */
    private function _setLighthouseNamespace(string $key, array $namespaces): self
    {
        config([$key => array_merge((array) config($key), $namespaces)]);
        return $this;
    }

    /**
     * @param string $key
     * @param string $namespaces
     * @return $this
     * @author: Hung <hung@hanbiro.com>
     */
    protected function _registerLighthouseNamespace(string $key, string $namespaces): self
    {
        return $this->_setLighthouseNamespace('lighthouse.namespaces.' . $key, [$namespaces]);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getSrcPath($path = ''): string
    {
        return $this->getBasePath() . $this->getDashedNamespacePath() . '/src/' . $path;
    }

    /**
     * @return bool
     */
    protected function hasGraphQLFolder(): bool
    {
        return File::exists($this->getSrcPath('Http'));
    }

    /**
     * Autoload graphql namespace
     */
    public function registerLightHouseNamespace()
    {
        if ($this->hasGraphQLFolder()) {
            $modules = [
                'Http/Mutations' => 'mutations',
                'Http/Queries' => 'queries',
                'Http/Subscriptions' => 'subscriptions',
                'Models' => 'models',
                'GraphQL/Directives' => 'directives',
            ];

            foreach ($modules as $name => $key) {
                if (File::exists($this->getSrcPath($name))) {
                    $this->_registerLighthouseNamespace($key, $this->getCurrentNamespace(str_replace('/', '\\', $name)));
                }
            }
        }
    }
}
