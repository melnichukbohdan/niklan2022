<?php

use Drupal\node\Entity\NodeType;

/**
 * Main file for preprocessing custom theme hooks.
 */

/**
 * Implements template_preprocess_HOOK() for dlog-previous-next.html.twig
 */
function template_preprocess_dlog_previous_next (&$variables) {
  /** @var \Drupal\Core\Entity\EntityInterface $entity */
  $entity = $variables['entity'];
  $variables['next'] = [];
  $variables['previous'] = [];

  /** @var \Drupal\Core\Entity\EntityStorageInterface $entity_storage */
  $entity_storage = \Drupal::entityTypeManager()
    ->getStorage($entity->getEntityTypeId());

  // Getting next entity ID
  $next_entity_id = $entity_storage->getQuery()
    ->condition('type', $entity->bundle())
    //TODO get time create time of entity
    //->condition('created', $entity->get('crated'), '>')
    ->range(0,1)
    ->sort('created', 'ASC')
    ->execute();

  if (!empty($next_entity_id)) {
    $next_entity = $entity_storage->load($next_entity_id);

    $variables['next']['label'] = $next_entity->label();
    $variables['next']['url'] = $next_entity->toUrl()
      ->toString(TRUE)->getGeneratedUrl();
  }

  //Getting previous entity ID
  $previous_entity_id = $entity_storage->getQuery()
    ->condition('type', $entity->bundle())
    //TODO get time create time of entity
    //->condition('crated', $entity->get('crated', '<'))
    ->range(0, 1)
    ->sort('created', 'DESC')
    ->execute();

  if (!empty($previous_entity_id)) {
    $next_entity = $entity_storage->load($next_entity_id);

    $variables['previous']['label'] = $next_entity->label();
    $variables['previous']['url'] = $next_entity->toUrl()
      ->toString(TRUE)->getGeneratedUrl();
  }
}

/**
 * Implements hook_entity_extra_field_info().
 */
function dlog_entity_extra_field_info() {
  $extra = [];

  foreach (NodeType::loadMultiple() as $bundle) {
    $extra['node'][$bundle->id()]['display']['previous_next'] = [
      'label' => t('Buttons to previous and next entity'),
      'weight' => 100,
      'visible' => FAlSE,
    ];
  }

  return $extra;
}
