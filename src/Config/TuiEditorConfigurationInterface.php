<?php


namespace Teebb\TuiEditorBundle\Config;


use Teebb\TuiEditorBundle\Exception\ConfigException;

interface TuiEditorConfigurationInterface
{
    public function isEnable(): bool;

    public function isToHtml(): bool;

    public function getBasePath(): string;

    public function getDefaultConfig(): ?string;

    public function getConfigs(): array;

    public function getExtensions(): array;

    public function getDependencies(): array;

    public function getJquery(): array;

    public function getEditor(): array;

    public function getViewer(): array;

    public function getAssetRepository(): string;

    /**
     * @throws ConfigException
     */
    public function getConfig(string $name): array;
}
