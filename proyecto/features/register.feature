Feature:
  In order to register an user
  On the route
  With a filled form
  I will get a registered user

  @register

  Scenario: Register a new User
    Given I am on "/en/register"
    When I fill in the register form
    Then I should have a registered User
