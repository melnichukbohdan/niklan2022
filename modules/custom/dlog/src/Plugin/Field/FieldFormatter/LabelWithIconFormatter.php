<?php

namespace Drupal\dlog\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\File\FileUrlGenerator;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\UrlGenerator;
use Drupal\Core\TypedData\Plugin\DataType\Uri;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;


/**
 * Plugin implementation of the 'Label with icon' formatter.
 *
 * @FieldFormatter(
 *   id = "dlog_label_with_icon",
 *   label = @Translation("Label with icon"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class LabelWithIconFormatter extends EntityReferenceFormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager services
   * @var Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private EntityTypeManagerInterface $entityTypeManager;

  public function __construct($plugin_id,
                              $plugin_definition,
                              FieldDefinitionInterface $field_definition,
                              array $settings,
                              $label,
                              $view_mode,
                              array $third_party_settings,
                              EntityTypeManagerInterface $entityTypeManager,
                              FileUrlGeneratorInterface $fileUrlGenerator,
                              ) {

    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->entityTypeManager = $entityTypeManager;
    $this->fileUrlGenerator = $fileUrlGenerator;
  }

  /**
   * @param ContainerInterface $container
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @return FormatterBase|LabelWithIconFormatter|void
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('file_url_generator'),
    );
  }

  /**
   * @inerhitDoc
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return ($field_definition->getFieldStorageDefinition()->getSetting('target_type') == 'media');
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      /**@var \Drupal\media\MediaInterface $entity*/
      $entity = $this->entityTypeManager->getStorage('media')->load($item->target_id);
      $element[$delta] = [
        '#theme' => 'dlog_label_with_icon_media_formatter',
        '#url' => $this->getMediaUrl($entity),
        '#label' => $entity->label(),
        '#filesize' => $this->getMediaFilesSize($entity),
        '#media_type' => $entity->bundle(),
        '#mime_type' => $this->getMediaMimeType($entity),
      ];
    }

    return $element;
  }



  /**
   * Get media url
   *
   * @param MediaInterface $entity
   *  The entity where to look for url
   *
   * @return string|null
   *  The URL to media or NULL not found
   */
  protected function getMediaUrl (MediaInterface $entity) {
    switch ($entity->bundle()) {
      case 'audio':
        return $this->gerFileUrlFromField($entity, 'field_media_audio_file');
      case 'document':
        return $this->gerFileUrlFromField($entity, 'field_media_document');
      case 'image':
        return $this->gerFileUrlFromField($entity, 'field_media_image');
      case 'remote_video':
        return $entity->get('field_media_oembed_video')->value;
      case 'video':
        return $this->gerFileUrlFromField($entity, 'field_media_video_file');
    }
    return;
  }


  /**
   * Get media size
   *
   * @param MediaInterface $entity
   *  The media entity.
   *
   * @return string|null
   *  The formatted the file size if found or null otherwise
   */
  protected function getMediaFilesSize (MediaInterface $entity) {
    switch ($entity->bundle()) {
      case 'audio':
        return $this->gerFileSizeFromField($entity, 'field_media_audio_file');
      case 'document':
        return $this->gerFileSizeFromField($entity, 'field_media_document');
      case 'image':
        return $this->gerFileSizeFromField($entity, 'field_media_image');
      case 'video':
        return $this->gerFileSizeFromField($entity, 'field_media_video_file');
    }
    return;
  }

  /**
   * Get mime type
   *
   * @param MediaInterface $entity
   *  The media entity
   *
   * @return string
   *  The file mime type.
   */
  protected function getMediaMimeType (MediaInterface $entity) {
    switch ($entity->bundle()) {
      case 'audio':
        return $this->gerFileMimeFromField($entity, 'field_media_audio_file');
      case 'document':
        return $this->gerFileMimeFromField($entity, 'field_media_document');
      case 'image':
        return $this->gerFileMimeFromField($entity, 'field_media_image');
      case 'remote_video':
        return 'video/x-wmv';
      case 'video':
        return $this->gerFileMimeFromField($entity, 'field_media_video_file');
      default : 'application/octet-stream';
    }
  }

  /**
   * Get file url from fields file
   *
   * @param \Drupal\media\MediaInterface $entity
   *  The entity with field.
   * @param string $fieldName
   *  The field name.
   *
   * @return string
   *  The file URL.
   */
  protected function gerFileUrlFromField (MediaInterface $entity, $fieldName) {
    /**@var FileInterface $file_entity */
    $file_entity = $entity->get($fieldName)->entity;
    $file_uri = $file_entity->getFileUri();
    return $this->fileUrlGenerator->generateAbsoluteString($file_uri);
  }

  /**
   * Get file size from fields file
   *
   * @param \Drupal\media\MediaInterface $entity
   *  The entity with field.
   * @param string $fieldName
   *  The field name.
   *
   * @return string
   *  The file size.
   */
  protected function gerFileSizeFromField (MediaInterface $entity, $fieldName) {
    /**@var FileInterface $file_entity */
    $file_entity = $entity->get($fieldName)->entity;

    return format_size($file_entity->getSize());
  }

  /**
   * Get file mime from fields file
   *
   * @param \Drupal\media\MediaInterface $entity
   *  The entity with field.
   * @param string $fieldName
   *  The field name.
   *
   * @return string
   *  The file size.
   */
  protected function gerFileMimeFromField (MediaInterface $entity, $fieldName) {
    /**@var FileInterface $file_entity */
    $file_entity = $entity->get($fieldName)->entity;

    return $file_entity->getMimeType();
  }
}
