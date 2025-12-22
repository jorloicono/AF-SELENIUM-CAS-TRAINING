@smoke @login @critical
Feature: Login Functionality
  # DESCRIPCIÓN EN LENGUAJE NEGOCIO
  As a new visitor of SauceDemo
  I want to login with valid credentials
  So that I can access the product catalog
  
  # Background se ejecuta ANTES de CADA scenario
  Background:
    Given I am on the login page

  # Scenario Outline = Data-Driven Testing (CSV-like)
  @positive
  Scenario Outline: Successful login with valid users
    When I login with username "<username>" and password "<password>"
    Then I should be redirected to the inventory page
    And I should see "Products"
    And the shopping cart badge should show "0"
    And the "add-to-cart-sauce-labs-backpack" button should be visible

    Examples:
      | username              | password     |
      | standard_user         | secret_sauce |
      | problem_user          | secret_sauce |
      | performance_glitch_user | secret_sauce |

  @negative @critical
  Scenario Outline: Failed login scenarios
    When I fill in "user-name" with "<username>"
    And I fill in "password" with "<password>"
    And I press "login-button"
    Then I should see error message "<error_message>"

    Examples:
      | username      | password | error_message                                           |
      | invalid_user  | secret_sauce | Epic sadface: Username and password do not match any account in this store |
      | standard_user | invalid_pass | Epic sadface: Username and password do not match any account in this store |
      |               | secret_sauce | Epic sadface: Username is required                      |
      | standard_user |           | Epic sadface: Password is required                      |

  @negative @security
  Scenario: Locked out user
    When I login with username "locked_out_user" and password "secret_sauce"
    Then I should see error message "Sorry, this user has been locked out of the system"