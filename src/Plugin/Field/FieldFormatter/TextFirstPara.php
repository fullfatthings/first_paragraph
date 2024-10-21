<?php

namespace Drupal\first_paragraph\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Extracts the first paragraph HTML element out of the content.
 */
#[FieldFormatter(
  id: "text_first_para",
  label: new TranslatableMarkup("First Paragraph"),
  field_types: [
    'text',
    'text_long',
    'text_with_summary',
  ],
)]
class TextFirstPara extends FormatterBase {

  /**
   * A Drupal Renderer instance.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, RendererInterface $renderer) {
    $this->renderer = $renderer;

    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
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
      $container->get('renderer'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      if ($item->isEmpty()) {
        continue;
      }
      $renderable_item = [
        '#type' => 'processed_text',
        '#text' => $item->value,
        '#format' => $item->format,
        '#langcode' => $item->getLangcode(),
      ];

      $rendered = $this->renderer->renderRoot($renderable_item);

      $first_para = Html::load($rendered)->getElementsByTagName('p');

      if ($first_para->length > 0) {
        $new_dom = new \DOMDocument();
        $new_dom->appendChild($new_dom->importNode(
          $first_para->item(0)->cloneNode(TRUE), TRUE
        ));
        $text = $new_dom->saveHTML();

        $elements[$delta] = [
          '#type' => 'processed_text',
          '#text' => $text,
          '#format' => $item->format,
          '#langcode' => $item->getLangcode(),
        ];
      }
      else {
        $elements[$delta] = '';
      }
    }

    return $elements;
  }

}
