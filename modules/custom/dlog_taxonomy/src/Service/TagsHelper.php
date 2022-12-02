<?php

namespace Drupal\dlog_taxonomy\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;
use Drupal\node\NodeStorageInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\taxonomy\TermStorageInterface;

/**
 * Class with helpers for taxonomy vocabulary tags.
 *
 * @package Drupal\dlog_taxonomy\Service
 */
class TagsHelper implements TagsHelperInterface {
  /**
   * The term storage
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  protected $termSrorage;

  /**
   * The mode storage
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *  The Entity Type Manager
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->termSrorage = $entityTypeManager->getStorage('taxonomy_term');
    $this->nodeStorage = $entityTypeManager->getStorage('node');
  }


  /**
   * {@inheritdoc }
   */
  public function getPromoUri($tid) {
    /**@var \Drupal\Taxonomy\TermInterface $term */
    $term = $this->termSrorage->load($tid);

    if ($term instanceof TermInterface && $term->bundle() == 'tags') {
      if ($term->get('field_image')->isEmpty()) {
        $lastBlogArticleResult = $this->nodeStorage->getQuery()
          ->condition('status', NodeInterface::PUBLISHED)
          ->condition('type', 'blog_article')
          ->condition('field_tags', $tid, 'IN')
          ->sort('created', 'DESC')
          ->range(0, 1)
          ->execute();

        if ($lastBlogArticleResult) {
          /**@var \Drupal\Node\NodeInterface $lastBlogArticle */
          $lastBlogArticle = $this->nodeStorage
            ->load(array_shift($lastBlogArticleResult));

          if ($lastBlogArticle->hasField('field_image') && !$lastBlogArticle->get('field_image')->isEmpty()) {
            /**@var MediaInterface $media */
            $media = $lastBlogArticle->get('field_image')->entity;
            /**@var \Drupal\file\FileInterface $file */
            $file = $media->get('field_media_image')->entity;

            return $file->getFileUri();
          }
        }
      } else {
        /**@var MediaInterface $media */
        $media = $term->get('field_image')->entity;
        /**@var \Drupal\file\FileInterface $file */
        $file = $media->get('field_media_image')->entity;

        return $file->getFileUri();
      }
    }
  }

}
