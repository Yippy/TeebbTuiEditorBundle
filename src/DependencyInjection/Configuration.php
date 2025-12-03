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

        $rootNode
            ->children()
                ->booleanNode('enable')->defaultTrue()->end()
                ->booleanNode('jquery')->defaultTrue()->info("If you want use jquery.js set true.")->end()
                ->scalarNode('base_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/')->end()
                ->scalarNode('editor_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/editor/dist/toastui-editor.js')->end()
                ->scalarNode('viewer_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/editor/dist/toastui-editor-viewer.js')->end()
                ->scalarNode('editor_css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/editor/dist/toastui-editor.css')->end()
                ->scalarNode('editor_contents_css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/editor/dist/toastui-editor-viewer.css')->end()
                ->scalarNode('jquery_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/jquery/dist/jquery.min.js')->end()
                ->scalarNode('default_config')->defaultValue(null)->end()
                ->append($this->createExtensions())
                ->append($this->createDependencies())
                ->append($this->createConfigsNode())
            ->end();

        return $treeBuilder;
    }

    private function createExtensions()
    {
        return $this->createNode('extensions')
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('editor_plugin_color_syntax')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('tui_code_color_syntax_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/chart/editor-plugin-color-syntax/dist/toastui-editor-plugin-color-syntax.js')->end()
                            ->scalarNode('tui_code_color_syntax_css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/chart/editor-plugin-color-syntax/dist/toastui-editor-plugin-color-syntax.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('editor_plugin_chart')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('tui_chart_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/chart/dist/toastui-chart.min.js')->end()
                            ->scalarNode('tui_chart_plugin_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/editor-plugin-chart/dist/toastui-editor-plugin-chart.js')->end()
                            ->scalarNode('tui_chart_css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/chart/dist/toastui-chart.min.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('editor_plugin_code_syntax_highlight')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('tui_code_syntax_highlight_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/editor-plugin-code-syntax-highlight/dist/toastui-editor-plugin-code-syntax-highlight.js')->end()
                            ->scalarNode('tui_code_syntax_highlight_css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/editor-plugin-code-syntax-highlight/dist/toastui-editor-plugin-code-syntax-highlight.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('editor_plugin_table_merged_cell')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('tui_table_merged_cell_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/editor-plugin-table-merged-cell/dist/toastui-editor-plugin-table-merged-cell.js')->end()
                            ->scalarNode('tui_table_merged_cell_css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/editor-plugin-table-merged-cell/dist/toastui-editor-plugin-table-merged-cell.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('editor_plugin_uml')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('tui_uml_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/@toast-ui/editor-plugin-uml/dist/toastui-editor-plugin-uml.js')->end()
                        ->end()
                    ->end()
                ->end();
    }

    private function createDependencies()
    {
        return $this->createNode('dependencies')
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('dompurify')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/dompurify/dist/purify.min.js')->end()
                            ->scalarNode('module_js_path')->defaultValue(null)->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()

                    ->arrayNode('orderedmap')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue(null)->end()
                            ->scalarNode('module_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/orderedmap/dist/index.js')->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()
                    ->arrayNode('plantuml_encoder')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/plantuml-encoder/dist/plantuml-encoder.min.js')->end()
                            ->scalarNode('module_js_path')->defaultValue(null)->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()
                    ->arrayNode('plantuml_decoder')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/plantuml-encoder/dist/plantuml-decoder.min.js')->end()
                            ->scalarNode('module_js_path')->defaultValue(null)->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()
                    ->arrayNode('prismjs')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/prismjs/prism.js')->end()
                            ->scalarNode('module_js_path')->defaultValue(null)->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()
                    ->arrayNode('prosemirror_bundle')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue(null)->end()
                            ->scalarNode('module_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/prosemirror-bundle.js')->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
                        ->end()
                    ->end()
                    ->arrayNode('tui_color_picker')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/tui-color-picker/dist/tui-color-picker.min.js')->end()
                            ->scalarNode('module_js_path')->defaultValue(null)->end()
                            ->scalarNode('css_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/tui-color-picker/dist/tui-color-picker.min.css')->end()
                        ->end()
                    ->end()
                    ->arrayNode('w3c-keyname')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('js_path')->defaultValue(null)->end()
                            ->scalarNode('module_js_path')->defaultValue('bundles/teebbtuieditor/tui.editor-bundles/node_modules/w3c-keyname/index.js')->end()
                            ->scalarNode('css_path')->defaultValue(null)->end()
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