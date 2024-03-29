<?php

namespace Drupal\dlog_hero\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\dlog_hero\Plugin\DlogHero\DlogHeroPluginInterface;
use Drupal\dlog_hero\Plugin\DlogHeroEntityPluginManager;
use Drupal\dlog_hero\Plugin\DlogHeroPathPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a dlog hero block.
 *
 * @Block(
 *   id = "dlog_hero",
 *   admin_label = @Translation("Dlog Hero"),
 *   category = @Translation("Custom")
 * )
 */
class DlogHeroBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The plugin manager for dlog hero entity plugins.
   *
   * @var \Drupal\dlog_hero\Plugin\DlogHeroEntityPluginManager
   */
  protected DlogHeroEntityPluginManager $dlogHeroEntityManager;

  /**
   * The plugin manager for dlog hero path plugins.
   *
   * @var \Drupal\dlog_hero\Plugin\DlogHeroPathPluginManager
   */
  protected DlogHeroPathPluginManager $dlogHeroPathManager;

  /**
   * Constructs a new DlogHeroBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\dlog_hero\Plugin\DlogHeroEntityPluginManager $dlogHeroEntity
   *   The plugin manager for dlog hero entity plugins.
   * @param \Drupal\dlog_hero\Plugin\DlogHeroPathPluginManager $dlogHeroPath
   *   The plugin manager for dlog hero path plugins.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition,
                              DlogHeroEntityPluginManager $dlogHeroEntity,
                              DlogHeroPathPluginManager $dlogHeroPath) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->dlogHeroEntityManager = $dlogHeroEntity;
    $this->dlogHeroPathManager = $dlogHeroPath;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.dlog_hero.entity'),
      $container->get('plugin.manager.dlog_hero.path'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $entity_plugins = $this->dlogHeroEntityManager->getSuitablePlugins();
    $path_plugins = $this->dlogHeroPathManager->getSuitablePlugins();


    $plugins = $entity_plugins + $path_plugins;
    uasort($plugins, '\Drupal\Component\Utility\SortArray::sortByWeightElement');
    $plugin = end($plugins);

    if ($plugin['plugin_type'] == 'entity') {
      /** @var \Drupal\dlog_hero\Plugin\DlogHero\DlogHeroPluginInterface $instance */
      $instance = $this->dlogHeroEntityManager->createInstance($plugin['id'], ['entity' => $plugin['entity']]);
    }

    if ($plugin['plugin_type'] == 'path') {
      /** @var \Drupal\dlog_hero\Plugin\DlogHero\DlogHeroPluginInterface $instance */
      $instance = $this->dlogHeroPathManager->createInstance($plugin['id']);
    }

    $build['content'] = [
      '#theme' => 'dlog_hero',
      '#title' => $instance->getHeroTitle(),
      '#subtitle' => $instance->getHeroSubtitle(),
      '#image' => $instance->getHeroImage(),
      '#video' => $instance->getHeroVideo(),
      '#plugin_id' => $instance->getPluginId(),
    ];
    $build['#cache']['max-age'] = 0;
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return [
      'url.path',
    ];
  }

}
