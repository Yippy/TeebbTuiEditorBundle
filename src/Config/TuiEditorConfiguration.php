<?php

namespace Teebb\TuiEditorBundle\Config;

use Teebb\TuiEditorBundle\Exception\ConfigException;

final class TuiEditorConfiguration implements TuiEditorConfigurationInterface
{

    /**
     * @var boolean
     */
    private $enable;

    /**
     * @var boolean
     */
    private $toHtml;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string|null
     */
    private $defaultConfig;

    /**
     * @var array
     */
    private $configs;

    /**
     * @var array
     */
    private $jquery;

    /**
     * @var array
     */
    private $editor;

    /**
     * @var array
     */
    private $viewer;

    /**
     * @var array
     */
    private $extensions;

    /**
     * @var array
     */
    private $dependencies;

    /**
     * @var string
     */
    private $assetRepository;

    public function __construct(array $config)
    {
        if ($config['enable']) {
            $config = $this->resolveConfigs($config);
        }

        $this->enable = $config['enable'];
        $this->basePath = $config['base_path'];
        $this->defaultConfig = $config['default_config'];
        $this->configs = $config['configs'];
        $this->jquery = $config['jquery'];
        $this->editor = $config['editor'];
        $this->viewer = $config['viewer'];
        $this->extensions = $config['extensions'];
        $this->dependencies = $config['dependencies'];
        $this->assetRepository = $config['asset_repository'];

    }

    private function resolveConfigs(array $config): array
    {
        if (empty($config['configs'])) {
            return $config;
        }

        if (!isset($config['default_config']) && !empty($config['configs'])) {
            reset($config['configs']);
            $config['default_config'] = key($config['configs']);
        }

        if (isset($config['default_config']) && !isset($config['configs'][$config['default_config']])) {
            throw ConfigException::invalidDefaultConfig($config['default_config']);
        }

        return $config;
    }

    public function isEnable(): bool
    {
        return $this->enable;
    }

    public function isToHtml(): bool
    {
        return $this->toHtml;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getDefaultConfig(): ?string
    {
        return $this->defaultConfig;
    }

    public function getConfigs(): array
    {
        return $this->configs;
    }

    public function getJquery(): array
    {
        return $this->jquery;
    }

    public function getEditor(): array
    {
        return $this->editor;
    }

    public function getViewer(): array
    {
        return $this->viewer;
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getAssetRepository(): string
    {
        return $this->assetRepository;
    }

    public function getConfig(string $name): array
    {
        if (!isset($this->configs[$name])) {
            throw ConfigException::configDoesNotExist($name);
        }

        return $this->configs[$name];
    }
}