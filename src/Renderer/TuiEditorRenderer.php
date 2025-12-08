<?php


namespace Teebb\TuiEditorBundle\Renderer;


use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;


final class TuiEditorRenderer implements TuiEditorRendererInterface
{
    /**
     * @var array
     */
    private $options;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Packages
     */
    private $assetsPackages;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var null|string
     */
    private $locale;

    /**
     * TuiEditorRenderer constructor.
     * @param array $options The TeebbTuiEditorBundle all the configs is here.
     * @param RouterInterface $router
     * @param Packages $packages
     * @param RequestStack $requestStack
     * @param Environment $twig
     */
    public function __construct(
        array $options,
        RouterInterface $router,
        Packages $packages,
        RequestStack $requestStack,
        Environment $twig,
        ?string $locale
    )
    {
        $this->options = $options;
        $this->router = $router;
        $this->assetsPackages = $packages;
        $this->twig = $twig;
        $this->requestStack = $requestStack;
        $this->locale = $locale;
    }

    public function renderBasePath(string $basePath): string
    {
        return $this->fixPath($basePath);
    }

    public function renderEditorJsPath(string $editorJsPath = null): string
    {
        if ($editorJsPath === null) {
            return $this->fixPath($this->options['editor_js_path']);
        }
        return $this->fixPath($editorJsPath);
    }

    public function renderJqueryPath(string $jqueryPath = null): string
    {
        if ($jqueryPath === null) {
            return $this->fixPath($this->options['jquery_path']);
        }
        return $this->fixPath($jqueryPath);
    }

    public function renderEditorCssPath(string $editorCssPath = null): string
    {
        if ($editorCssPath === null) {
            return $this->fixPath($this->options['editor_css_path']);
        }
        return $this->fixPath($editorCssPath);
    }

    public function renderViewerCssPath(string $viewerCssPath = null): string
    {
        if ($viewerCssPath === null) {
            return $this->fixPath($this->options['viewer_css_path']);
        }
        return $this->fixPath($viewerCssPath);
    }


    public function renderEditorContentsCssPath(string $editorContentsCssPath = null): string
    {
        if ($editorContentsCssPath === null) {
            return $this->fixPath($this->options['editor_contents_css_path']);
        }
        return $this->fixPath($editorContentsCssPath);
    }

    public function renderDependencies(array $dependencies = null): string
    {
        if ($dependencies === null) {
            $dependencies = $this->options['dependencies'];
        }

        $dependenciesJsHtml = "";
        $dependenciesCssHtml = "";

        if ($this->options['jquery']) {
            $dependenciesJsHtml .= $this->renderScriptBlock($this->options['jquery_path']);
        }
        foreach ($dependencies as $dependency) {
            if ($dependency['js_path'] !== null) {
                $dependenciesJsHtml .= $this->renderScriptBlock($dependency['js_path']);
            }
            if ($dependency['css_path'] !== null) {
                $dependenciesCssHtml .= $this->renderStyleBlock($dependency['css_path']);
            }
        }
        return $dependenciesJsHtml . $dependenciesCssHtml;
    }

    public function renderScriptBlock(string $path): string
    {
        return sprintf('<script src="%s"></script>', $this->fixPath($path));
    }

    public function renderStyleBlock(string $path): string
    {
        return sprintf('<link rel="stylesheet" href="%s" />', $this->fixPath($path));
    }

    public function renderExtensions($extensions): string
    {
        $extsJsHtml = "";
        $extsCssHtml = "";

        if (null !== $extensions) {
            foreach ($extensions as $extKey => $extValue) {
                switch ($extValue) {
                    case 'colorSyntax':
                        $extsJsHtml .= $this->renderScriptBlock($this->options['extensions']['colorSyntax']['tui_code_color_syntax_js_path']);
                        $extsCssHtml .= $this->renderStyleBlock($this->options['extensions']['colorSyntax']['tui_code_color_syntax_css_path']);
                        $extsCssHtml .= $this->renderStyleBlock($this->options['extensions']['colorSyntax']['tui_code_color_picker_css_path']);
                        break;
                    case 'chart':
                        $extsJsHtml .= $this->renderScriptBlock($this->options['extensions']['chart']['tui_chart_js_path']);
                        $extsCssHtml .= $this->renderStyleBlock($this->options['extensions']['chart']['tui_chart_css_path']);
                        break;
                    case 'codeSyntaxHighlight':
                        $extsJsHtml .= $this->renderScriptBlock($this->options['extensions']['codeSyntaxHighlight']['tui_code_syntax_highlight_js_path']);
                        $extsCssHtml .= $this->renderStyleBlock($this->options['extensions']['codeSyntaxHighlight']['tui_code_syntax_highlight_css_path']);
                        break;
                    case 'tableMergedCell':
                        $extsJsHtml .= $this->renderScriptBlock($this->options['extensions']['tableMergedCell']['tui_table_merged_cell_js_path']);
                        $extsCssHtml .= $this->renderStyleBlock($this->options['extensions']['tableMergedCell']['tui_table_merged_cell_css_path']);
                        break;
                    case 'uml':
                        $extsJsHtml .= $this->renderScriptBlock($this->options['extensions']['uml']['tui_uml_js_path']);
                        break;
                }
            }
        }
        return $extsJsHtml.$extsCssHtml;
    }

    public function renderViewer(string $id, string $content, string $viewerJsPath = null): string
    {

        if (null === $viewerJsPath) {
            $viewerJsPath = $this->options['viewer_js_path'];
        }
        $extensions = $this->options['configs'][$this->options['default_config']]['exts'];

        $viewerJsCode = $this->renderScriptBlock($viewerJsPath);
        $editorContentsCssCode = $this->renderStyleBlock($this->options['editor_contents_css_path']);
        $viewerCssCode = $this->renderStyleBlock($this->options['viewer_css_path']);

        $extsHtml = $this->renderExtensions($extensions);

        $viewerJsScript = sprintf(
            '<script class="code-js">
                var content = %s;
                const viewer_%s = new Viewer({
                    el: document.querySelector("#%s"),
                    height: "%s",
                    initialValue: content,
                });
            </script>',
            $this->fixContentToJs($content),
            $id,
            $id,
            "300px",
            $this->fixArrayToJs($extensions)
        );

        return $viewerJsCode . $viewerCssCode. $editorContentsCssCode . $extsHtml . $viewerJsScript;
    }

    private function fixArrayToJs(array $array, array $excludeList = []): string
    {
        if (null == $array) {
            return "";
        }
        $jsArray = [];
        foreach ($array as $key => $item) {
            if (in_array($item, $excludeList)) continue;
            array_push($jsArray, $item);
        }

        return implode(",", $jsArray);
    }

    private function fixContentToJs(string $content): string
    {
        if (null == $content) {
            $content = "";
        }
        return json_encode($content);
    }

    private function fixPath(string $path): string
    {
        if (null === $this->assetsPackages) {
            return $path;
        }

        $url = $this->assetsPackages->getUrl($path);

        if ('/' === substr($path, -1) && false !== ($position = strpos($url, '?'))) {
            $url = substr($url, 0, (int)$position);
        }

        return $url;
    }

    private function fixConfigLanguage(array $config): array
    {
        if (!isset($config['locale']) && null !== ($language = $this->getLanguage())) {
            $config['locale'] = $language;
        }

        return $config;
    }

    private function getLanguage(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $language = $request->getLocale();
            $language = substr($language, 0, 2) . strtoupper(substr(str_replace('-', '_', $language), 2));

            return $language;
        }

        return $this->locale;
    }

    public function renderEditor(string $id, array $config, string $content = null): string
    {
        $config = $this->fixConfigLanguage($config);
        $extensions = $config['exts'];

        $editorJsCode = $this->renderScriptBlock($this->options['editor_js_path']);
        $editorCssCode = $this->renderStyleBlock($this->options['editor_css_path']);
        $editorContentsCssCode = $this->renderStyleBlock($this->options['editor_contents_css_path']);

        $extsHtml = $this->renderExtensions($extensions);

        $editorJsScript = sprintf(
            '<script class="code-js">
                var content = %s;
                const %s = new Editor({
                    el: document.querySelector("#%s"),
                    initialEditType: "%s",
                    previewStyle: "%s",
                    height: "%s",
                    language: "%s",
                    initialValue: content,
                    plugins: [%s]
                });
            </script>',
            $this->fixContentToJs($content),
            $id,
            $id,
            array_key_exists('initialEditType', $config) ? $config['initialEditType'] : "markdown",
            array_key_exists('previewStyle', $config) ? $config['previewStyle'] : "vertical",
            array_key_exists('height', $config) ? $config['height'] : "300px",
            array_key_exists('language', $config) ? $config['language'] : $config['locale'],
            $this->fixArrayToJs($extensions)
        );

        return $extsHtml . $editorJsCode . $editorCssCode . $editorContentsCssCode . $editorJsScript;
    }
}