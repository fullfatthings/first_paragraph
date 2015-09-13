<?php /**
 * @file
 * Contains \Drupal\first_paragraph\Plugin\Field\FieldFormatter\TextFirstPara.
 */

namespace Drupal\first_paragraph\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Component\Utility\Html;


/**
 * @FieldFormatter(
 *  id = "text_first_para",
 *  label = @Translation("First Paragraph"),
 *  field_types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary"
 *  }
 * )
 */
class TextFirstPara extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();

    foreach ($items as $delta => $item) {
      $first_para = Html::load($item->value)
        ->getElementsByTagName('p')
        ->item(0)
        ->cloneNode(TRUE);

      $newdom = new \DOMDocument();
      $newdom->appendChild($newdom->importNode($first_para, TRUE));

      $elements[$delta] = array('#markup' => $newdom->saveHTML());
    }

    return $elements;
  }

}
