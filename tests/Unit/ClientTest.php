<?php

namespace Unit;

use ArtemCherepanov\ClientPackage\Application\Client;
use ArtemCherepanov\ClientPackage\Domain\Entity\Comment;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\CurlHttpClient;

class ClientTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $httpClient = new CurlHttpClient([
            'verify_peer' => false,
            'verify_host' => false,
        ]);

        $this->client = new Client($httpClient);
    }

    public function testGetComments(): void
    {
        $response = $this->client->getComments();

        $this->assertCount(2, $response);
        $this->assertInstanceOf(Comment::class, $response[0]);
    }

    public function testPostComment(): void
    {
        $response = $this->client->postComment(1, 'New amazing comment', 'some text');

        $this->assertInstanceOf(Comment::class, $response);
        $this->assertEquals(1, $response->getUserId());
        $this->assertEquals('New amazing comment', $response->getTitle());
        $this->assertEquals('some text', $response->getBody());
    }

    public function testPutComment(): void
    {
        $response = $this->client->putComment(1, 1, 'New title', 'new text');

        $this->assertInstanceOf(Comment::class, $response);
        $this->assertEquals(1, $response->getId());
        $this->assertEquals(1, $response->getUserId());
        $this->assertEquals('New title', $response->getTitle());
        $this->assertEquals('new text', $response->getBody());
    }
}
