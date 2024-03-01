<?php

namespace ArtemCherepanov\ClientPackage\Infrastructure;

use ArtemCherepanov\ClientPackage\Application\Contract\HttpServiceInterface;
use ArtemCherepanov\ClientPackage\Application\Dto\CommentPostDto;
use ArtemCherepanov\ClientPackage\Application\Dto\CommentPutDto;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpService implements HttpServiceInterface
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getData(): ResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            Constants::GET_COMMENTS
        );
    }


    /**
     * @throws TransportExceptionInterface
     */
    public function postData(CommentPostDto $commentDto): ResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            Constants::POST_COMMENT,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(
                    [
                        'title' => $commentDto->getTitle(),
                        'body' => $commentDto->getBody(),
                        'userId' => $commentDto->getUserId()
                    ],
                )
            ],
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function putData(CommentPutDto $commentDto): ResponseInterface
    {
        return $this->httpClient->request(
            'PUT',
            Constants::PUT_COMMENT . $commentDto->getId(),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(
                    [
                        'title' => $commentDto->getTitle(),
                        'body' => $commentDto->getBody(),
                        'userId' => $commentDto->getUserId(),
                        'id' => $commentDto->getId()
                    ],
                )
            ],
        );
    }
}
