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
 *   id = "dlog_paragraphs_paragraph_style",
 *   label = @Translation("Paragraph_style"),
 *   description = @Translation("Allow to select custom class for paragraphs"),
 *   weight = 0,
 * )
 */

class ParagraphStyleBehavior extends ParagraphsBehaviorBase {

   /**
   * {@inheritDoc}
   */
  public static function isApplicable(ParagraphsType $paragraphs_type)  {
    return TRUE;
  }

  /**
   * Extends the paragraph render array with behavior
   * @param array $build
   * @param Paragraph $paragraph
   * @param EntityViewDisplayInterface $display
   * @param string $view_mode
   */
  public function view(array &$build,
                       Paragraph $paragraph,
                       EntityViewDisplayInterface $display,
                       $view_mode) {
    $bem_block = 'paragraph-style';
    $selected_styles =
      $paragraph->getBehaviorSetting($this->getPluginId(), 'styles', []);

    foreach ($selected_styles as $style) {
      $build['#attributes']['class'][] =
        Html::getClass($bem_block . '--' . $style);
    }

  }

  /**
   * {@inheritDoc}
   */
  public function buildBehaviorForm(ParagraphInterface $paragraph,
                                    array &$form,
                                    FormStateInterface $form_state) {
    $form['style_wrapper'] = [
      '#type' => 'details',
      '#title' => $this->t('Paragraphs styles'),
      '#open' => FALSE,
    ];

    $styles = $this->getStyle($paragraph);
    $selected_styles = $paragraph->getBehaviorSetting($this->getPluginId(),
      'styles' ,[]);

    foreach ($styles as $group_id => $group) {
      $form['style_wrapper'][$group_id] = [
        '#type' => 'checkboxes',
        '#title' => $group['label'],
        '#options' => $group['options'],
        '#default_value' => $selected_styles,
      ];
    }

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitBehaviorForm(ParagraphInterface $paragraph,
                                     array &$form,
                                     FormStateInterface $form_state) {
    $styles = [];
    $filtered_value =
      $this->filterBehaviorFormSubmitValues($paragraph,$form, $form_state);

    if (isset($filtered_value['style_wrapper'])) {
      $style_groups = $filtered_value['style_wrapper'];

      foreach ($style_groups as $group) {
        foreach ($group as $style_name) {
          $styles[] = $style_name;
        }
      }
    }

    $paragraph->setBehaviorSettings($this->getPluginId(), ['styles' => $styles]);
  }

  /**
   * Return styles for paragraph.
   */

  public function getStyle (ParagraphInterface $paragraph) {
    $style = [];
    if ($paragraph->hasField('field_title')) {


      $style['title'] = [
        'label' => $this->t('Paragraphs title'),
        'options' => [
          'title_bold' => $this->t('Bold'),
          'title_centered' => $this->t('Centered'),
        ],
      ];

      $style['common'] = [
        'label' => $this->t('Paragraphs common style'),
        'options' => [
          'style_black' => $this->t('Style black'),
        ],
      ];
    }
    return $style;
  }

}
