@bootstrap @smoke
Feature: Application Bootstrap and Page Load
  Sanity checks que la aplicación carga correctamente

  Scenario: Login page loads correctly
    Given I am on the login page
    Then the page title should be "Swag Labs"
    And I should see "Swag Labs"
    And the "user-name" field should be visible
    And the "password" field should be visible
    And the "login-button" button should be enabled

  Scenario: Page responsiveness
    Given I am on the login page
    When the window is resized to mobile
    Then the login form should be responsive