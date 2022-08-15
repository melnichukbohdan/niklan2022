<?php

namespace Drupal\dlog_hero\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Path\PathMatcher;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Factory\ContainerFactory;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\Container;

/**
 * DlogHero plugin manager
 */
class DlogHeroPluginManager extends DefaultPluginManager {

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
   * @params string $type
   *   The DlogHero plugin type.
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
  public function __construct($type,
                              \Traversable $namespaces,
                              CacheBackendInterface $cacheBackend,
                              ModuleHandlerInterface $moduleHandler,
                              CurrentPathStack $pathCurrent,
                              PathMatcher $pathMatcher,
                              CurrentRouteMatch $currentRouteMatch) {

   $this->pathCurrent = $pathCurrent;
   $this->pathMatcher = $pathMatcher;
   $this->currentRouteMatch = $currentRouteMatch;


    //E.g. entity => Entity, path => Path
  $typeCamelaized = Container::camelize($type);
  $subdir = "Plugin/DlogHero/{$typeCamelaized}";
 // $plugin_interface = "Drupal\dlog_hero\Plugin\DlogHero\{$typeCamelaized}\DlogHero{$typeCamelaized}PluginInterface";
  $plugin_interface = 'Drupal\dlog_hero\Plugin\DlogHero\\' . $typeCamelaized . "\DlogHero{$typeCamelaized}PluginInterface";
  $plugin_definition_annotation_name = "Drupal\dlog_hero\Annotation\DlogHero{$typeCamelaized}";

    $this->defaults += [
      'plugin_type' => $type,
      'enabled' => TRUE,
      'weight' => 0
    ];

    parent::__construct($subdir, $namespaces, $moduleHandler, $plugin_interface, $plugin_definition_annotation_name);

    if ($type = 'path') {
      $this->defaults += [
        'match_type' => 'listed',
      ];
    }

    //Call hook_dlog_hero_TYPE_alter
    $this->alterInfo("dlog_hero_{$type}");

    $this->setCacheBackend($cacheBackend, "dlog_hero:{$type}");

    $this->factory = new ContainerFactory($this->getDiscovery());
  }

  /**
   * Gets suitable plugins for current request.
   */
  public function getSuitablePlugins() {
    $plugin_type = $this->defaults['plugin_type'];

    if ($plugin_type == 'entity') {
      return $this->getSuitableEntityPlugins();
    }

    if ($plugin_type == 'path') {
      return $this->getSuitablePathPlugins();
    }
  }

  /**
   * Gets dlog hero entity plugins suitable for current request.
   */
  protected function getSuitableEntityPlugins() {
    $plugins = [];

    $entity = NULL;
    $parameters = $this->currentRouteMatch->getParameters();
    foreach ($parameters as $parameter) {
      if ($parameter instanceof EntityInterface) {
        $entity = $parameter;
        break;
      }
    }

    if ($entity) {
     $definitions = $this->getDefinitions();
      foreach ($definitions as $plugin_id => $plugin) {
        if ($plugin['enabled'] && $plugin['plugin_type'] == 'path') {
          break;
//       // if ($plugin['enabled']) {
//          $same_entity_type = $plugin['entity_type'] == $entity->getEntityTypeId();
//
//          //TODO need delete variable $entity_type
//
//         $entityBundle = $entity->bundle();
//         $eb =  $plugin['entity_bundle'];
//        $bundle1 = in_array($entityBundle , $eb);
//       //   $bundle1 = in_array( $entity->bundle() , $plugin['entity_bundle']);
//
//
//
//          $bundle2 = in_array('*', $plugin['entity_bundle']);
//
//          $needed_bundle = $bundle1 || $bundle2;
//
//        //  $needed_bundle = in_array( $entity->bundle() , $plugin['entity_bundle']) || in_array('*', $plugin['entity_bundle']);
//
//
//          if ($same_entity_type && $needed_bundle) {
//            $plugins[$plugin_id] = $plugin;
//            $plugins[$plugin_id]['entity'] = $entity;
//          }
        }

        if ($plugin['enabled'] && $plugin['plugin_type'] == 'entity') {
          $same_entity_type = $plugin['entity_type'] == $entity->getEntityTypeId();

          //TODO need delete variable $entity_type

          $entityBundle = $entity->bundle();
          $eb =  $plugin['entity_bundle'];
          $bundle1 = in_array($entityBundle , $eb);
          //   $bundle1 = in_array( $entity->bundle() , $plugin['entity_bundle']);



          $bundle2 = in_array('*', $plugin['entity_bundle']);

          $needed_bundle = $bundle1 || $bundle2;

          //  $needed_bundle = in_array( $entity->bundle() , $plugin['entity_bundle']) || in_array('*', $plugin['entity_bundle']);


          if ($same_entity_type && $needed_bundle) {
            $plugins[$plugin_id] = $plugin;
            $plugins[$plugin_id]['entity'] = $entity;
          }
        }



      }
    }

    uasort($plugins, '\Drupal\Component\Utility\SortArray::sortByWeightElement');
    return $plugins;
  }

  /**
   * Gets dlog hero path plugins suitable for current request.
   */
  protected function getSuitablePathPlugins() {
    $plugins = [];

      $definitions = $this->getDefinitions();
    foreach ($definitions as $pluginId => $plugin) {
      if ($plugin['enabled']) {
          if ($plugin['plugin_type'] == 'path') {
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
    }

    uasort($plugins, '\Drupal\Component\Utility\SortArray::sortByWeightElement');
    return $plugins;
  }
}
