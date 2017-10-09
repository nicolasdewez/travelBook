@reset-schema
Feature:
    Check all API which concerns users.

    Scenario:
          There are users (with travels) saved in database.
          Tests of get collection of User resources:
              - Get first and second pages of users list.

        Given the following users:
          | username | firstname | lastname | locale | email             | enabled |
          | user1    | Nicolas   | Dewez    | fr     | user1@example.com | 1       |
          | user2    | Nicolas   | Dewez    | en     | user2@example.com | 0       |
        And the following travel:
          | user  | title   |
          | user1 | travel1 |

        Given I prepare a GET request on "/api/resources/users"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        [
          {
            "id": 1,
            "username": "user1",
            "firstname": "Nicolas",
            "lastname": "Dewez",
            "locale": "fr",
            "enabled": true,
            "emailNotification": true,
            "travels": [
              "/api/resources/travels/1"
            ]
          },
          {
            "id": 2,
            "username": "user2",
            "firstname": "Nicolas",
            "lastname": "Dewez",
            "locale": "en",
            "enabled": false,
            "emailNotification": true,
            "travels": []
          }
        ]
        """

        Given I prepare a GET request on "/api/resources/users?page=2"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        []
        """


    Scenario:
          There are users saved in database.
          Tests of get a User resource:
              - Get a user when it exists and not exists

        Given the following users:
          | username | firstname | lastname | locale | email             | enabled |
          | user1    | Nicolas   | Dewez    | fr     | user1@example.com | 1       |
          | user2    | Nicolas   | Dewez    | en     | user2@example.com | 0       |

        Given I prepare a GET request on "/api/resources/users/2"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        {
          "id": 2,
          "username": "user2",
          "firstname": "Nicolas",
          "lastname": "Dewez",
          "locale": "en",
          "enabled": false,
          "emailNotification": true,
          "travels": []
        }
        """

        Given I prepare a GET request on "/api/resources/users/42"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 404 response
