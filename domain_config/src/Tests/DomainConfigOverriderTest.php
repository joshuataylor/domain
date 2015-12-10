<?php

/**
 * @file
 * Definition of Drupal\domain\Tests\DomainConfigOverriderTest.
 */

namespace Drupal\domain_config\Tests;

use Drupal\domain_config\Tests\DomainConfigTestBase;

/**
 * Tests the domain config system.
 *
 * @group domain_config
 */
class DomainConfigOverriderTest extends DomainConfigTestBase {

  /**
   * Tests that domain-specific variable loading works.
   */
  function testDomainConfigOverrider() {
    // No domains should exist.
    $this->domainTableIsEmpty();
    // Create four new domains programmatically.
    $this->domainCreateTestDomains(5);
    // Get the domain list.
    $domains = \Drupal::service('domain.loader')->loadMultiple();
    // Except for the default domain, the page title element should match what
    // is in the override files.
    foreach ($domains as $domain) {
      $path = $domain->getPath();
      $this->drupalGet($path);
      if ($domain->isDefault()) {
        $this->assertRaw('<title>Log in | Drupal</title>', 'Loaded the proper site name.');
      }
      else {
        $this->assertRaw('<title>Log in | ' . $domain->label() . '</title>', 'Loaded the proper site name.');
      }
    }
    // Now set a language context. Based on how we have our files setup, we
    // expect the following outcomes:
    //  example.com name = 'Drupal' for English, 'Drupal' for Spanish.
    //  one.example.com name = 'One' for English, 'Drupal' for Spanish.
    //  two.example.com name = 'Two' for English, 'Dos' for Spanish.
    //  three.example.com name = 'Three' for English, 'Drupal' for Spanish.
    //  four.example.com name = 'Four' for English, 'Four' for Spanish.
    foreach ($domains as $domain) {
      $path = $domain->getPath();
      $this->drupalGet($path);
      if ($domain->isDefault()) {
        $this->assertRaw('<title>Log in | Drupal</title>', 'Loaded the proper site name.');
      }
      else {
        $this->assertRaw('<title>Log in | ' . $domain->label() . '</title>', 'Loaded the proper site name.');
      }
      $path = $domain->getPath() . 'es';
      $this->drupalGet($path);
      if ($domain->isDefault()) {
        $this->assertRaw('<title>Log in | Drupal</title>', 'Loaded the proper site name.');
      }
      else {
        $this->assertRaw('<title>Log in | ' . $this->expectedName($domain) . '</title>', 'Loaded the proper site name.');
      }
    }

  }

  /**
   * Returns the expected site name value from our test configuration.
   *
   * @return string
   */
  private function expectedName($domain) {
    switch ($domain->id()) {
      case 'one_example_com':
      case 'three_example_com':
        $name = 'Drupal';
        break;
      case 'two_example_com':
        $name = 'Dos';
        break;
      case 'four_example_com':
        $name = 'Four';
        break;
    }
    return $name;
  }

}
