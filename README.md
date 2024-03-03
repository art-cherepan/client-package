## Description

The project is a client for the abstract web comment service `example.com`

The project implements the following methods for working with the `example.com` service:
* receiving comments
* creating a comment
* comment update

## Installation

The installation of this library is made via composer and the autoloading of all classes of this library is made through their autoloader.

* Download `composer.phar` from their [website](https://getcomposer.org/download/).
* Then run the following command to install this library as dependency:
* `php composer.phar require artem-cherepanov/client-package`

## Basic Usage

To get, create or update a comment use class `ArtemCherepanov\ClientPackage\Application\Client`

The implementation at the infrastructure level is in the class `ArtemCherepanov\ClientPackage\Infrastructure\HttpService`