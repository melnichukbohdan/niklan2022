<?php

namespace Drupal\dlog\Plugin\DlogHero\Entity;

use Drupal\dlog_hero\Plugin\DlogHero\Entity\DlogHeroEntityPluginBase;

/**
 * Hero bloc for blog_article node type
 *
 * @DlogHeroEntity(
 *  id = "dlog_node_blog_article"
 *  entity_type = "node"
 *  entity_bundle = {"bloc_article"}
 * )
 */
class NodeBlogArticle extends DlogHeroEntityPluginBase {

  public function getHeroSubtitle() {
    /** @var  \Drupal\node\NodeInterface $node */
    $node = $this->getEntity();
    return $node->get('body')->value;
  }
}
