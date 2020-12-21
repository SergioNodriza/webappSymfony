
Feature:
    In order register an user

    Scenario: Register a new User
        Given I am on "/en/register"
        When I fill in the form
        Then I should have a registered User
