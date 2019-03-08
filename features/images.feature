Feature: Manage image
  @createSchema @image
  Scenario: Create && Upload Image
    Given I am authenticated as "admin@bg.local"
    When I add "Content-Type" header equal to "multipart/form-data; boundary --- WebKit193844043-h"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "api/images" with parameters:
      | key  | value       |
      | file | @Stewie.png |
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON matches expected template:
    """
    {"@context":"\/api\/contexts\/Image","@id":"\/api\/images\/1","@type":"Image","id":1,"url":"@string@","file":"@string@"}
    """
    @image
    Scenario: Assign BlogPost
      Given I am authenticated as "admin@bg.local"
      When I add "Content-Type" header equal to "application/ld+json"
      And I add "Accept" header equal to "application/ld+json"
      And I send a "PUT" request to "api/blog_posts/1" with body:
      """
      {
        "images": ["/api/images/1"]
      }
      """
      Then the response status code should be 200
      And the response should be in JSON
      And the JSON nodes should contain:
        | images[0] | api/images/1 |