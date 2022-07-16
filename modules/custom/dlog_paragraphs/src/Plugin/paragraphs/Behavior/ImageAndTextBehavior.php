<?php

namespace Drupal\dlog_paragraphs\Plugin\paragraphs\Behavior;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\ParagraphsBehaviorBase;

/**
 * @ParagraphsBehavior (
 *   id = "dlog_paragraphs_image_and_text",
 *   label = @Translation("Image and text settings"),
 *   description = @Translation("Settings for paragraphs type"),
 *   weight = 0,
 * )
 */

class ImageAndTextBehavior extends ParagraphsBehaviorBase {

   /**
   * {@inheritDoc}
   */
  public static function isApplicable(ParagraphsType $paragraphs_type)  {
    return $paragraphs_type->id() == 'image_and_text';
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
    $image_size = $paragraph->getBehaviorSetting($this->getPluginId(),
      'image_size' ,'6_12');
    $image_position = $paragraph->getBehaviorSetting($this->getPluginId(),
      'image_position' ,'left');

    //generating class name for bundle 'image_and_text'
    $bem_block = 'paragraph-' . $paragraph->bundle() .
      ($view_mode == 'default' ? '' : '-' . $view_mode);

    $build['#attributes']['class'][] =
      Html::getClass($bem_block . '--image_size-' . $image_size);
    $build['#attributes']['class'][] =
      Html::getClass($bem_block . '--image_position-' . $image_position);

    if (isset($build['field_image']) &&
        $build['field_image']['#formatter'] == 'media_thumbnail') {
      switch ($image_size) {
        case '4_12' :
        default:
          $image_style = 'paragraphs_image_and_text_4_with_12';
        break;

        case '6_12' :
          $image_style = 'paragraphs_image_and_text_6_with_12';
          break;

        case '8_12' :
          $image_style = 'paragraphs_image_and_text_8_with_12';
          break;
      }

      $build['field_image'][0]['#image_style'] = $image_style;
    }
  }

  public function buildBehaviorForm(ParagraphInterface $paragraph,
                                    array &$form,
                                    FormStateInterface $form_state) {

    $form['image_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Image position'),
      '#options' => [
        'left' => $this->t('Left'),
        'right' => $this->t('Right'),
      ],
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(),
        'image_position' ,'left'),
    ];

    $form['image_size'] = [
      '#type' => 'select',
      '#title' => $this->t('Image size'),
      '#description' => $this->t('Size of the image in grid'),
      '#options' => [
        '4_12' => $this->t('4 colums with 12'),
        '6_12' => $this->t('6 colums with 12'),
        '8_12' => $this->t('8 colums with 12'),
      ],
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(),
        'image_size' ,'6_12'),
    ];

    return $form;
  }

}
