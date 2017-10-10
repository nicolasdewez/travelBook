@reset-schema
Feature:
    Check all API which concerns travels.

    Scenario:
          There are travels (with users and places) saved in database.
          Tests of get collection of Travel resources:
            - Get first and second pages of travels list
            - Get first and second pages of user's travels list.

        Given the following users:
          | username | firstname | lastname | locale | email             | enabled |
          | user1    | Nicolas   | Dewez    | fr     | user1@example.com | 1       |
          | user2    | Nicolas   | Dewez    | en     | user2@example.com | 0       |
        And the following places:
          | title  | locale |
          | place1 | fr     |
          | place2 | fr     |
        And the following travels:
          | user  | place  | title   | startDate  | endDate    |
          | user1 | place1 | travel1 | 2017-02-04 | 2017-02-10 |
          | user2 | place2 | travel2 | 2017-02-05 | 2017-02-11 |
        And the following pictures:
          | place  | travel  | title    | date       |
          | place1 | travel1 | picture1 | 2017-02-04 |
          | place2 | travel1 | picture2 | 2017-02-05 |
          | place2 | travel2 | picture3 | 2017-02-06 |

        Given I prepare a GET request on "/api/resources/travels"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        [
          {
            "id": 1,
            "title": "travel1",
            "startDate": "2017-02-04T00:00:00+01:00",
            "endDate": "2017-02-10T00:00:00+01:00",
            "place": "/api/resources/places/1",
            "pictures": [
               "/api/resources/pictures/2",
               "/api/resources/pictures/1"
            ]
          },
          {
            "id": 2,
            "title": "travel2",
            "startDate": "2017-02-05T00:00:00+01:00",
            "endDate": "2017-02-11T00:00:00+01:00",
            "place": "/api/resources/places/2",
            "pictures": [
               "/api/resources/pictures/3"
            ]
          }
        ]
        """

        Given I prepare a GET request on "/api/resources/travels?page=2"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        []
        """

        Given I prepare a GET request on "/api/resources/users/1/travels"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        [
          {
            "id": 1,
            "title": "travel1",
            "startDate": "2017-02-04T00:00:00+01:00",
            "endDate": "2017-02-10T00:00:00+01:00",
            "place": "/api/resources/places/1",
            "pictures": [
               "/api/resources/pictures/2",
               "/api/resources/pictures/1"
            ]
          }
        ]
        """

        Given I prepare a GET request on "/api/resources/users/1/travels?page=2"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        []
        """


    Scenario:
          There are travels (with places and users) saved in database.
          Tests of get a Travel resource:
              - Get a travel when it exists and not exists

        Given the following users:
          | username | firstname | lastname | locale | email             | enabled |
          | user1    | Nicolas   | Dewez    | fr     | user1@example.com | 1       |
          | user2    | Nicolas   | Dewez    | en     | user2@example.com | 0       |
        And the following places:
          | title  | locale |
          | place1 | fr     |
          | place2 | fr     |
        And the following travels:
          | user  | place  | title   | startDate  | endDate    |
          | user1 | place1 | travel1 | 2017-02-04 | 2017-02-10 |
          | user2 | place2 | travel2 | 2017-02-05 | 2017-02-11 |
        And the following pictures:
          | place  | travel  | title    | date       |
          | place1 | travel1 | picture1 | 2017-02-04 |
          | place2 | travel1 | picture2 | 2017-02-05 |
          | place2 | travel2 | picture3 | 2017-02-06 |

        Given I prepare a GET request on "/api/resources/travels/1"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        {
          "id": 1,
          "title": "travel1",
          "startDate": "2017-02-04T00:00:00+01:00",
          "endDate": "2017-02-10T00:00:00+01:00",
          "place": "/api/resources/places/1",
          "pictures": [
             "/api/resources/pictures/2",
             "/api/resources/pictures/1"
          ]
        }
        """

        Given I prepare a GET request on "/api/resources/travels/42"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 404 response
