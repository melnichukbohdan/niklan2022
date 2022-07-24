<?php

namespace Drupal\dlog_paragraphs\Plugin\paragraphs\Behavior;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\ParagraphsBehaviorBase;

/**
 * @ParagraphsBehavior (
 *   id = "dlog_paragraphs_paragraph_title",
 *   label = @Translation("Paragraphs title settings"),
 *   description = @Translation("Allow to configure paragraph title bihavior"),
 *   weight = 0,
 * )
 */

class ParagraphTitleBehavior extends ParagraphsBehaviorBase {

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
  }

  /**
   * {@inheritDoc}
   */
  public function preprocess(&$variables) {
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph  = $variables['paragraph'];
    $variables['title_wrapper'] = $paragraph
     ->getBehaviorSetting($this->getPluginId(),'title_wrapper', 'h2');
  }

  /**
   * {@inheritDoc}
   */
  public function buildBehaviorForm(ParagraphInterface $paragraph,
                                    array &$form,
                                    FormStateInterface $form_state) {
    $form['title_wrapper'] = [
      '#type' => 'select',
      '#title' => $this->t('Title wrapper element'),
      '#options' => $this->getHeadingOptions(),
      '#default_value' => $paragraph->getBehaviorSetting($this->getPluginId(),
        'title_wrapper', 'h2'),
    ];

    return $form;
  }

  /**
   * Defines list of available for title element
   */
  public function getHeadingOptions () {
    return [
      'h1' => 'h1',
      'h2' => 'h2',
      'h3' => 'h3',
      'h4' => 'h4',
      'div' => 'div',
    ];
  }

}
