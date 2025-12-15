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

    public function renderScriptBlock(string $path): string
    {
        return sprintf('<script src="%s"></script>', $this->fixPath($path));
    }

    public function renderStyleBlock(string $path): string
    {
        return sprintf('<link rel="stylesheet" href="%s" />', $this->fixPath($path));
    }

    private function retrieveJsPathToHtml(?array $paths) {
        $html = '';
        if ($paths && is_array($paths)) {
            foreach ($paths as $path) {
                if ($path) {
                    $html .= $this->renderScriptBlock($path);
                }
            }
        }
        return $html;
    }

    private function retrieveCssPathToHtml(?array $paths) {
        $html = '';
        if ($paths && is_array($paths)) {
            foreach ($paths as $path) {
                if ($path) {
                    $html .= $this->renderStyleBlock($path);
                }
            }
        }
        return $html;
    }

    public function renderDependencies(array $dependencies = null): string
    {
        $dependencies = $this->getOption(null, 'dependencies', $dependencies, $this->options, true);

        $dependenciesJsHtml = "";
        $dependenciesCssHtml = "";

        foreach ($dependencies as $dependencyName => $dependencyConfigs) {
            if (is_array($dependencyConfigs) && array_key_exists('js_paths',$dependencyConfigs)) {
                $dependenciesJsHtml .= $this->retrieveJsPathToHtml($dependencyConfigs['js_paths']);
            } else {
                // Find default
                $dependencyFindDefault = $this->getOptionForParent('js_paths', $dependencyName, 'dependencies', $dependencies, $this->options, true);
                $dependenciesJsHtml .= $this->retrieveJsPathToHtml($dependencyFindDefault);
            }
            if (is_array($dependencyConfigs) && array_key_exists('css_paths',$dependencyConfigs)) {
                $dependenciesCssHtml .= $this->retrieveCssPathToHtml($dependencyConfigs['css_paths']);
            } else {
                // Find default
                $dependencyFindDefault = $this->getOptionForParent('css_paths', $dependencyName, 'dependencies', $dependencies, $this->options, true);
                $dependenciesCssHtml .= $this->retrieveCssPathToHtml($dependencyFindDefault);
            }
        }
        return $dependenciesJsHtml . $dependenciesCssHtml;
    }

    public function renderExtensions($extensions, array $excludeList = []): string
    {
        $extsJsHtml = "";
        $extsCssHtml = "";
        if (null !== $extensions) {
            foreach ($extensions as $extensionName => $extensionConfigs) {
                if (in_array($extensionName, $excludeList)) continue;

                if (is_array($extensionConfigs) && array_key_exists('js_paths',$extensionConfigs)) {
                    $extsJsHtml .= $this->retrieveJsPathToHtml($extensionConfigs['js_paths']);
                } else {
                    // Find default
                    $extensionFindDefault = $this->getOptionForParent('js_paths', $extensionName, 'extensions', $extensionConfigs, $this->options, true);
                    $extsJsHtml .= $this->retrieveJsPathToHtml($extensionFindDefault);
                }
                if (is_array($extensionConfigs) && array_key_exists('css_paths',$extensionConfigs)) {
                    $extsCssHtml .= $this->retrieveCssPathToHtml($extensionConfigs['css_paths']);
                } else {
                    // Find default
                    $extensionFindDefault = $this->getOptionForParent('css_paths', $extensionName, 'extensions', $extensionConfigs, $this->options, true);
                    $extsCssHtml .= $this->retrieveCssPathToHtml($extensionFindDefault);
                }
            }
        }
        return $extsJsHtml.$extsCssHtml;
    }

    private function fixArrayToJs(array $array, array $excludeList = []): string
    {
        if (null == $array) {
            return "";
        }
        $jsArray = [];
        foreach ($array as $key=>$item) {
            if (in_array($key, $excludeList)) continue;
            $options = null;
            if (is_array($item)) {
                if (array_key_exists('options', $item)) {
                    $options = $item['options'];
                }
            }
            if ($options) {
                array_push($jsArray, '['.$key.','.json_encode($item['options']).']');
            } else {
                array_push($jsArray, $key);
            }
        }

        return implode(",", $jsArray);
    }

    private function getContentToJson(string $content): string
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

    private function getOptionAsJson($key, $keyOption, $userPreferenceConfig, $config, $checkDefaultConfig = false): ?string
    {
        return json_encode($this->getOption($key, $keyOption, $userPreferenceConfig, $config, $checkDefaultConfig));
    }

    private function getOption($key, $keyOption, $userPreferenceConfig, $config, $checkDefaultConfig = false)
    {
        if (empty($key)) {
            if (is_array($userPreferenceConfig) && array_key_exists($keyOption, $userPreferenceConfig)) {
                return $userPreferenceConfig[$keyOption];
            } else {
                if ($checkDefaultConfig) {
                    $defaultConfig = $config['default_config'];
                    return $this->getOption($key, $keyOption,$defaultConfig && array_key_exists($defaultConfig, $config['configs']) ? $config['configs'][$defaultConfig]: null, $config);
                } else {
                    return $config[$keyOption];
                }
            }
        } else if (is_array($userPreferenceConfig) && array_key_exists($keyOption, $userPreferenceConfig) && is_array($userPreferenceConfig[$keyOption]) && array_key_exists($key, $userPreferenceConfig[$keyOption])) {
            return $userPreferenceConfig[$keyOption][$key];
        } else {
            if ($checkDefaultConfig) {
                $defaultConfig = $config['default_config'];
                return $this->getOption($key, $keyOption,$defaultConfig && array_key_exists($defaultConfig, $config['configs']) ? $config['configs'][$defaultConfig]: null, $config);
            } else {
                return $config[$keyOption][$key];
            }
        }
    }

    private function getOptionForParentAsJson($key, $keyOption, $keyParent, $userPreferenceConfig, $config, $checkDefaultConfig = false): ?string
    {
        return json_encode($this->getOptionForParent($key, $keyOption, $keyParent, $userPreferenceConfig, $config, $checkDefaultConfig));
    }

    private function getOptionForParent($key, $keyOption, $keyParent, $userPreferenceConfig, $config, $checkDefaultConfig = false)
    {
        if (empty($key)) {
            if (is_array($userPreferenceConfig) && array_key_exists($keyOption, $userPreferenceConfig)) {
                return $userPreferenceConfig[$keyOption];
            } else {
                if ($checkDefaultConfig) {
                    $defaultConfig = $config['default_config'];
                    return $this->getOption($key, $keyOption,$defaultConfig && array_key_exists($defaultConfig, $config['configs']) ? $config['configs'][$defaultConfig][$keyParent]: null, $config[$keyParent]);
                } else {
                    return $config[$keyParent][$keyOption];
                }
            }
        } else if (is_array($userPreferenceConfig) && array_key_exists($keyOption, $userPreferenceConfig) && is_array($userPreferenceConfig[$keyOption]) && array_key_exists($key, $userPreferenceConfig[$keyOption])) {
            return $userPreferenceConfig[$keyOption][$key];
        } else {
            if ($checkDefaultConfig) {
                $defaultConfig = $config['default_config'];
                return $this->getOption($key, $keyOption,$defaultConfig && array_key_exists($defaultConfig, $config['configs']) ? $config['configs'][$defaultConfig][$keyParent]: null, $config[$keyParent]);
            } else {
                return $config[$keyParent][$keyOption][$key];
            }
        }
    }

    public function renderEditor(string $id, array $config, string $content = null, ?array $formConfig): string
    {
        $config = $this->fixConfigLanguage($config);
        $jqueryEnable = $this->getOption('enable', 'jquery', $formConfig, $this->options, true);
        $jqueryJsCode = '';
        if ($jqueryEnable) {
            $jqueryJsPaths = $this->getOption('js_paths', 'jquery', $formConfig, $this->options, true);
            $jqueryJsCode = $this->retrieveJsPathToHtml($jqueryJsPaths);
        }

        $extensions = $this->getOption(null, 'extensions', $config, $this->options, true);
        $extensionsnHtml = $this->renderExtensions($extensions);

        $editorJsScript = sprintf(
            '<script class="code-js">
                var content = %s;
                const %s = new Editor({
                    el: document.querySelector("#%s"),
                    initialEditType: %s,
                    previewStyle: %s,
                    height: %s,
                    language: "%s",
                    initialValue: content,
                    plugins: [%s],
                    theme: %s,
                    toolbarItems: %s
                });
            </script>',
            $this->getContentToJson($content),
            $id,
            $id,
            $this->getOptionForParentAsJson('initial_edit_type', 'options', 'editor', $formConfig, $this->options, true),
            $this->getOptionForParentAsJson('preview_style', 'options', 'editor', $formConfig, $this->options, true),
            $this->getOptionForParentAsJson('height', 'options', 'editor', $formConfig, $this->options, true),
            array_key_exists('language', $config) ? $config['language'] : $config['locale'],
            $this->fixArrayToJs($extensions),
            $this->getOptionForParentAsJson('theme', 'options', 'editor', $formConfig, $this->options, true),
            $this->getOptionForParentAsJson('toolbar_items', 'options', 'editor', $formConfig, $this->options, true)
        );

        $editorJsPaths = $this->getOption('js_paths', 'editor', $formConfig, $this->options, true);
        $editorJsCode = $this->retrieveJsPathToHtml($editorJsPaths);

        $editorCssPaths = $this->getOption('css_paths', 'editor', $formConfig, $this->options, true);
        $editorCssCode = $this->retrieveCssPathToHtml($editorCssPaths);

        return $extensionsnHtml . $jqueryJsCode . $editorJsCode . $editorCssCode . $editorJsScript;
    }

    public function renderViewer(string $id, string $content, ?array $formConfig = null): string
    {
        $jqueryEnable = $this->getOption('enable', 'jquery', $formConfig, $this->options, true);
        $jqueryJsCode = '';
        if ($jqueryEnable) {
            $jqueryJsPaths = $this->getOption('js_paths', 'jquery', $formConfig, $this->options, true);
            $jqueryJsCode = $this->retrieveJsPathToHtml($jqueryJsPaths);
        }
        $extensions = $this->getOption(null, 'extensions', $formConfig, $this->options, true);
        $extensionsnHtml = $this->renderExtensions($extensions, ["uml", "colorSyntax"]);

        $viewerJsScript = sprintf(
            '<script class="code-js">
                var content = %s;
                const viewer_%s = new Viewer({
                    el: document.querySelector("#%s"),
                    height: %s,
                    initialValue: content,
                    plugins: [%s]
                });
            </script>',
            $this->getContentToJson($content),
            $id,
            $id,
            $this->getOptionForParentAsJson('height', 'options', 'viewer', $formConfig, $this->options, true),
            $this->fixArrayToJs($extensions, ["uml", "colorSyntax"])
        );

        $viewerJsPaths = $this->getOption('js_paths', 'viewer', $formConfig, $this->options, true);
        $viewerJsCode = $this->retrieveJsPathToHtml($viewerJsPaths);

        $viewerCssPaths = $this->getOption('css_paths', 'viewer', $formConfig, $this->options, true);
        $viewerCssCode = $this->retrieveCssPathToHtml($viewerCssPaths);

        return $viewerJsCode . $jqueryJsCode. $viewerCssCode . $extensionsnHtml . $viewerJsScript;
    }
}