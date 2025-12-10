# TeebbTuiEditorBundle
这个包集成Markdown编辑器tui.editor到您的symfony项目。 这个包的源代码是从[FOSCKEditorBundle](https://github.com/FriendsOfSymfony/FOSCKEditorBundle)修改而来.
感谢 FOSCKEditorBundle 的作者:[Eric Geleon](https://github.com/egeloen) 和 [FriendsOfSymfony Community](https://github.com/FriendsOfSymfony/FOSCKEditorBundle/graphs/contributors) , 你们的代码很酷. 感谢 MIT 开源协议.

![tui.editor](https://user-images.githubusercontent.com/1215767/34356204-4c03be8a-ea7f-11e7-9aa9-0d84f9e912ec.gif)


安装和使用
============

如果您的应用使用了Symfony Flex
----------------------------------

打开命令终端, 进入项目目录并运行如下命令:

```console
$ composer require teebbstudios/tuieditor-bundle
```

如果您的应用没有使用Symfony Flex
----------------------------------------

### 第1步: 下载TeebbTuiEditorBundle

打开命令终端, 进入项目目录运行如下命令下载最新版的TeebbTuiEditorBundle:

```console
$ composer require teebbstudios/tuieditor-bundle
```

这个命令要求已经安装composer并且添加环境变量到系统路径, 您可以查看Composer文档 [Composer 安装章节](https://getcomposer.org/doc/00-intro.md)
或者自行使用搜索引擎查阅相关文档.

### 第2步: 启用TeebbTuiEditorBundle

修改您的应用的`app/AppKernel.php` 文件,把TeebbTuiEditorBundle注册到您的应用中,如下:

```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Teebb\TuiEditorBundle\TeebbTuiEditorBundle(),
        ];

        // ...
    }

    // ...
}
```

### 第3步: 下载tui.editor所有的资源

下载已经打包好的最新的[tui.editor-bundles](https://github.com/teebbstudios/tui.editor-bundles)到您的项目中。

```console 
$ php bin/console tuieditor:install
```

这一步会下载tui.editor所有的文件到TeebbTuiEditorBundle目录 `src/Resources/public`, 再运行命令：

```console
$ php bin/console assets:install --symlink
```

### 第4步: 配置TeebbTuiEditorBundle

您可以在 `config/packages` 目录中添加配置文件（只是一个示例配置, 但您可以完全使用如下配置）:
```yaml
#config/packages/teebb_tuieditor.yaml
teebb_tui_editor:
    #enable: true                                           # 是否启用tui.editor
    #jquery: true                                           # 是否使用jquery, 如果您的项目中使用过jquery,可以设置为false,避免重复引入jquery
    #jquery_path: ~                                         # 自定义jquery路径.
    #editor_js_path: ~                                      # 自定义editor js 路径
    #viewer_js_path: ~                                      # Custom tui.viewer js path.
    #editor_css_path: ~                                     # Custom tui.editor css path.
    #viewer_css_path: ~                                     # Custom tui.viewer css path.
    #editor_contents_css_path: ~                            # Custom content css path.
    #asset_repository: 'teebbstudios/tui.editor-bundles'    # Public assets installer repository
    # ...                                                   # 更多配置使用命令: bin/console debug:config teebb_tui_editor 查看
    
    default_config: basic_config
    #editor_options:
        #height: 'auto'                                     # Editor's height style value. Height is applied as border-box ex) '300px', '100%', 'auto'
        #initial_edit_type: 'wysiwyg'                       # Initial editor type (markdown, wysiwyg)
        #preview_style: 'vertical'                          # Markdown editor's preview style (tab, vertical)
        #theme: 'dark'                                      # override editor color scheme with dark theme
        #toolbar_items:
            #- ["heading"]
    #viewer_options:
        #height: 'auto'                                     # Viewer's height style value. Height is applied as border-box ex) '300px', '100%', 'auto'

    configs:
        basic_config:
            to_html: false                                  # Save to database use html syntax?
            editor_options:
                #height: 'auto'                             # Editor's height style value. Height is applied as border-box ex) '300px', '100%', 'auto'
                #initial_edit_type: 'wysiwyg'               # Initial editor type (markdown, wysiwyg)
                #preview_style: 'vertical'                  # Markdown editor's preview style (tab, vertical)
                #theme: 'dark'                              # override editor color scheme with dark theme
                #toolbar_items:
                    #- ["heading"]
            extensions:                                     # extensions must defined as array of plugin_name variable or {plugin_name, {plugin_options}}
                - chart                                     # chart default
                #- chart:                                   # chart custom options
                    #width: 'auto'                          # number|string	'auto'	Default width value
                    #height: 'auto'                         # number|string	'auto'	Default height value
                    #minWidth: 0                            # number	0	Minimum width value
                    #maxWidth: 0                            # number	0	Minimum height value
                    #minHeight: Infinity                    # number    Infinity	Maximum width value
                    #maxHeight: Infinity                    # number	Infinity	Maximum height value
                - codeSyntaxHighlight
                - colorSyntax                               # colorSyntax default
                #- colorSyntax:                             # colorSyntax custom options
                    #preset: ['#181818', '#292929']     # [required] preset	Array.<string>		Preset for color palette
                - tableMergedCell
                - uml                                       # uml default
                #- uml:                                     # uml custom options
                    #rendererURL: ~                         # [required]string	'http://www.plantuml.com/plantuml/png/'	URL of plant uml renderer
            dependencies:
                editor_dark_theme:                          # Must include if using 'dark' theme
                    js_path:
                    css_path: /bundles/teebbtuieditor/tui.editor-bundles/css/toastui-editor-dark.css

```

> [!CAUTION]
> asset_repository config is the GitHub repository that will be used for the `php bin/console tuieditor:install` command, the script will look for the latest release and download all files into the TeebbTuiEditorBundle `src/Resources/public` folder. Use only trusted repository for this bundle.

您可以修改tui.editor的语言显示。
```yaml
#config/services.yaml

parameters:
    locale: 'zh_CN'                   # Change the locale

```

### 第5步: 使用TeebbTuiEditorBundle

首先, 添加tui.editor的依赖js库到您页面的顶部。您可以使用twig 方法 `tuieditor_dependencies`, 如下:

```twig
{{ tuieditor_dependencies() }}
```

再在您的表单对应字段中使用TeebbTuiEditorBundle预先定义好的类型`TuiEditorType`, 如下:

```php
use Teebb\TuiEditorBundle\Form\Type\TuiEditorType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ...
            ->add('body', TuiEditorType::class)
        ;
    }

    // ...
} 
```

### 第6步: 解析渲染Markdown内容

如果您在数据库中保存的是Markdown内容,那么您在前台显示的时候可以使用预定义好的twig函数 `tuieditor_viewer_widget`进行解析。
第一个参数id是 div 标签的DOM id.
第二个参数content是 twig 变量, 从数据库中读取到的Markdown内容.

提示: 别忘了在页面顶部添加tui.editor的依赖js库！

```twig
<div id="id"></div>
{{ tuieditor_viewer_widget("id", content) }}
```

### 第7步: 完成!!

好了, 在您的Controller代码中使用对应的表单, 再刷新页面, 一个很清新的Markdown编辑器就成功集成了。
您可以发挥您的灵感进行创作了！ 

:)
