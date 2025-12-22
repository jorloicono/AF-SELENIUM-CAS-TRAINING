@e2e @cart
Feature: Shopping Cart Functionality
  As a logged-in customer
  I want to add/remove products from cart
  So that I can checkout with selected items

  Background:
    Given I am on the login page
    When I login with username "standard_user" and password "secret_sauce"

  @smoke
  Scenario: Add single product to cart
    When I click on "add-to-cart-sauce-labs-backpack"
    Then the "add-to-cart-sauce-labs-backpack" button should become "remove-from-cart-sauce-labs-backpack"
    And the shopping cart badge should show "1"

  @regression
  Scenario: Remove product from cart
    Given I click on "add-to-cart-sauce-labs-backpack"
    When I click on "remove-from-cart-sauce-labs-backpack"
    Then the shopping cart badge should show "0"
    And the "add-to-cart-sauce-labs-backpack" button should be visible

  @e2e
  Scenario: View cart contents
    Given I click on "add-to-cart-sauce-labs-backpack"
    When I click on shopping cart icon
    Then I should see "Your Cart"
    And I should see "Sauce Labs Backpack"
    And the checkout button should be enabled