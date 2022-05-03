<?php

namespace Servers\Controllers;

use Avocado\Router\AvocadoRequest;
use Servers\Models\User;
use Servers\Repositories;

class UserController {
    public static final function login(AvocadoRequest $req): void {
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
    }

    public static final function register(AvocadoRequest $req): void {
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
    }
}