<?php

namespace Teebb\TuiEditorBundle\Twig;

use Teebb\TuiEditorBundle\Renderer\TuiEditorRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


final class TuiEditorExtension extends AbstractExtension implements TuiEditorRendererInterface
{
    /**
     * @var TuiEditorRendererInterface
     */
    private $renderer;

    public function __construct(TuiEditorRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function getFunctions(): array
    {
        $options = ['is_safe' => ['html']];

        return [
            new TwigFunction('tuieditor_viewer_widget', [$this, 'renderViewer'], $options),
            new TwigFunction('tuieditor_editor_widget', [$this, 'renderEditor'], $options),
            new TwigFunction('tuieditor_dependencies', [$this, 'renderDependencies'], $options),
        ];
    }

    public function renderViewer(string $id, string $content, ?array $formConfig): string
    {
        return $this->renderer->renderViewer($id, $content, $formConfig);
    }

    public function renderEditor(string $id, array $config, string $content = null, ?array $formConfig): string
    {
        return $this->renderer->renderEditor($id, $config, $content, $formConfig);
    }

    public function renderDependencies(array $dependencies = null): string
    {
        return $this->renderer->renderDependencies($dependencies);
    }

}
