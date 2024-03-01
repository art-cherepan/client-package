<?php

namespace ArtemCherepanov\ClientPackage\Application\Dto;

readonly class CommentPostDto
{
    public function __construct(private int $userId, private string $title, private string $body)
    {
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
