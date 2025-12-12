<?php


namespace Teebb\TuiEditorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Teebb\TuiEditorBundle\Config\TuiEditorConfigurationInterface;


final class TuiEditorType extends AbstractType
{
    /**
     * @var TuiEditorConfigurationInterface
     */
    private $configuration;

    /**
     * @var string|null
     */
    private $locale;

    public function __construct(TuiEditorConfigurationInterface $configuration, ?string $locale)
    {
        $this->configuration = $configuration;
        $this->locale = $locale;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->setAttribute('enable', $options['enable']);

        if (!$options['enable']) {
            return;
        }
        $builder->setAttribute('base_path', $options['base_path']);
        $builder->setAttribute('locale', $options['locale']);
        $builder->setAttribute('jquery', $options['jquery']);
        $builder->setAttribute('editor', $options['editor']);
        $builder->setAttribute('viewer', $options['viewer']);
        $builder->setAttribute('config', $this->resolveConfig($options));
        $builder->setAttribute('config_name', $options['config_name']);
        $builder->setAttribute('extensions', $options['extensions']);
        $builder->setAttribute('dependencies', $options['dependencies']);
    }

    private function resolveConfig(array $options): array
    {
        $config = $options['config'];

        if (null === $options['config_name']) {
            $options['config_name'] = uniqid('teebb', true);
        } else {
            $config = array_merge($this->configuration->getConfig($options['config_name']), $config);
        }

        return $config;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $config = $form->getConfig();
        $view->vars['enable'] = $config->getAttribute('enable');

        if (!$view->vars['enable']) {
            return;
        }

        $view->vars['locale'] = $config->getAttribute('locale');
        $view->vars['base_path'] = $config->getAttribute('base_path');
        $view->vars['jquery'] = $config->getAttribute('jquery');
        $view->vars['editor'] = $config->getAttribute('editor');
        $view->vars['viewer'] = $config->getAttribute('viewer');
        $view->vars['config'] = $config->getAttribute('config');
        $view->vars['extensions'] = $config->getAttribute('extensions');
        $view->vars['dependencies'] = $config->getAttribute('dependencies');
        $view->vars['config_name'] = $config->getAttribute('config_name');

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'enable' => $this->configuration->isEnable(),
                'locale' => $this->locale,
                'base_path' => $this->configuration->getBasePath(),
                'jquery' => $this->configuration->getJquery(),
                'editor' => $this->configuration->getEditor(),
                'viewer' => $this->configuration->getViewer(),
                'config_name' => $this->configuration->getDefaultConfig(),
                'config' => $this->configuration->getConfigs(),
                'extensions' => $this->configuration->getExtensions(),
                'dependencies' => $this->configuration->getDependencies(),
            ])
            ->addAllowedTypes('enable', 'bool')
            ->addAllowedTypes('locale', ['string', 'null'])
            ->addAllowedTypes('config_name', ['string', 'null'])
            ->addAllowedTypes('base_path', 'string')
            ->addAllowedTypes('jquery', 'array')
            ->addAllowedTypes('editor',  'array')
            ->addAllowedTypes('viewer',  'array')
            ->addAllowedTypes('config', 'array')
            ->addAllowedTypes('extensions', 'array')
            ->addAllowedTypes('dependencies', 'array')
            ->setNormalizer('base_path', function (Options $options, $value) {
                if ('/' !== substr($value, -1)) {
                    $value .= '/';
                }

                return $value;
            });
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'tuieditor';
    }
}
