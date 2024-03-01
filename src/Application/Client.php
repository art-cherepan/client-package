<?php

namespace ArtemCherepanov\ClientPackage\Application;

use ArtemCherepanov\ClientPackage\Application\Contract\HttpServiceInterface;
use ArtemCherepanov\ClientPackage\Application\Dto\CommentPostDto;
use ArtemCherepanov\ClientPackage\Application\Dto\CommentPutDto;
use ArtemCherepanov\ClientPackage\Domain\Entity\Comment;
use ArtemCherepanov\ClientPackage\Infrastructure\HttpService;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Client
{
    private readonly HttpServiceInterface $httpService;
    private readonly SerializerInterface $serializer;

    private const string COMMENT_DTO_TYPE_PATH = 'ArtemCherepanov\ClientPackage\Application\Dto\CommentPutDto[]';
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
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getComments(): array
    {
        $data = $this->httpService->getData()->getContent();

        /**
         * @var CommentPutDto[] $commentsDto
         */
        $commentsDto = $this->serializer->deserialize($data, self::COMMENT_DTO_TYPE_PATH, 'json');

        $comments = [];
        foreach ($commentsDto as $commentDto) {
            $comment = new Comment();
            $comment->setId($commentDto->getId());
            $comment->setUserId($commentDto->getUserId());
            $comment->setTitle($commentDto->getTitle());
            $comment->setBody($commentDto->getBody());

            $comments[] = $comment;
        }

        return $comments;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function postComment(int $userId, string $title, string $body): ResponseInterface
    {
        $commentDto = new CommentPostDto($userId, $title, $body);

        return $this->httpService->postData($commentDto);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function putComment(int $id, int $userId, string $title, string $body): ResponseInterface
    {
        $commentDto = new CommentPutDto($id, $userId, $title, $body);

        return $this->httpService->putData($commentDto);
    }
}
