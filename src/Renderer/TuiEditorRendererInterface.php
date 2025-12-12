<?php


namespace Teebb\TuiEditorBundle\Renderer;


interface TuiEditorRendererInterface
{
    public function renderViewer(string $id, string $content, ?array $formConfig): string;

    public function renderEditor(string $id, array $config, string $content = null, ?array $formConfig): string;

    public function renderDependencies(array $dependencies = null): string;
}