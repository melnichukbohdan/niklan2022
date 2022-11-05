<?php

namespace Drupal\dlog_blog\Service;

use Drupal\node\NodeInterface;

/**
 * Simple helpers for dlog article.
 *
 * @package Drupal\dlog_blog\Service
 */

interface BlogManagerInterface {

  /**
   *  Get related blog posts with exact same tags
   *
   * @param NodeInterface $node
   *  The node object for which search related posts.
   * @param int $limit
   *  The max limit of related posts.
   *
   * @return array
   *  The related entity ids
   */
  public function getRelatedPostsWithExactTags (NodeInterface $node, int $limit);

  /**
   *  Get related blog posts with same tags (one of them exists).
   *
   * @param NodeInterface $node
   *  The node object for which search related posts.
   * @param array $excludeIds
   *  The array with node ids which must be excluded.
   * @param int $limit
   *  The max limit of related posts.
   *
   * @return array
   *  The related entity ids
   */
  public function getRelatedPostsWithSameTags (NodeInterface $node, array $excludeIds, int $limit = 2);


  /**
   * Get random blog posts.
   *
   * @param array $excludeIds
   *  The array with node ids which must be excluded.
   * @param int $limit
   *  The max limit of related posts.
   *
   * @return array
   *  The related entity ids
   */
  public function getRandomPosts (array $excludeIds, int $limit = 2);

  /**
   * Get related posts
   * @param NodeInterface $node
   *  The node for which related posts is looking for.
   * @param int $max
   *  The max related post trying to find.
   * @param int $exactTags
   *  The max related post trying to find with exact same tags.   *
   *
   * @return array
   *  The related entity ids
   */
  public function getRelatedPosts (NodeInterface $node, int $max, int $exactTags);
}
