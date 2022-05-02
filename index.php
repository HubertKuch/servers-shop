<?php

session_start();

// DEV
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "vendor/autoload.php";

use Avocado\Router\AvocadoRouter;
use Avocado\Router\AvocadoRequest;
use Avocado\Router\AvocadoResponse;
use Avocado\ORM\AvocadoORMSettings;

use Servers\Repositories;

putenv('hash_algo=sh256');

Repositories::init();

try {
    AvocadoORMSettings::useDatabase('mysql:host=localhost;dbname=servers;', 'root', '');
} catch (\Avocado\ORM\AvocadoRepositoryException $e) {
    // in development mode
    exit(1);
}
AvocadoORMSettings::useFetchOption(PDO::FETCH_CLASS);

AvocadoRouter::GET("/", [], function() {
    require "./views/main.php";
});

AvocadoRouter::POST('/api/login', [], function (AvocadoRequest $req, AvocadoResponse $res) {
    if (!isset($req->body['username']) || !isset($req->body['password'])) {}

    $user = Repositories::$userRepository->findOne(array(
        "username" => $req->body['username']
    ));

    if (!$user){}

    $isCorrectPassword = password_verify($req->body['password'], $user->passwordHash);

    if(!$isCorrectPassword) {}

    $_SESSION['id'] = $user->id;
});

AvocadoRouter::listen();
