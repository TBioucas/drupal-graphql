@homepage
Feature: Homepage
  In order to use the Homepage
  As a visitor
  I should be able to see all the content

  @javascript @api @text @section
  Scenario Outline: Sections Titles
    Given I go to "<page>"
    Then I should see the text "<text>" on element "<selector>" on parent "<parent_selector>"

    Examples:
      | page | parent_selector                    | text                     | selector|
      | /  | .content | Welcome to Drush Site-Install         | h1       |

