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
 *   id = "dlog_paragraphs_paragraph_class",
 *   label = @Translation("Custom class for paragraphs"),
 *   description = @Translation("Allow to add custom class for paragraphs"),
 *   weight = 0,
 * )
 */

class ParagraphClassBehavior extends ParagraphsBehaviorBase {

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
    $classes_value = $paragraph->getBehaviorSetting($this->getPluginId(),
      'classes' ,'');
    $classes = explode(' ', $classes_value);

    foreach ($classes as $class) {
      $build['#attributes']['class'][] = Html::getClass($class);
    }
  }

  public function buildBehaviorForm(ParagraphInterface $paragraph,
                                    array &$form,
                                    FormStateInterface $form_state) {

    $form['classes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Classes'),
      '#description' => $this->t(
        'Add class separated by space.
              They will be processed via Html::getClass().'),
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(),
        'classes' ,''),
    ];

    return $form;
  }

}
