<?php

namespace Drupal\Tests\days_difference\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Config;
use Drupal\Core\Form\FormState;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\savings_calculator\Form\SavingsCalculatorSettingsForm;
use Drupal\Tests\UnitTestCase;

/**
 * Test class for CustomModuleForm
 * 
 * Must extend from UnitTestCase
 */
class DaysDifferenceFormTest extends UnitTestCase {

  /**
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  private $translationInterfaceMock;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $configFactoryMock;

  /**
   * @var \Drupal\Core\Config\Config
   */
  private $configMock;

  /**
   * @var \Drupal\savings_calculator\Form
   */
  private $form;

  public function setUp() {
    // prophesize() is made available via extension from UnitTestCase
    // Call this method to create a mock based on a class
    $this->translationInterfaceMock = $this->prophesize(TranslationInterface::class);

    // Create mock to return config that will be used in the code under test
    $this->configMock = $this->prophesize(Config::class);
    // When the get method of the config is called with the parameter 'custom_property', 
    // the array ['label' => 'Discounts'] will be returned.
    $this->configMock->get('custom_property')->willReturn([
      'label' => 'Discounts'
    ]);

    $this->configFactoryMock = $this->prophesize(ConfigFactoryInterface::class);
    $this->configFactoryMock->getEditable('days_difference.settings')->willReturn($this->configMock);

    // Instantiate the code under test
    $this->form = new CustomModuleForm($this->configFactoryMock->reveal());
    // Config Base Form has a call to $this->t() which references the TranslationService
    // Set the translation service mock so that the program won't throw an error
    $this->form->setStringTranslation($this->translationInterfaceMock->reveal());
  }

  // Test that the correct form ID is returned
  public function testFormId() {
    $this->assertEquals('days_difference_settings_form', $this->form->getFormId());
  }

  // Test that the correct form fields are added
  public function testBuildForm() {
    // Arrange
    $form = [];
    $form_state = new FormState();

    // Act
    // Call the function being tested
    $retForm = $this->form->buildForm($form, $form_state);

    // Assert
    $this->assertEquals('module_theme', $retForm['#theme']);
    $this->assertArrayEquals(['#type' => 'submit'], $retForm['submit']);

    // The code under test retrieves the label from config
    // Check that the label returned by config is given 
    // to the title attribute of the custom field
    $this->assertArrayEquals(
      [
        '#type' => 'date',
        '#title' => 'form'
      ],
      $retForm['custom']
    );
  }
}