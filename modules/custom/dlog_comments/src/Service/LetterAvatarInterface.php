<?php

namespace Drupal\dlog_comments\Service;

/**
 * The class with colors
 *
 * @package Drupal\dlog_comments\Service
 */
interface LetterAvatarInterface {




  /**
   * Gets letters from user name
   * @param string $userName;
   *  The user name.
   *
   * @return string
   *  The user name letter.
   *
   * @see https://github.com/discourse/discourse/blob/main/lib/letter_avatar.rb#L6
   */
  public function getLetterFromUserName ($userName);

  /**
   * Gets color from user name
   * @param string $userName;
   *  The user name
   *
   * @return array
   *  An array with RGB color
   */
  public function fromUserName ($userName);

  /**
   * Gets text color by contrast using QIX formula.
   *
   * @param string|array $color
   *  The color which will be tested for contrast. Can be array with RGB color
   *  or HEX color.
   * @param $textColorDark
   *  The HEX color for dark text.
   * @param $textColorLight
   *  The HEX color for light text.
   * @return string
   *  The HEX color for dark or light text compared $color.
   *
   * @see https://en.wikipedia.org/wiki/YIQ
   */
  public function getTextColor ($color, $textColorDark = '#000000', $textColorLight = '#ffffff');

    /**
     * Get available colors
     *
     * @return array
     *  An array with RGB color.
     *
     * @see https://github.com/discourse/discourse/blob/main/lib/letter_avatar.rb#L6
     */
  public function getColor ();
}
