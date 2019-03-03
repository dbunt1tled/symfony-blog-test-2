Feature: Manage blog post

  @createSchema
  Scenario: Throw blog post
    Given I am authenticated as "admin@bg.local"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "api/blog_posts" with body:
    """
    {
	"title": "",
	"slug": "hello-from-new-world-you"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON matches expected template:
    """
      {"@context":"\/api\/contexts\/ConstraintViolationList","@type":"ConstraintViolationList","hydra:title":"An error occurred","hydra:description":"content: This value should not be blank.","violations":[{"propertyPath":"content","message":"This value should not be blank."}]}
    """

  @createSchema
  Scenario: Throw when user not authenticated
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "api/blog_posts" with body:
    """
    {
	"title": "",
	"slug": "hello-from-new-world-you"
    }
    """
    Then the response status code should be 401

  @createSchema
  Scenario: Create blog post
    Given I am authenticated as "admin@bg.local"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "api/blog_posts" with body:
    """
    {
	"title": "Hello",
	"content": "Hello!",
	"slug": "hello-from-new-world-you"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON matches expected template:
    """
      {"@context":"\/api\/contexts\/BlogPost","@id":"@string@","@type":"BlogPost","id":@integer@,"title":"Hello","content":"Hello!","slug":"hello-from-new-world-you","published":"@string@","author":"@string@","comments":[],"images":[]}
    """
