<?php

/**
 * @file
 * Description.
 */

use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawDrupalContext {

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }

  /**
   * Scroll view into an alignment.
   *
   * @param string $selector
   *   The CSS selector of the element.
   * @param string $alignTo
   *   A string value that indicates the type of the align.
   *
   * @Then I scroll into view :selector aligned to :alignTo
   *
   * @javascript @api
   */
  public function iScrollIntoViewAlignedTo($selector, $alignTo) {
    $locator = substr($selector, 0, 1);
    $alignment = $alignTo == 'top' ? TRUE : FALSE;
    switch ($locator) {
      case '#':
        $selector = substr($selector, 1);
        $function = <<<JS
        (function(){
          var elem = document.getElementById("$selector");
          console.log(elem)
          elem.scrollIntoView("$alignment");
        })()
        JS;
        break;

      case '.':
        $selector = substr($selector, 1);
        $function = <<<JS
        (function(){
          var elem = document.getElementsByClassName("$selector");
          elem[0].scrollIntoView("$alignment");
        })()
        JS;
        break;

      default:
        throw new \Exception(sprintf("Couldn\'t find selector '%s' - Allowed selectors: #id, .className"), $selector);
    }
    try {
      $this->getSession()->executeScript($function);
    }
    catch (Exception $e) {
      throw new \Exception("ScrollIntoView failed");
    }
  }

  /**
   * Check text of element on parent.
   *
   * @param string $text
   *   The text inside the element.
   * @param string $selector
   *   The CSS selector of the element.
   * @param string $parent_selector
   *   The CSS selector of the element's parent.
   *
   * @Then I should see the text :text on element :selector on parent :parent_selector
   *
   * @throws \Exception
   */
  public function iShouldSeeTheTextOnElementOnParent($text, $selector, $parent_selector) {
    $parent = $this->assertSession()->elementExists('css', $parent_selector);
    $nodes = $parent->findAll('css', $selector);
    foreach ($nodes as $node) {
      $node_text = $node->getText();
      if (mb_strtolower($node_text) != mb_strtolower($text)) {
        throw new \Exception(
          sprintf("The text '%s' found on element '%s' inside '%s' doesn't match with given '%s'.",
            strtolower($node_text),
            $selector,
            $parent_selector,
            strtolower($text)
          )
        );
      }
    }
  }

  /**
   * Check n elements on parent.
   *
   * @param string $number
   *   The minimun number of nodes to count.
   * @param string $selector
   *   The CSS selector of the element.
   * @param string $parent_selector
   *   The CSS selector of the element's parent.
   *
   * @Then I should see :number items :selector on parent :parent_selector
   *
   * @throws \Exception
   */
  public function iShouldSeeItemsOnParent($number, $selector, $parent_selector) {
    $parent = $this->assertSession()->elementExists('css', $parent_selector);
    $nodes = $parent->findAll('css', $selector);
    $count = count($nodes);
    if ($count < (int) $number) {
      throw new \Exception(
        sprintf("The parent '%s' does not contain '%s' items of '%s'. Only '%s' were found.",
          $parent_selector,
          $number,
          $selector, $count
        )
      );
    }
  }

  /**
   * Check items class.
   *
   * @param string $selector
   *   The CSS selector of the element.
   * @param string $class
   *   The CSS class which the element have.
   *
   * @Then I should see the items :selector with the class :class
   *
   * @throws \Exception
   */
  public function iShouldSeeTheItemsWithTheClass($selector, $class) {
    $nodes = $this->assertSession()->elementExists('css', $selector);
    foreach ($nodes as $node) {
      if (!$node->hasClass($class)) {
        throw new Exception(
          sprintf("No '%s' element was found with the class '%s'",
            $node,
            $class
          )
        );
      }
    }
  }

  /**
   * Check element with class.
   *
   * @param string $selector
   *   The CSS selector of the element.
   * @param string $class
   *   The CSS class which the element have.
   *
   * @Then I should see the element :selector with the class :class
   *
   * @throws \Exception
   */
  public function iShouldSeeTheElementWithTheClass($selector, $class) {
    $node = $this->assertSession()->elementExists('css', $selector);
    if (!$node->hasClass($class)) {
      throw new Exception(
        sprintf("No '%s' element was found with the class '%s'",
          $node,
          $class
        )
      );
    }
  }

  /**
   * Check element without class.
   *
   * @param string $selector
   *   The CSS selector of the element.
   * @param string $class
   *   The CSS class which the element doesn't have.
   *
   * @Then I should see the element :selhasclassector without the class :class
   *
   * @throws \Exception
   */
  public function iShouldSeeTheElementWithoutTheClass($selector, $class) {
    $node = $this->assertSession()->elementExists('css', $selector);
    if ($node->hasClass($class)) {
      throw new Exception(
        sprintf("No '%s' element was found without the class '%s'",
          $node,
          $class
        )
      );
    }
  }

  /**
   * Click on element.
   *
   * @param string $selector
   *   The CSS selector of the element.
   *
   * @When I click on :selector
   *
   * @throws \Exception
   */
  public function iClickOn($selector) {
    $element = $this->assertSession()->elementExists('css', $selector);
    $element->click();
    $this->getSession()->wait(1000);
  }

  /**
   * Click element inside parent.
   *
   * @param string $selector
   *   The CSS selector of the element.
   * @param string $parent_selector
   *   The CSS selector of the element's parent.
   *
   * @When I click on :selector inside parent :parent_selector
   *
   * @throws \Exception
   */
  public function iClickOnInsideParent($selector, $parent_selector) {
    $parent = $this->assertSession()->elementExists('css', $parent_selector);
    $element = $parent->find('css', $selector);
    $element->click();
    $this->getSession()->wait(1000);
  }

  /**
   * Wait.
   *
   * @param string $duration
   *   The duration to wait for.
   *
   * @Then I wait :duration
   */
  public function iWait($duration) {
    $this->getSession()->wait((int ) $duration);
  }

  /**
   * Close the cookies popup.
   *
   * @Given I close the cookie popup
   */
  public function iCloseTheCookiePopup() {
    $element = $this->assertSession()->elementExists('css', 'button.eu-cookie-compliance-default-button.agree-button');
    $element->click();
    $this->getSession()->wait(1000);
  }

}
