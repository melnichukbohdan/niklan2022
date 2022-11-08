<?php

namespace Drupal\dlog_blog\Service;

use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Class for blog lazy bilders.
 *
 * @package Drupal\dlog_blog\Service
 */
class DlogLazyBuilder implements DlogLazyBuilderInterface, TrustedCallbackInterface {

  /**
   * @inheritdoc
   */
  public static function trustedCallbacks() {
    return ['randomBlogPosts'];
  }

  /**
   * @inheritdoc
   */
  public static function randomBlogPosts() {
    return $build['random_posts'] = [
      '#theme' => 'dlog_blog_random_posts',
    ];
  }
}
