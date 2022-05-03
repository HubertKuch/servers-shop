<?php

namespace Servers;

require "vendor/autoload.php";

// DEV
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

use Avocado\Router\AvocadoRouter;
use Avocado\Router\AvocadoRequest;
use Avocado\Router\AvocadoResponse;
use Avocado\ORM\AvocadoORMSettings;
use Servers\Controllers\AuthController;
use Servers\Models\ProductStatus;
use Servers\Controllers\LogsController;
use Servers\Models\User;
use function Servers\Controllers\redirectToLoginViewWithMessage;

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

    $payments = Repositories::$paymentsRepository->findMany();

    $soldServers = Repositories::$productsRepository->findMany([
        "status" => ProductStatus::SOLD->value
    ]);

    $logs = Repositories::$logsRepository->findMany();

    require "./views/admin.php";
});

AvocadoRouter::GET('/login', [], function (AvocadoRequest $req, AvocadoResponse $res) {
    $errors = $req->query;
    require "views/login.php";
});

AvocadoRouter::GET('/register', [], function (AvocadoRequest $req, AvocadoResponse $res) {
    $errors = $req->query;
    require "views/register.php";
});


AvocadoRouter::POST('/api/login', [], function (AvocadoRequest $req, AvocadoResponse $res) {
    if (!isset($req->body['username']) || !isset($req->body['password'])) {
        AuthController::redirectToLoginWithMessage(["message" => "Nazwa użytkownika lub email i hasło muszą być prowadzone."]);
        return;
    }

    $user = Repositories::$userRepository->findOne(array(
        "username" => $req->body['username']
    ));

    if (!$user){
        AuthController::redirectToLoginWithMessage(['test' => 2]);
        return;
    }

    $isCorrectPassword = password_verify($req->body['password'], $user->passwordHash);

    if(!$isCorrectPassword) {
        AuthController::redirectToLoginWithMessage(['message' => "Nieprawidlowe dane"]);
        return;
    }

    $_SESSION['id'] = $user->id;
    LogsController::saveUserLoginLog($user->id, $user->username);
    AuthController::redirect('', []);
});

AvocadoRouter::POST('/api/register', [], function (AvocadoRequest $req) {
    $username = $req->body['username'] ?? null;
    $email = $req->body['email'] ?? null;
    $password = $req->body['password'] ?? null;

    if (!$username || !$email || !$password) {
        AuthController::redirect('register', ["message" => "Wszystkie dane muszą być wypełnione"]);
        return;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $isEmailIsBusy  = Repositories::$userRepository->findOne(["email" => $email]);

    if ($isEmailIsBusy) {
        AuthController::redirect('register', ["message" => "Email jest zajety"]);
        return;
    }

    $user = new User($username, $email, $passwordHash);

    Repositories::$userRepository->save($user);
    $userId = Repositories::$userRepository->findOne(["email" => $email])->id;
    LogsController::saveUserRegisterLog($userId);
    AuthController::redirect('login', []);
});

AvocadoRouter::listen();
