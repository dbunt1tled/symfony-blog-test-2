<?php

use Behatch\Context\RestContext;
use Behat\Gherkin\Node\PyStringNode;

class FeatureContext extends RestContext
{

    public const USERS = [
        'admin@bg.local' => '12345678'
    ];

    public const AUTH_PATH = 'api/login_check';
    public const AUTH_JSON = '
        {
            "username":"%s",
            "password":"%s"
        }
    ';

    /** @var \App\DataFixtures\AppFixtures */
    private $fixtures;

    /** @var \Coduo\PHPMatcher\Matcher */
    private $matcher;
    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $em;

    public function __construct(
        \Behatch\HttpCall\Request $request,
        \App\DataFixtures\AppFixtures $fixtures,
        \Doctrine\ORM\EntityManagerInterface $em
    )
    {
        parent::__construct($request);
        $this->fixtures = $fixtures;
        $this->matcher = (new \Coduo\PHPMatcher\Factory\SimpleFactory())->createMatcher();
        $this->em = $em;
    }

    /**
     * @Given I am authenticated as :user
     * @param $user
     */
    public function iAmAuthenticatedAs($user)
    {
       $this->request->setHttpHeader('Content-Type', 'application/ld+json');
       $this->request->send(
           'POST',
           $this->locatePath(self::AUTH_PATH),
           [],
           [],
           sprintf(self::AUTH_JSON, $user, self::USERS[$user])
       );
       $json = json_decode($this->request->getContent(), true);
        // Make sure the taken was returned
       $this->assertTrue(isset($json['token']));
       $token = $json['token'];

       $this->request->setHttpHeader('Authorization', 'Bearer ' . $token);
    }


    /**
     * @param PyStringNode $json
     * @Then the JSON matches expected template:
     */
    public function theJsonMatchesExpectedTemplate(PyStringNode $json)
    {
        $actual = $this->request->getContent();
        $this->assertTrue(
            $this->matcher->match($actual, $json->getRaw())
        );
    }


    /**
     * @BeforeScenario @createSchema
     */
    public function createSchema()
    {
        // Get Entity Metadata
        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        // Drop New Schema
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        //Load fixtures
        $purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger($this->em);
        $fixtureExecuter = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($this->em, $purger);
        $fixtureExecuter->execute([
            $this->fixtures
        ]);


    }
}
