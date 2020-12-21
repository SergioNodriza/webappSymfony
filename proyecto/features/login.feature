Feature:
  In order to do login
  On the route
  With a filled form
  I will get a logged user

  @login

  Scenario: Login a User
    Given I am on "/en/login"
    When I fill in the logIn form
    Then I should have a logged User
