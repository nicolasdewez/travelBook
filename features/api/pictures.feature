@reset-schema
Feature:
    Check all API which concerns pictures

    Scenario:
          There are pictures saved in database.
          Tests of get collection of Place resources:
              - Get first and second pages of pictures list
              - Get first and second pages of travel pictures list.
              - Get first and second pages of travel pictures of user list.

        Given the following users:
          | username | firstname | lastname | locale | email             | enabled |
          | user1    | Nicolas   | Dewez    | fr     | user1@example.com | 1       |
          | user2    | Nicolas   | Dewez    | en     | user2@example.com | 0       |
        And the following travels:
          | user  | title   | startDate  | endDate    |
          | user1 | travel1 | 2017-02-04 | 2017-02-10 |
          | user2 | travel2 | 2017-02-05 | 2017-02-11 |
        And the following places:
          | title  | locale |
          | place1 | fr     |
          | place2 | fr     |
        And the following pictures:
          | place  | travel  | title    | date       |
          | place1 | travel1 | picture1 | 2017-02-04 |
          | place2 | travel1 | picture2 | 2017-02-05 |
          | place2 | travel2 | picture3 | 2017-02-06 |

        Given I prepare a GET request on "/api/resources/pictures"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        [
          {
            "id":1,
            "title":"picture1",
            "date":"2017-02-04T00:00:00+01:00",
            "place":"/api/resources/places/1",
            "content":null
          },
          {
            "id":2,
            "title":"picture2",
            "date":"2017-02-05T00:00:00+01:00",
            "place":"/api/resources/places/2",
            "content":null
          },
          {
            "id":3,
            "title":"picture3",
            "date":"2017-02-06T00:00:00+01:00",
            "place":"/api/resources/places/2",
            "content":null
          }
        ]
        """

        Given I prepare a GET request on "/api/resources/pictures?page=2"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        []
        """

        Given I prepare a GET request on "/api/resources/travels/1/pictures"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        [
          {
            "id":1,
            "title":"picture1",
            "date":"2017-02-04T00:00:00+01:00",
            "place":"/api/resources/places/1",
            "content":null
          },
          {
            "id":2,
            "title":"picture2",
            "date":"2017-02-05T00:00:00+01:00",
            "place":"/api/resources/places/2",
            "content":null
          }
        ]
        """

        Given I prepare a GET request on "/api/resources/travels/1/pictures?page=2"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        []
        """

        Given I prepare a GET request on "/api/resources/users/2/travels/2/pictures"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        [
          {
            "id":3,
            "title":"picture3",
            "date":"2017-02-06T00:00:00+01:00",
            "place":"/api/resources/places/2",
            "content":null
          }
        ]
        """

        Given I prepare a GET request on "/api/resources/users/2/travels/2/pictures?page=2"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        []
        """


    Scenario:
          There are pictures (with places) saved in database.
          Tests of get a Picture resource:
              - Get a picture when it exists and not exists

        Given the following places:
          | title  | locale |
          | place1 | fr     |
          | place2 | en     |
        And the following pictures:
          | place  | title    | date       |
          | place1 | picture1 | 2017-02-04 |
          | place2 | picture2 | 2017-02-05 |

      Given I prepare a GET request on "/api/resources/pictures/2"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 200 json response
        Then the response should contain the following json:
        """
        {
          "id":2,
          "title":"picture2",
          "date":"2017-02-05T00:00:00+01:00",
          "place":"/api/resources/places/2",
          "content":null
        }
        """

        Given I prepare a GET request on "/api/resources/pictures/42"
        And I specified the following request headers:
          | Accept | application/json |
        When I send the request
        Then I should receive a 404 response
