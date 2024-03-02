<?php

use ArtemCherepanov\ClientPackage\Application\Client;
use ArtemCherepanov\ClientPackage\Application\Exception\GetDataException;
use ArtemCherepanov\ClientPackage\Application\Exception\PostDataException;
use ArtemCherepanov\ClientPackage\Application\Exception\PutDataException;
use Symfony\Component\HttpClient\CurlHttpClient;

require_once __DIR__ . '/../vendor/autoload.php';

$curlHttpClient = new CurlHttpClient([
    'verify_peer' => false,
    'verify_host' => false,
]);

$client = new Client($curlHttpClient);

try {
    $comments = $client->getComments();
} catch (GetDataException $e) {
    echo 'Error while receiving comments. Message: ' . $e->getMessage() .
        ' Code: ' . $e->getCode() .
        ' File: ' . $e->getFile() .
        ' Line: ' . $e->getLine();
}

try {
    $newComment = $client->postComment(1, 'New amazing comment', 'some text');
} catch (PostDataException $e) {
   echo 'Error creating comment. Message: ' . $e->getMessage() .
       ' Code: ' . $e->getCode() .
       ' File: ' . $e->getFile() .
       ' Line: ' . $e->getLine();
}

try {
    $updatedComment = $client->putComment(1, 1, 'New title', 'new text');
} catch (PutDataException $e) {
    echo 'Error updating comment. Message: ' . $e->getMessage() .
        ' Code: ' . $e->getCode() .
        ' File: ' . $e->getFile() .
        ' Line: ' . $e->getLine();
}
