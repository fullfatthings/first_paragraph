<?php
namespace Drupal\first_paragraph\Tests;

/**
 * Test case.
 */
class FirstParagraphTestCase extends \Drupal\simpletest\WebTestBase {

  protected $profile = 'standard';

  /**
   * Implements getInfo().
   */
  public static function getInfo() {
    return [
      'name' => t('First Paragraph tests'),
      'description' => t('Check that teasers have the right output..'),
      'group' => t('First Paragraph'),
    ];
  }

  public /**
   * Implements setUp().
   */
  function setUp() {
    // Call the parent with an array of modules to enable for the test.
    parent::setUp([
      'first_paragraph'
      ]);

    $instance = field_info_instance('node', 'body', 'page');
    $instance['display']['teaser']['type'] = 'text_first_para';
    $instance['display']['teaser']['settings'] = [];
    $instance->save();
  }

  public /**
   * Test the module's functionality.
   */
  function testFirstParaEntity() {
    // Define test content/paragraphs.
    $paras = [
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam nec vulputate nibh.',
      'Morbi faucibus nunc feugiat nisi elementum, eu imperdiet nisl semper. Vivamus tincidunt ex magna.',
    ];

    // Create a test node with the above 2 paragraphs. Using AutoP and \n\n to
    // make the filter make 2 paragraphs.
    $settings = [
      'promote' => 1,
      'language' => \Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED,
    ];
    $settings['body'][$settings['language']][0] = [
      'value' => implode("\n\n", $paras),
      'format' => 'filtered_html',
    ];
    $node = $this->drupalCreateNode($settings);

    // First test; the promotes nodes page. Should only have first para.
    $this->drupalGet('node');
    $this->assertText($paras[0]);
    $this->assertNoText($paras[1]);

    // Second test; the node page. Should have both para's.
    $this->drupalGet('node/' . $node->nid);
    $this->assertText($paras[0]);
    $this->assertText($paras[1]);

  }

}
