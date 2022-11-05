<?php

namespace Drupal\dlog_blog\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Simple helpers for dlog article.
 *
 * @package Drupal\dlog_blog\Service
 */

class BlogManager implements BlogManagerInterface {

  /**
   * The entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  /**
   * The node Storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * The node view builder.
   * @var \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  protected  $nodeViewBuilder;


  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->nodeStorage = $entityTypeManager->getStorage('node');
    $this->nodeViewBuilder = $entityTypeManager->getViewBuilder('node');
  }

  /**
   * @inheritdoc
   */
  public function getRelatedPostsWithExactTags(NodeInterface $node, $limit = 2) {
    $result = &drupal_static(this::class  . __METHOD__  . $node->id() . $limit);
    if (!isset($result)) {
      if ($node->hasField('field_tags') && !$node->get('field_tags')->isEmpty()) {
        $query = $this->nodeStorage->getQuery()
          ->condition('status', NodeInterface::PUBLISHED)
          ->condition('type' , 'blog_article')
          ->condition('nid', $node->id(), '<>')
          ->range(0, $limit)
          ->addTag('entity_query_random');

        foreach ($node->get('field_tags')->getValue() as $fieldTags) {
          $and = $query->andConditionGroup();
          $and->condition('field_tags', $fieldTags['target_id']);
          $query->condition($and);
        }

        $result = $query->execute();
      }else {
        return $result = [];
      }
    }

    return $result;
  }

  /**
   * @inheritdoc
   */
  public function getRelatedPostsWithSameTags (NodeInterface $node, array $excludeIds = [],$limit = 2) {
    $result = &drupal_static(this::class . __METHOD__ . $node->id() . $limit);
    if (!isset($result)) {
      if ($node->hasField('field_tags') && !$node->get('field_tags')->isEmpty()) {
        $fieldTagsIds = [];
        foreach ($node->get('field_tags')->getValue() as $fieldTags) {
          $fieldTagsIds[] = $fieldTags['target_id'];
        }
        $query = $this->nodeStorage->getQuery()
          ->condition('status', NodeInterface::PUBLISHED)
          ->condition('type' , 'blog_article')
          ->condition('field_tags', $fieldTagsIds , "IN")
          ->range(0 , $limit)
          ->addTag('entity_query_random');
          if (!empty($excludeIds)) {
            $query->condition('nid', $excludeIds, 'NOT IN');
          }

        $result = $query->execute();
      }else {
        return $result = [];
      }
    }

    return $result;
  }

  /**
   * @inheritdoc
   */
  public function getRandomPosts ($excludeIds, $limit = 2) {
    $query = $this->nodeStorage->getQuery()
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('type' , 'blog_article')
      ->range(0 , $limit)
      ->addTag('entity_query_random');

    if (!empty($excludeIds)) {
      $query->condition('nid', $excludeIds, 'NOT IN');
    }

    return $query->execute();
  }

  /**
   * @inheritdoc
   */
  public function getRelatedPosts (NodeInterface $node, $max = 4, $exactTags = 2) {
    $result = &drupal_static(this::class . __METHOD__ .  $node->id() . $max . $exactTags);
    if (!isset($result)) {
      if ($exactTags > $max) {
        $exactTags = $max;
      }

      $counter = 0;
      $result = [];
      if ($exactTags > 0) {
        $exactSame = $this->getRelatedPostsWithExactTags($node, $exactTags);
        $result += $exactSame;
        $counter += count($exactSame);
      }


      if ($counter < $max) {
        $excludeIds = [];
        if (!empty($exactSame)) {
          $currentNode = $node->id();
          $excludeIds['current_node'] = $currentNode;
          $excludeIds += $exactSame;
        }

        $sameTags = $this->getRelatedPostsWithSameTags($node , $excludeIds, ($max - $counter));
        $result += $sameTags;
        $counter += count($sameTags);
      }

      if ($counter < $max) {
        if (!empty($sameTags)) {
          $excludeIds += $sameTags;
        }

        $random = $this->getRandomPosts($excludeIds, ($max - $counter));
        $result +=$random;
      }
    }

    return $result;
  }
}
