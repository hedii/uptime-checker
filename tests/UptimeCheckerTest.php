<?php

namespace Hedii\UptimeChecker\Tests;

use Hedii\UptimeChecker\UptimeChecker;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class UptimeCheckerTest extends TestCase
{
    /**
     * @var \Hedii\UptimeChecker\UptimeChecker
     */
    private $checker;

    public function setUp()
    {
        parent::setUp();

        $this->checker = new UptimeChecker();
    }

    public function testClientInstance()
    {
        $this->assertInstanceOf(UptimeChecker::class, $this->checker);
    }

    public function testClientPropertyIsHttpClient()
    {
        $this->assertInstanceOf(Client::class, $this->checker->client);
    }

    public function testConnectionTimeoutGetterAndSetter()
    {
        $this->checker->setConnectionTimeout(12345);

        $this->assertEquals(12345, $this->checker->getConnectionTimeout());
        $this->assertInstanceOf(UptimeChecker::class, $this->checker->setConnectionTimeout(12345));
    }

    public function testRequestTimeoutGetterAndSetter()
    {
        $this->checker->setRequestTimeout(12345);

        $this->assertEquals(12345, $this->checker->getRequestTimeout());
        $this->assertInstanceOf(UptimeChecker::class, $this->checker->setRequestTimeout(12345));
    }

    public function testAnOnlineSite()
    {
        $mock = new MockHandler([new Response(200)]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->assertEquals([
            'uri' => 'http://a_website_that_is_online.com',
            'success' => true,
            'status' => 200,
            'message' => 'OK',
            'transfer_time' => 0
        ], (new UptimeChecker($client))->check('http://a_website_that_is_online.com'));
    }

    public function testAnOfflineSite()
    {
        $mock = new MockHandler([new Response(500)]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->assertEquals([
            'uri' => 'http://a_website_that_is_not_online.com',
            'success' => false,
            'status' => 500,
            'message' => 'Server error: `GET http://a_website_that_is_not_online.com` resulted in a `500 Internal Server Error` response:',
            'transfer_time' => 0
        ], (new UptimeChecker($client))->check('http://a_website_that_is_not_online.com'));
    }
}
