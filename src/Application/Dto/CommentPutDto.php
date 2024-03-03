<?php

namespace ArtemCherepanov\ClientPackage\Application\Dto;

use ArtemCherepanov\ClientPackage\Application\Exception\CommentPutDtoIdNotValidException;
use ArtemCherepanov\ClientPackage\Application\Exception\CommentPutDtoUserIdNotValidException;

class CommentPutDto
{
    /**
     * @throws CommentPutDtoIdNotValidException
     * @throws CommentPutDtoUserIdNotValidException
     */
    public function __construct(
        private readonly int $id,
        private readonly int $userId,
        private readonly string $title,
        private readonly string $body
    ) {
        if ($this->id < 1) {
            throw new CommentPutDtoIdNotValidException();
        }

        if ($this->userId < 1) {
            throw new CommentPutDtoUserIdNotValidException();
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
