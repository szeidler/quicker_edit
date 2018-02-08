<?php

namespace Drupal\Tests\quicker_edit\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\JavascriptTestBase;
use Drupal\Tests\node\Traits\ContentTypeCreationTrait;
use Drupal\Tests\node\Traits\NodeCreationTrait;

class QuickerEditFunctionalTest extends JavascriptTestBase {

  use ContentTypeCreationTrait;
  use NodeCreationTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'node',
    'field_ui',
    'contextual',
    'editor',
    'quickedit',
    'quicker_edit',
    'toolbar',
  ];

  /**
   * A user with permissions to edit Articles and use Quick Edit.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $contentAuthorUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create the Article node type.
    $this->drupalCreateContentType(['type' => 'article', 'name' => 'Article']);

    // Log in as a content author who can use Quick Edit and edit Articles.
    $this->contentAuthorUser = $this->drupalCreateUser([
      'access contextual links',
      'access toolbar',
      'access in-place editing',
      'access content',
      'create article content',
      'edit any article content',
      'delete any article content',
    ]);
    $this->drupalLogin($this->contentAuthorUser);
  }

  /**
   * Tests, that a dblclick on a quickedit field, opens quickedit.
   */
  public function testQuickerEditToggler() {
    $node = $this->createNode([
      'type' => 'article',
      'uid' => $this->contentAuthorUser->id(),
    ]);

    // Assemble common CSS selectors.
    $field_name = 'body';
    $field_selector = '[data-quickedit-field-id="node/' . $node->id() . '/' . $field_name . '/' . $node->language()->getId() . '/full"]';

    // Visit the node.
    $this->drupalGet($node->toUrl()->toString());

    // Wait until the quick edit link is available.
    $this->assertSession()->waitForElement('css', '.quickedit > a');

    // Find the field and initiate Quick Editing.
    $this->assertSession()->elementExists('css', $field_selector);
    $this->doubleClick($field_selector);
    $this->click();

    // Wait until Quick Edit field was activated via an AJAX request.
    $this->assertSession()->assertWaitOnAjaxRequest();

    // Check, that the quick entity toolbar was loaded and the field is in editing mode.
    $this->assertSession()->elementExists('css', '.quickedit-toolbar');
    $this->assertSession()->elementAttributeContains('css', $field_selector, 'class', 'quickedit-editing');
    $this->assertSession()->elementExists('css', 'textarea[name="body[0][value]"]');

    // For now, we assume, that Quick Edit is working from here.
  }

  /**
   * Perform a double click on the given selector.
   *
   * @param string $css_selector
   *   CSS selector.
   *
   * @return string
   *   The clicked element.
   *
   * @throws \Behat\Mink\Exception\DriverException
   * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
   */
  protected function doubleClick($css_selector) {
    return $this->getSession()->getDriver()->doubleClick($this->cssSelectToXpath($css_selector));
  }

}
