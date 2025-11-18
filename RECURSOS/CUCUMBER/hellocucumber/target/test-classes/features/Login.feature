Feature: User Login

  # Escenario para login exitoso con un solo usuario
  @LoginTest
  Scenario: Successful login with valid credentials
    Given the user navigates on the login page
    When the user enters valid username and password
    Then the user should be redirected to the homepage

  # Escenario para login exitoso con múltiples usuarios
  # Scenario Outline: Successful login with multiple valid credentials
  #  Given the user is on the login page
  #  When the user provides "<username>" and "<password>"
  #  Then the user is redirected to the homepage

  #  Examples:
  #    | username      | password     |
  #    | standard_user | secret_sauce |
  #    | problem_user  | secret_sauce |

  # Escenario para login fallido (usuarios bloqueados)
  # Scenario Outline: Login attempt with blocked or invalid users
  #  Given the user goes to the login page
  #  When the user enters "<username>" and "<password>"
  #  Then the user should see an error message indicating login failure

  #  Examples:
  #    | username    | password     |
  #    | locked_user | secret_sauce |
  #    | invalid_user| wrong_pass  |
