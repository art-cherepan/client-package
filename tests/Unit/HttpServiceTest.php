<?php

namespace Unit;

use ArtemCherepanov\ClientPackage\Application\Dto\CommentPostDto;
use ArtemCherepanov\ClientPackage\Application\Dto\CommentPutDto;
use ArtemCherepanov\ClientPackage\Infrastructure\HttpService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

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
        $commentPostDto = new CommentPostDto(55, 'some title', 'some body');

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

    public function testPutData(): void
    {
        $commentPutDto = new CommentPutDto(222, 33, 'new title', 'old body');

        $mockResponse = <<<JSON
          {
            "id": 222,
            "userId": 33,
            "title": "new title",
            "body": "old body"
          }
        JSON;

        $httpClient = new MockHttpClient([
            new MockResponse($mockResponse, ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']])
        ]);

        $stub = $this->getMockBuilder(HttpService::class)
            ->setConstructorArgs([$httpClient])
            ->onlyMethods([])
            ->getMock();

        $response = $stub->putData($commentPutDto);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaders()['content-type'][0]);
        $this->assertEquals(222, $response->toArray()['id']);
        $this->assertEquals(33, $response->toArray()['userId']);
        $this->assertEquals('new title', $response->toArray()['title']);
        $this->assertEquals('old body', $response->toArray()['body']);
    }

    public function testPostDataUserIdNotValid(): void
    {
        $uri = 'https://jsonplaceholder.typicode.com/posts';

        $commentPostDtoMock = $this->createMock(CommentPostDto::class);
        $commentPostDtoMock->method('getUserId')->willReturn(-1);
        $commentPostDtoMock->method('getTitle')->willReturn('some title');
        $commentPostDtoMock->method('getBody')->willReturn('some body');

        $mockResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockResponse->method('toArray')->willReturn(
            [
                'status' => 400,
                'error' => 'The userId field value must not be less than 1'
            ]
        );

        $clientMock = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $clientMock->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $uri,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode(
                        [
                            'title' => $commentPostDtoMock->getTitle(),
                            'body' => $commentPostDtoMock->getBody(),
                            'userId' => $commentPostDtoMock->getUserId()
                        ],
                    )
                ],
            )
            ->willReturn($mockResponse);

        $stub = $this->getMockBuilder(HttpService::class)
            ->setConstructorArgs([$clientMock])
            ->onlyMethods([])
            ->getMock();

        $response = $stub->postData($commentPostDtoMock);

        $this->assertEquals(400, $response->toArray()['status']);
        $this->assertEquals('The userId field value must not be less than 1', $response->toArray()['error']);
    }

    public function testPutDataIdNotValid(): void
    {
        $uri = 'https://jsonplaceholder.typicode.com/posts/';

        $commentPutDto = $this->createMock(CommentPutDto::class);
        $commentPutDto->method('getId')->willReturn(-1);
        $commentPutDto->method('getUserId')->willReturn(1);
        $commentPutDto->method('getTitle')->willReturn('new title');
        $commentPutDto->method('getBody')->willReturn('old body');

        $mockResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockResponse->method('toArray')->willReturn(
            [
                'status' => 400,
                'error' => 'The id field value must not be less than 1'
            ]
        );

        $clientMock = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $clientMock->expects($this->once())
            ->method('request')
            ->with(
                'PUT',
                $uri . $commentPutDto->getId(),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode(
                        [
                            'title' => $commentPutDto->getTitle(),
                            'body' => $commentPutDto->getBody(),
                            'userId' => $commentPutDto->getUserId(),
                            'id' => $commentPutDto->getId()
                        ],
                    )
                ],
            )
            ->willReturn($mockResponse);

        $stub = $this->getMockBuilder(HttpService::class)
            ->setConstructorArgs([$clientMock])
            ->onlyMethods([])
            ->getMock();

        $response = $stub->putData($commentPutDto);

        $this->assertEquals(400, $response->toArray()['status']);
        $this->assertEquals('The id field value must not be less than 1', $response->toArray()['error']);
    }
}
