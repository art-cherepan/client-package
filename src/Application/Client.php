<?php

namespace ArtemCherepanov\ClientPackage\Application;

use ArtemCherepanov\ClientPackage\Application\Contract\HttpServiceInterface;
use ArtemCherepanov\ClientPackage\Application\Dto\CommentPostDto;
use ArtemCherepanov\ClientPackage\Application\Dto\CommentPutDto;
use ArtemCherepanov\ClientPackage\Application\Exception\CommentPostDtoUserIdNotValidException;
use ArtemCherepanov\ClientPackage\Application\Exception\CommentPutDtoIdNotValidException;
use ArtemCherepanov\ClientPackage\Application\Exception\CommentPutDtoUserIdNotValidException;
use ArtemCherepanov\ClientPackage\Application\Exception\GetDataException;
use ArtemCherepanov\ClientPackage\Application\Exception\PostDataException;
use ArtemCherepanov\ClientPackage\Application\Exception\PutDataException;
use ArtemCherepanov\ClientPackage\Domain\Entity\Comment;
use ArtemCherepanov\ClientPackage\Infrastructure\HttpService;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    private readonly HttpServiceInterface $httpService;
    private readonly SerializerInterface $serializer;
    private const string COMMENT_PUT_DTO_TYPE_PATH = 'ArtemCherepanov\ClientPackage\Application\Dto\CommentPutDto[]';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
        $this->httpService = new HttpService($this->httpClient);

        $this->serializer = new Serializer(
            [new GetSetMethodNormalizer(), new ArrayDenormalizer()],
            [new JsonEncoder()]
        );
    }

    /**
     * @return Comment[]
     * @throws GetDataException
     */
    public function getComments(): array
    {
        try {
            $content = $this->httpService->getData()->getContent();
        } catch (
            ClientExceptionInterface|
            RedirectionExceptionInterface|
            ServerExceptionInterface|
            TransportExceptionInterface $e
        ) {
            throw new GetDataException($e);
        }


        /**
         * @var CommentPutDto[] $commentsPutDto
         */
        $commentsPutDto = $this->serializer->deserialize($content, self::COMMENT_PUT_DTO_TYPE_PATH, 'json');

        $comments = [];
        foreach ($commentsPutDto as $commentPutDto) {
            $comment = new Comment();
            $comment->setId($commentPutDto->getId());
            $comment->setUserId($commentPutDto->getUserId());
            $comment->setTitle($commentPutDto->getTitle());
            $comment->setBody($commentPutDto->getBody());

            $comments[] = $comment;
        }

        return $comments;
    }

    /**
     * @throws PostDataException|CommentPostDtoUserIdNotValidException
     */
    public function postComment(int $userId, string $title, string $body): Comment
    {
        $commentPostDto = new CommentPostDto($userId, $title, $body);

        try {
            $content = $this->httpService->postData($commentPostDto)->toArray();
        } catch (
            ClientExceptionInterface|
            DecodingExceptionInterface|
            RedirectionExceptionInterface|
            ServerExceptionInterface|
            TransportExceptionInterface $e
        ) {
            throw new PostDataException($e);
        }

        $comment = new Comment();
        $comment->setId($content['id']);
        $comment->setUserId($content['userId']);
        $comment->setTitle($content['title']);
        $comment->setBody($content['body']);

        return $comment;
    }

    /**
     * @throws PutDataException|CommentPutDtoIdNotValidException|CommentPutDtoUserIdNotValidException
     */
    public function putComment(int $id, int $userId, string $title, string $body): Comment
    {
        $commentPutDto = new CommentPutDto($id, $userId, $title, $body);

        try {
            $response = $this->httpService->putData($commentPutDto)->toArray();
        } catch (
            ClientExceptionInterface|
            DecodingExceptionInterface|
            RedirectionExceptionInterface|
            ServerExceptionInterface|
            TransportExceptionInterface $e
        ) {
            throw new PutDataException($e);
        }

        $comment = new Comment();
        $comment->setId($response['id']);
        $comment->setUserId($response['userId']);
        $comment->setTitle($response['title']);
        $comment->setBody($response['body']);

        return $comment;
    }
}
