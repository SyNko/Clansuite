<?php
use Koch\Mvc\HttpResponse;

class Clansuite_HttpResponse_Test extends Clansuite_UnitTestCase
{
    /**
     * @var Clansuite_HttpResponse
     */
    protected $response;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->response = new HttpResponse;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        unset($this->response);
    }

    public function testProperty_DefaultStatusIs200()
    {
        #$this->assertEquals(200, $this->response->getStatus());
        #$this->assertEqual(200, $this->response->statusCode);
        $this->assertEqual(200, HttpResponse::getStatusCode());
    }
}
