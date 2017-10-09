@reset-schema
Feature:
    Check all API which concerns places

    Scenario:
          There are places saved in database.
          Tests of get collection of Place resources:
              - Get first and second pages of places list.
              - Search places by title and locale.

        Given the following places:
          | title         | locale | latitude | longitude |
          | Cap Gris Nez  | fr     | 10       | 100       |
          | Tower Bridge  | en     | 11       | 101       |
          | Cap Blanc Nez | fr     | 12       | 102       |
          | Vosges        | fr     | 13       | 103       |

        Given I prepare a GET request on "/api/resources/places"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        [
          {
            "id": 1,
            "title": "Cap Gris Nez",
            "locale": "fr",
            "latitude": 10,
            "longitude": 100
          },
          {
            "id": 2,
            "title": "Tower Bridge",
            "locale": "en",
            "latitude": 11,
            "longitude": 101
          },
          {
            "id": 3,
            "title": "Cap Blanc Nez",
            "locale": "fr",
            "latitude": 12,
            "longitude": 102
          },
          {
            "id": 4,
            "title": "Vosges",
            "locale": "fr",
            "latitude": 13,
            "longitude": 103
          }
        ]
        """

        Given I prepare a GET request on "/api/resources/places?page=2"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        []
        """

        Given I prepare a GET request on "/api/resources/places?locale=fr&title=nez"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        [
          {
            "id": 1,
            "title": "Cap Gris Nez",
            "locale": "fr",
            "latitude": 10,
            "longitude": 100
          },
          {
            "id": 3,
            "title": "Cap Blanc Nez",
            "locale": "fr",
            "latitude": 12,
            "longitude": 102
          }
        ]
        """

        Given I prepare a GET request on "/api/resources/places?locale=fr&title=nez&page=2"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        []
        """


    Scenario:
          There are places (with users, travels and pictures) saved in database.
          Tests of get a Place resource:
              - Get a place when it exists and not exists
              - Get a travel's place
              - Get a travel's place of user
              - Get a picture's place
              - Get a picture's place of travel
              - Get a picture's place of user's travel  # ko

        Given the following users:
          | username | firstname | lastname | locale | email             | enabled |
          | user1    | Nicolas   | Dewez    | fr     | user1@example.com | 1       |
          | user2    | Nicolas   | Dewez    | en     | user2@example.com | 0       |
        And the following places:
          | title         | locale | latitude | longitude |
          | Cap Gris Nez  | fr     | 10       | 100       |
          | Tower Bridge  | en     | 11       | 101       |
        And the following travels:
          | place        | user  | title   | startDate  | endDate    |
          | Cap Gris Nez | user2 | travel1 | 2017-02-04 | 2017-02-10 |
          | Tower Bridge | user1 | travel2 | 2017-02-05 | 2017-02-11 |
        And the following pictures:
          | place        | travel  | title    | date       |
          | Cap Gris Nez | travel2 | picture1 | 2017-02-04 |
          | Tower Bridge | travel1 | picture2 | 2017-02-05 |

        Given I prepare a GET request on "/api/resources/places/1"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        {
          "id": 1,
          "title": "Cap Gris Nez",
          "locale": "fr",
          "latitude": 10,
          "longitude": 100
        }
        """

        Given I prepare a GET request on "/api/resources/places/42"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 404 response

        Given I prepare a GET request on "/api/resources/travels/2/place"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        {
          "id": 2,
          "title": "Tower Bridge",
          "locale": "en",
          "latitude": 11,
          "longitude": 101
        }
        """

        Given I prepare a GET request on "/api/resources/users/1/travels/2/place"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        {
          "id": 2,
          "title": "Tower Bridge",
          "locale": "en",
          "latitude": 11,
          "longitude": 101
        }
        """

        Given I prepare a GET request on "/api/resources/pictures/1/place"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        {
          "id": 1,
          "title": "Cap Gris Nez",
          "locale": "fr",
          "latitude": 10,
          "longitude": 100
        }
        """

        Given I prepare a GET request on "/api/resources/travels/2/pictures/1/place"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        {
          "id": 1,
          "title": "Cap Gris Nez",
          "locale": "fr",
          "latitude": 10,
          "longitude": 100
        }
        """
