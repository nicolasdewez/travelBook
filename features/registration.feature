@reset-schema
@reset-queue
Feature:
    Check process of registration

    Scenario:
          There are no users in database.
          An user wants to register. He's redirected to change password page.

        Given I go to "/"
        Then I should see "Bienvenue"
        When I follow "Inscription"
        Then I should see "Inscription"
        When I fill in "registration_firstname" with "Nicolas"
        And I fill in "registration_lastname" with "Dewez"
        And I fill in "registration_email" with "ndewez@example.com"
        And I fill in "registration_username" with "ndewez"
        And I select "fr" from "registration_locale"
        And I press "Enregistrer"
        Then the url should match "/login"
        And I should see "Votre demande d'inscription est en cours"
        And 1 user should have been created
        And should be 1 user like:
            | id | username | firstname | lastname | email              | locale | firstConnection | enabled |
            | 1  | ndewez   | Nicolas   | Dewez    | ndewez@example.com | fr     | true            | false   |
        And the queue associated to "registration" producer has messages to re-publish below:
            | 1 | {"id":1} |
        When I run the app command "rabbitmq:consumer registration --messages=1"
        Then the queue associated to "registration" producer is empty
        When I run the app command "swiftmailer:spool:send"
        Then I should see "1 emails sent" in the output of command
        And I should see mail with subject "[Travelbook] Inscription"
        When I open mail with subject "[Travelbook] Inscription"
        Then I should see "Votre login" in mail
        And I should see "Mot de passe" in mail
        And I should see "Pour activer votre compte, merci de cliquer sur ce lien" in mail
        And I save password in mail
        When I follow "ici" in mail
        Then I should see "Votre compte est activé"
        And should be 1 user like:
            | id | username | firstname | lastname | email              | locale | firstConnection | enabled |
            | 1  | ndewez   | Nicolas   | Dewez    | ndewez@example.com | fr     | true            | true    |
        When I fill in "_username" with "ndewez"
        And I fill in "_password" with password saved from mail
        And I press "Se connecter"
        Then I should see "Mise à jour du mot de passe"
        And I should see "Il est très conseillé de modifier le mot de passe lors de la première connexion."
        When I fill in "change_password_currentPassword" with password saved from mail
        And I fill in "change_password_newPassword_first" with "password1"
        And I fill in "change_password_newPassword_second" with "password1"
        And I press "Enregistrer"
        Then should be 1 user like:
            | id | username | firstname | lastname | email              | locale | firstConnection | enabled |
            | 1  | ndewez   | Nicolas   | Dewez    | ndewez@example.com | fr     | false           | true    |
        And I should see "Votre mot de passe a été mis à jour"
        And I should see "Mes voyages"
