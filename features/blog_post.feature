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
  # RUN ./vendor/bin/behat --tags=comment
  @createSchema @blogPost @comment
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
  @comment
  Scenario: AddCommentInBlog
    Given I am authenticated as "admin@bg.local"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "api/comments" with body:
    """
    {
	"content": "It first comment in blog from tests",
	"blogPost": "/api/blog_posts/11"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON matches expected template:
    """
      {"@context":"\/api\/contexts\/Comment","@id":"@string@","@type":"Comment","id":@integer@,"content":"It first comment in blog from tests","published":"@string@","author":"@string@","blogPost":"@string@"}
    """
  @comment
  Scenario: Show Comment
    Given I am authenticated as "admin@bg.local"
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "GET" request to "api/blog_posts/11/comments"
    Then the response status code should be 200
    And the response should be in JSON
    #And the JSON matches expected template:
    #"""
    #  {"@context":"\/api\/contexts\/Comment","@id":"\/api\/blog_posts\/11\/comments","@type":"hydra:Collection","hydra:member":[{"@id":"\/api\/comments\/48","@type":"Comment","id":48,"content":"It first comment in blog from tests","published":"2019-03-08T09:03:01+02:00","author":"\/api\/users\/21"},{"@id":"\/api\/comments\/42","@type":"Comment","id":42,"content":"Officiis nam sint et autem et totam. Quia rem quia consequatur vel consequuntur inventore dolores necessitatibus. Omnis amet omnis quis eum. Culpa autem ut iure aut odio neque nulla. Dolorem ipsam voluptas voluptatem dolorem illo. Minus suscipit aut reprehenderit. Sint ut quis id aspernatur atque rerum. Qui id consequatur dolorem delectus odit. Voluptatibus facilis animi autem deserunt. Non modi omnis sed delectus. Molestiae ut omnis dolore iste consectetur est. Ipsum odio saepe eaque error corporis nulla esse. Sunt soluta saepe dolor.","published":"2018-07-09T19:43:36+03:00","author":"\/api\/users\/20"},{"@id":"\/api\/comments\/9","@type":"Comment","id":9,"content":"Est cumque tempora a eaque sit nobis. Temporibus eveniet animi sunt adipisci eum maiores aut atque. Odio possimus quia et. Quo est accusamus consequatur odit rerum ut. A consequuntur qui excepturi porro fugit iure in. Ipsam dolorum vel explicabo ut. Nostrum autem quia ducimus non natus laborum. Quasi aliquid dolorum dolor natus et animi autem. Tempore odit et dolorem rerum quae eos. Officiis vitae rem est quos dolores asperiores beatae. Inventore aut qui necessitatibus voluptatem odit.","published":"2018-05-21T19:56:23+03:00","author":"\/api\/users\/14"}],"hydra:totalItems":3}
    #"""
