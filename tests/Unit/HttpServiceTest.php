<?php

namespace Unit;

use ArtemCherepanov\ClientPackage\Application\Dto\CommentPostDto;
use ArtemCherepanov\ClientPackage\Infrastructure\HttpService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class HttpServiceTest extends TestCase
{
    public function testGetData(): void
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

    public function testPostData(): void
    {
        $commentPostDto = new CommentPostDto(55, 'title', 'body');

        $mockResponse = <<<JSON
          {
            "id": 1111,
            "userId": 55,
            "title": "some title",
            "body": "some body"
          }
        JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($mockResponse, ['http_code' => 201, 'response_headers' => ['Content-Type: application/json']])
        ]);

        $stub = $this->getMockBuilder(HttpService::class)
            ->setConstructorArgs([$httpClient])
            ->onlyMethods([])
            ->getMock();

        $response = $stub->postData($commentPostDto);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaders()['content-type'][0]);
        $this->assertEquals(1111, $response->toArray()['id']);
        $this->assertEquals(55, $response->toArray()['userId']);
        $this->assertEquals('some title', $response->toArray()['title']);
        $this->assertEquals('some body', $response->toArray()['body']);
    }

//    public function testPostDataUserIdNotValid(): void
//    {
//        $commentPostDto = new CommentPostDto(-1, 'title', 'body');
//
//        $mockResponse = <<<JSON
//          {
//            "status": 400,
//            "error": "sad"
//          }
//        JSON;
//
//        $httpClient = new MockHttpClient([
//            new MockResponse($mockResponse, ['http_code' => 400, 'response_headers' => ['Content-Type: application/json']])
//        ]);
//
//        $stub = $this->getMockBuilder(HttpService::class)
//            ->setConstructorArgs([$httpClient])
//            ->onlyMethods(['postData'])
//            ->getMock();
//
//        $response = $stub->postData();
//
//        $this->assertEquals(400, $response->getStatusCode());
//    }
}
