<?php

namespace Unit;

use ArtemCherepanov\ClientPackage\Application\Dto\CommentPostDto;
use ArtemCherepanov\ClientPackage\Application\Dto\CommentPutDto;
use ArtemCherepanov\ClientPackage\Application\Exception\CommentPostDtoUserIdNotValidException;
use ArtemCherepanov\ClientPackage\Application\Exception\CommentPutDtoIdNotValidException;
use ArtemCherepanov\ClientPackage\Application\Exception\CommentPutDtoUserIdNotValidException;
use PHPUnit\Framework\TestCase;

class DtoTest extends TestCase
{
    public function testCommentPostDtoUserIdIsNotValid(): void
    {
        $this->expectException(CommentPostDtoUserIdNotValidException::class);

        new CommentPostDto(-1, 'title', 'body');
    }

    public function testCommentPutDtoIdIsNotValid(): void
    {
        $this->expectException(CommentPutDtoIdNotValidException::class);

        new CommentPutDto(-1, 1, 'title', 'body');
    }

    public function testCommentPutDtoUserIdIsNotValid(): void
    {
        $this->expectException(CommentPutDtoUserIdNotValidException::class);

        new CommentPutDto(1, -1, 'title', 'body');
    }
}
