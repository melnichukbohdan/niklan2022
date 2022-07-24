<?php

namespace Drupal\dlog_paragraphs\Plugin\paragraphs\Behavior;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\ParagraphsBehaviorBase;
use Drupal\Component\Utility\Html;

/**
 * @ParagraphsBehavior (
 *   id = "dlog_paragraphs_gallery",
 *   label = @Translation("Gallery settings"),
 *   description = @Translation("Settings for gallery paragraphs type"),
 *   weight = 0,
 * )
 */

class GalleryBehavior extends ParagraphsBehaviorBase {

   /**
   * {@inheritDoc}
   */
  public static function isApplicable(ParagraphsType $paragraphs_type)  {
    return $paragraphs_type->id() == 'gallery';
  }

  /**
   * @param array $build
   * @param Paragraph $paragraph
   * @param EntityViewDisplayInterface $display
   * @param string $view_mode
   */
  public function view(array &$build,
                       Paragraph $paragraph,
                       EntityViewDisplayInterface $display,
                       $view_mode)  {
    $images_per_row = $paragraph->getBehaviorSetting($this->getPluginId(),
      'items_per_row' ,3);

    //generating class name for bundle 'gallery'
    $bem_block = 'paragraph-' . $paragraph->bundle() .
      ($view_mode == 'default' ? '' : '-' . $view_mode);
    $build['#attributes']['class'][] =
      Html::getClass($bem_block  . '--images-per-row-' . $images_per_row);
  }

  /**
   * {@inheritDoc}
   */
  public function buildBehaviorForm(ParagraphInterface $paragraph,
                                    array &$form,
                                    FormStateInterface $form_state) {
    $form['items_per_row'] = [
      '#type' => 'select',
      '#title' => $this->t('Number of images per row'),
      '#options' => [
        '2' => $this->formatPlural('2', '1 photo per row',
          '@count photos per row'),
        '3' => $this->formatPlural('3', '1 photo per row',
          '@count photos per row'),
        '4' => $this->formatPlural('4', '1 photo per row',
          '@count photos per row'),
      ],
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(),
        'items_per_row' ,3),
    ];

    return $form;
  }

}
