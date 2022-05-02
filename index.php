<?php

namespace Servers;

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
use Servers\Controllers\AuthController;

$mainDir = explode('/', $_SERVER['SCRIPT_NAME'])[1];
putenv("MAIN_DIR=$mainDir");

Repositories::init();

AvocadoORMSettings::useDatabase('mysql:host=localhost;dbname=servers;', 'root', '');
AvocadoORMSettings::useFetchOption(\PDO::FETCH_CLASS);

AvocadoRouter::GET("/", [], function() {
    AuthController::authenticationMiddleware(["error" => "Unauthorized"]);
    require "./views/main.php";
});

AvocadoRouter::GET('/admin', [], function () {
    AuthController::authenticationMiddleware(["error" => "Unauthorized"]);
    require "./views/admin.php";
});

AvocadoRouter::GET('/login', [], function (AvocadoRequest $req, AvocadoResponse $res) {
    $errors = $req->query;
    require "views/login.php";
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
