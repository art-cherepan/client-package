<?php

namespace Unit;

use ArtemCherepanov\ClientPackage\Infrastructure\HttpService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class HttpServiceTest extends TestCase
{
    public function testGetData()
    {
        $mockResponse = <<<JSON
        [
          {
            "userId": 1,
            "id": 1,
            "title": "sunt aut facere repellat provident occaecati excepturi optio reprehenderit",
            "body": "quia et suscipit"
          },
          {
            "userId": 1,
            "id": 2,
            "title": "qui est esse",
            "body": "est rerum tempore vitae"
          }
        ]
        JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($mockResponse, ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']])
        ]);

        $stub = $this->getMockBuilder(HttpService::class)
            ->setConstructorArgs([$httpClient])
            ->onlyMethods([])
            ->getMock();

        $response = $stub->getData();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaders()['content-type'][0]);
        $this->assertCount(2, json_decode($response->getContent(), true));
    }
}
