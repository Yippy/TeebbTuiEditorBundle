<?php


namespace Teebb\TuiEditorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        if (\method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('teebb_tui_editor');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('teebb_tui_editor');
        }
        $bundleBasePath = "bundles/teebbtuieditor/tui.editor-bundles/";
        $rootNode
            ->children()
                ->booleanNode('enable')->defaultTrue()->end()
                ->booleanNode('jquery')->defaultTrue()->info("If you want use jquery.js set true.")->end()
                ->scalarNode('base_path')->defaultValue($bundleBasePath)->end()
                ->scalarNode('editor_js_path')->defaultValue($bundleBasePath.'js/toast-ui-editor-bundle.js')->end()
                ->scalarNode('viewer_js_path')->defaultValue($bundleBasePath.'js/toast-ui-viewer-bundle.js')->end()
                ->scalarNode('editor_css_path')->defaultValue($bundleBasePath.'css/toastui-editor.css')->end()
                ->scalarNode('viewer_css_path')->defaultValue($bundleBasePath.'css/toastui-editor-viewer.css')->end()
                ->scalarNode('editor_contents_css_path')->defaultValue(null)->end()
                ->scalarNode('jquery_path')->defaultValue($bundleBasePath.'js/jquery.min.js')->end()
                ->scalarNode('default_config')->defaultValue(null)->end()
                ->scalarNode('asset_repository')->defaultValue('teebbstudios/tui.editor-bundles')->end()
                ->append($this->createEditorOptions())
                ->append($this->createViewerOptions())
                ->append($this->createExtensions($bundleBasePath))
                ->append($this->createDependencies($bundleBasePath))
                ->append($this->createConfigsNode())
            ->end();

        return $treeBuilder;
    }

    private function createEditorOptions()
    {
        return $this->createNode('editor_options')
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('toolbar_items')
                        ->defaultValue([['heading', 'bold', 'italic', 'strike'],['hr', 'quote'],['ul', 'ol', 'task', 'indent', 'outdent'],['table', 'image', 'link'],['code', 'codeblock']])
                        ->arrayPrototype()
                            ->scalarPrototype()
                            ->end()
                        ->end()
                    ->end()
                    ->scalarNode('theme')->defaultValue('light')->end()
                    ->scalarNode('initial_edit_type')->defaultValue('markdown')->end()
                    ->scalarNode('preview_style')->defaultValue('vertical')->end()
                    ->scalarNode('height')->defaultValue('auto')->end()
            ->end();
    }

    private function createViewerOptions()
    {
        return $this->createNode('viewer_options')
            ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('height')->defaultValue('auto')->end()
            ->end();
    }

    private function createExtensions(string $bundleBasePath)
    {
        return $this->createNode('extensions')
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('colorSyntax')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('js_paths')
                                ->defaultValue([$bundleBasePath.'js/toast-ui-color-syntax-bundle.js'])
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('css_paths')
                                ->defaultValue([$bundleBasePath.'css/toastui-editor-plugin-color-syntax.css',$bundleBasePath.'css/tui-color-picker.css'])
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('options')
                                ->defaultValue(null)
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('chart')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('js_paths')
                                ->defaultValue([$bundleBasePath.'js/toast-ui-chart-bundle.js'])
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('css_paths')
                                ->defaultValue([$bundleBasePath.'css/toastui-chart.min.css'])
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('options')
                                ->defaultValue(null)
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('codeSyntaxHighlight')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('js_paths')
                                ->defaultValue([$bundleBasePath.'js/toast-ui-code-syntax-highlight-bundle.js'])
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('css_paths')
                                ->defaultValue([$bundleBasePath.'css/toastui-editor-plugin-code-syntax-highlight.css'])
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('options')
                                ->defaultValue(null)
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('tableMergedCell')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('js_paths')
                                ->defaultValue([$bundleBasePath.'js/toast-ui-table-merged-cell-bundle.js'])
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('css_paths')
                                ->defaultValue([$bundleBasePath.'css/toastui-editor-plugin-table-merged-cell.css'])
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('options')
                                ->defaultValue(null)
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('uml')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('js_paths')
                                ->defaultValue([$bundleBasePath.'js/toast-ui-uml-bundle.js'])
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('css_paths')
                                ->defaultValue(null)
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('options')
                                ->defaultValue(null)
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();
    }

    private function createDependencies($bundleBasePath)
    {
        return $this->createNode('dependencies')
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('editor_dark_theme')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('js_paths')
                                ->defaultValue(null)
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('css_paths')
                                ->defaultValue([$bundleBasePath.'css/toastui-editor-dark.css'])
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();
    }

    private function createConfigsNode(): ArrayNodeDefinition
    {
        return $this->createPrototypeNode('configs')
            ->arrayPrototype()
                ->normalizeKeys(false)
                ->useAttributeAsKey('name')
                ->variablePrototype()->end()
            ->end();
    }

    private function createPrototypeNode(string $name): ArrayNodeDefinition
    {
        return $this->createNode($name)
            ->normalizeKeys(false)
            ->useAttributeAsKey('name');
    }

    private function createNode(string $name): ArrayNodeDefinition
    {
        if (\method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder($name);
            $node = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();
            $node = $treeBuilder->root($name);
        }

        \assert($node instanceof ArrayNodeDefinition);

        return $node;
    }
}