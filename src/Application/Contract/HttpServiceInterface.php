<?php

namespace ArtemCherepanov\ClientPackage\Application\Contract;

use ArtemCherepanov\ClientPackage\Application\Dto\CommentPostDto;
use ArtemCherepanov\ClientPackage\Application\Dto\CommentPutDto;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface HttpServiceInterface
{
    public function getData(): ResponseInterface;
    public function postData(CommentPostDto $commentPostDto): ResponseInterface;
    public function putData(CommentPutDto $commentPutDto): ResponseInterface;
}
