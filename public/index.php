<?php

use ArtemCherepanov\ClientPackage\Application\Client;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

require_once __DIR__ . '/../vendor/autoload.php';

$curlHttpClient = new CurlHttpClient([
    'verify_peer' => false,
    'verify_host' => false,
]);

$client = new Client($curlHttpClient);

try {
    var_dump($client->getComments());
    var_dump($client->postComment(1, 'New amazing comment', 'some text')->getContent());
    var_dump($client->putComment(1, 1, 'New title', 'new text')->getContent());
} catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
    echo 'Error sending request. Message: ' . $e->getMessage() .
         ' Code: ' . $e->getCode() .
         ' File: ' . $e->getFile() .
         ' Line: ' . $e->getLine();
}
