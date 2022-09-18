/**
 * @file
 * Paragraph code highlight behavior
 */

(function ($, Drupal) {

  Drupal.behavior.paparagraphCodeHighlight  = {
    attach: function (context, settings) {
      const elements = $(context).find('pre code').once('highlight')

      if (elements.length) {
        $.each(elements, (key, element) => {
          console.log(element);
        });
      }
    }
  };
})(jQuery, Drupal);



