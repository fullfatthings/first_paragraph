<?php /**
 * @file
 * Contains \Drupal\first_paragraph\Plugin\Field\FieldFormatter\TextFirstPara.
 */

namespace Drupal\first_paragraph\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;

/**
 * @FieldFormatter(
 *  id = "text_first_para",
 *  label = @Translation("First Paragraph"),
 *  field_types = {"text&quot;, &quot;text_long&quot;, &quot;text_with_summary"}
 * )
 */
class TextFirstPara extends FormatterBase {

  /**
   * @FIXME
   * Move all logic relating to the text_first_para formatter into this
   * class. For more information, see:
   *
   * https://www.drupal.org/node/1805846
   * https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Field%21FormatterInterface.php/interface/FormatterInterface/8
   * https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Field%21FormatterBase.php/class/FormatterBase/8
   */

}
