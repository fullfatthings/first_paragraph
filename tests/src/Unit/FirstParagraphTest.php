<?php

namespace Drupal\Tests\first_paragraph\Unit;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\filter\Entity\FilterFormat;

/**
 * Tests the First Paragraph formatter functionality.
 *
 * @group text
 */
class FirstParagraphTest extends EntityKernelTestBase {


  /**
   * The entity type used in this test.
   *
   * @var string
   */
  protected $entityType = 'entity_test';

  /**
   * The bundle used in this test.
   *
   * @var string
   */
  protected $bundle = 'entity_test';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['first_paragraph'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['field']);
    $this->installEntitySchema('entity_test');

    $this->entityTypeManager->getStorage('filter_format')->create([
      'format' => 'my_text_format',
      'name' => 'My text format',
      'filters' => [
        'filter_autop' => [
          'module' => 'filter',
          'status' => TRUE,
        ],
      ],
    ])->save();

    $this->entityTypeManager->getStorage('field_storage_config')->create([
      'field_name' => 'formatted_text',
      'entity_type' => $this->entityType,
      'type' => 'text',
    ])->save();

    $this->entityTypeManager->getStorage('field_config')->create([
      'entity_type' => $this->entityType,
      'bundle' => $this->bundle,
      'field_name' => 'formatted_text',
      'label' => 'Filtered text',
    ])->save();
  }

  /**
   * Tests all text field formatters.
   */
  public function testFormatters() {
    $strings = [
      "This is a first paragraph.\n\nThis is the second paragraph.\n\nThis is the third paragraph." => "<p>This is a first paragraph.</p>\n",
      "<p>First</p><p>Second</p>" => "<p>First</p>\n",
      "First\nSecond" => "<p>First<br><br />\nSecond</p>\n",
      'test' => "<p>test</p>\n",
      '<ul><li>Bullet</li></ul><p>First Para</p>' => "<p>First Para</p>\n",
    ];

    // Create the entity to be referenced.
    $entity = $this->entityTypeManager
      ->getStorage($this->entityType)
      ->create([
        'name' => $this->randomMachineName(),
      ]);

    foreach ($strings as $input => $output) {
      $entity->formatted_text = [
        'value' => $input,
        'format' => 'my_text_format',
      ];
      $entity->save();

      // Verify the text field formatter's render array.
      $build = $entity->get('formatted_text')
        ->view(['type' => 'text_first_para']);
      \Drupal::service('renderer')->renderRoot($build[0]);
      $this->assertSame((string) $build[0]['#markup'], $output);

      // Check the cache tags.
      $this->assertEquals(
        $build[0]['#cache']['tags'],
        FilterFormat::load('my_text_format')->getCacheTags(),
        new FormattableMarkup('The @formatter formatter has the expected cache tags when formatting a formatted text field.', ['@formatter' => 'text_first_para'])
      );
    }
  }

}
