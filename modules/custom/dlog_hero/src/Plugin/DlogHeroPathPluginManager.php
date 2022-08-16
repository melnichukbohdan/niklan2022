<?php

namespace Drupal\dlog_hero\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Path\PathMatcher;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Factory\ContainerFactory;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * DlogHero plugin manager
 */
class DlogHeroPathPluginManager extends DefaultPluginManager {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $pathCurrent;

  /**
   *  The path current.
   *
   * @var \Drupal\Core\Path\PathMatcher
   */
  protected $pathMatcher;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected CurrentRouteMatch $currentRouteMatch;

  /**
   * DlogHeroPluginManager constructor.
   *
   * @param \Traversable $namespaces
   *   The namespaces.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cache backend.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Path\CurrentPathStack $pathCurrent
   *    The path current.
   * @param \Drupal\Core\Path\PathMatcher $pathMatcher
   *    The path matcher.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $currentRouteMatch
   *   The current route match.
   */
  public function __construct(\Traversable $namespaces,
                              CacheBackendInterface $cacheBackend,
                              ModuleHandlerInterface $moduleHandler,
                              CurrentPathStack $pathCurrent,
                              PathMatcher $pathMatcher,
                              CurrentRouteMatch $currentRouteMatch) {

   $this->pathCurrent = $pathCurrent;
   $this->pathMatcher = $pathMatcher;
   $this->currentRouteMatch = $currentRouteMatch;

  $subdir = "Plugin/DlogHero/Path";
  $plugin_interface = "Drupal\dlog_hero\Plugin\DlogHero\Path\DlogHeroPathPluginInterface";
  $plugin_definition_annotation_name = "Drupal\dlog_hero\Annotation\DlogHeroPath";

    $this->defaults += [
      'plugin_type' => 'path',
      'enabled' => TRUE,
      'weight' => 0,
      'match_type' => 'listed'
    ];

    parent::__construct($subdir, $namespaces, $moduleHandler, $plugin_interface, $plugin_definition_annotation_name);

    //Call hook_dlog_hero_TYPE_alter
    $this->alterInfo("dlog_hero_path");
    $this->setCacheBackend($cacheBackend, "dlog_hero:path");
    $this->factory = new ContainerFactory($this->getDiscovery());
  }

  /**
   * Gets suitable plugins for current request.
   */
  public function getSuitablePlugins() {
    $plugin_type = $this->defaults['plugin_type'];

    if ($plugin_type == 'path') {
      return $this->getSuitablePathPlugins();
    }
  }

  /**
   * Gets dlog hero path plugins suitable for current request.
   */
  protected function getSuitablePathPlugins() {
    $plugins = [];

      $definitions = $this->getDefinitions();
    foreach ($definitions as $pluginId => $plugin) {
      if ($plugin['enabled']) {

        $pattern = implode(PHP_EOL, $plugin['match_path']);
        $currentPath = $this->pathCurrent->getPath();
        $isMatchPathMatch = $this->pathMatcher->matchPath($currentPath, $pattern);

        switch ($plugin['match_path']) {
          case 'listed':
          default:
            $matchType = 0;
            break;

          case 'unlisted':
            $matchType = 1;
            break;
        }

        $isPluginNeeded = ($isMatchPathMatch xor $matchType);

        if ($isPluginNeeded) {
          $plugins[$pluginId] = $plugin;
        }
      }
    }

    uasort($plugins, '\Drupal\Component\Utility\SortArray::sortByWeightElement');
    return $plugins;
  }
}
