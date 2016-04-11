<?php

namespace cckakhandki\BehatHTMLFormatter;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class BehatHTMLFormatterExtension
 * @package Features\Formatter
 */
class BehatHTMLFormatterExtension implements ExtensionInterface {
  /**
   * You can modify the container here before it is dumped to PHP code.
   *
   * @param ContainerBuilder $container
   *
   * @api
   */
  public function process(ContainerBuilder $container) {
  }

  /**
   * Returns the extension config key.
   *
   * @return string
   */
  public function getConfigKey() {
    return "cckhtml";
  }

  /**
   * Initializes other extensions.
   *
   * This method is called immediately after all extensions are activated but
   * before any extension `configure()` method is called. This allows extensions
   * to hook into the configuration of other extensions providing such an
   * extension point.
   *
   * @param ExtensionManager $extensionManager
   */
  public function initialize(ExtensionManager $extensionManager) {
  }

  /**
   * Setups configuration for the extension.
   *
   * @param ArrayNodeDefinition $builder
   */
  public function configure(ArrayNodeDefinition $builder) {
    $builder->children()->scalarNode("name")->defaultValue("cckhtml");
    $builder->children()->scalarNode("renderer")->defaultValue("Twig");
    $builder->children()->scalarNode("file_name")->defaultValue("generated");
    $builder->children()->scalarNode("print_args")->defaultValue("false");
    $builder->children()->scalarNode("print_outp")->defaultValue("false");
    $builder->children()->scalarNode("loop_break")->defaultValue("false");
    $builder->children()->scalarNode("screenshot_folder")->defaultValue('Screenshots');
    $builder->children()->scalarNode('output')->defaultValue('.');
  }

  /**
   * Loads extension services into temporary container.
   *
   * @param ContainerBuilder $container
   * @param array $config
   */
  public function load(ContainerBuilder $container, array $config) {
    $definition = new Definition("cckakhandki\\BehatHTMLFormatter\\Formatter\\BehatHTMLFormatter");
    $definition->addArgument($config['name']);
    $definition->addArgument($config['renderer']);
    $definition->addArgument($config['file_name']);
    $definition->addArgument($config['print_args']);
    $definition->addArgument($config['print_outp']);
    $definition->addArgument($config['loop_break']);
    $definition->addArgument($config['screenshot_folder']);

    $definition->addArgument('%paths.base%');
    $container->setDefinition("html.formatter", $definition)
      ->addTag("output.formatter");
  }
}
