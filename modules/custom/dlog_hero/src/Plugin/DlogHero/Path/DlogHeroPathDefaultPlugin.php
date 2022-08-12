<?php

namespace Drupal\dlog_hero\Plugin\DlogHero\Path;

/**
 * Default plugin witch will be used if not of met their requirement
 *
 * @DlogHeroPath(
 *   id = "dlog_hero_path_default",
 *   match_path={"*"},
 *   weight = -100,
 * )
 */
class DlogHeroPathDefaultPlugin extends DlogHeroPathPluginBase {

}
