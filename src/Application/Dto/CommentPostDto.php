<?php

namespace ArtemCherepanov\ClientPackage\Application\Dto;

use ArtemCherepanov\ClientPackage\Application\Exception\CommentPostDtoUserIdNotValidException;

class CommentPostDto
{
    /**
     * @throws CommentPostDtoUserIdNotValidException
     */
    public function __construct(
        private readonly int $userId,
        private readonly string $title,
        private readonly string $body
    ) {
        if ($this->userId < 1) {
            throw new CommentPostDtoUserIdNotValidException();
        }
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
