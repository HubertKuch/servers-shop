<?php

namespace Servers\Controllers;

use Avocado\Router\AvocadoRequest;
use Servers\Models\User;
use Servers\Repositories;

class UserController {
    public static final function login(AvocadoRequest $req): void {
        if (!isset($req->body['username']) || !isset($req->body['password'])) AuthController::redirect('login', ["message" => "Nazwa użytkownika lub email i hasło muszą być prowadzone."]);

        $user = Repositories::$userRepository->findOne(["username" => $req->body['username']]);

        if (!$user) AuthController::redirect('login', ["message" => "Nieprawidlowe dane"]);

        $isCorrectPassword = password_verify($req->body['password'], $user->passwordHash);

        if(!$isCorrectPassword) AuthController::redirect('login', ["message" => "Nieprawidlowe dane"]);

        $_SESSION['id'] = $user->id;
        LogsController::saveUserLoginLog($user->id, $user->username);
        AuthController::redirect('');
    }

    public static final function register(AvocadoRequest $req): void {
        $username = $req->body['username'] ?? null;
        $email = $req->body['email'] ?? null;
        $password = $req->body['password'] ?? null;

        if (!$username || !$email || !$password) AuthController::redirect('register', ["message" => "Wszystkie dane muszą być wypełnione"]);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) AuthController::redirect('register', ["message" => "Nieprawidłowy email"]);

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $isEmailIsBusy  = Repositories::$userRepository->findOne(["email" => $email]);

        if ($isEmailIsBusy) AuthController::redirect('register', ["message" => "Email jest zajety"]);

        $user = new User($username, $email, $passwordHash);

        Repositories::$userRepository->save($user);
        $userId = Repositories::$userRepository->findOne(["email" => $email])->id;
        LogsController::saveUserRegisterLog($userId);
        AuthController::redirect('login');
    }

    public static final function changePassword(AvocadoRequest $req): void {
        AuthController::authenticationMiddleware([]);

        $oldPassword = $req->body['old-password'] ?? null;
        $newPassword = $req->body['new-password'] ?? null;

        if (!$oldPassword || !$newPassword) AuthController::redirect('panel', ["message" => "Stare i nowe hasło muszą byc podane."]);

        $userId = $_SESSION['id'];
        $user = Repositories::$userRepository->findOneById($userId);
        $isOldPasswordIsCorrect = password_verify($oldPassword, $user->passwordHash);
        $isOldPasswordEqualsNew = password_verify(password_hash($newPassword, PASSWORD_DEFAULT), $user->passwordHash);

        if (!$isOldPasswordIsCorrect) AuthController::redirect('panel', ["message" => "Stare hasło jest nieprawidłowe."]);
        if ($isOldPasswordEqualsNew) AuthController::redirect('panel', ["message" => "Stare hasło jest identyczne jak stare."]);

        Repositories::$userRepository->updateOneById(["passwordHash" => password_hash($newPassword, PASSWORD_DEFAULT)], $userId);
        AuthController::redirect('panel');
    }

    public static final function changeUsername(AvocadoRequest $req): void {
        AuthController::authenticationMiddleware();
        $username = $req->body['new-username'] ?? null;

        if (!$username) AuthController::redirect('panel', ["message" => "Nazwa uzytkownika musi byc podana"]);

        Repositories::$userRepository->updateOneById(["username" => $username], $_SESSION['id']);
        AuthController::redirect('panel');
    }

    public static final function changeEmail(AvocadoRequest $req): void {
        AuthController::authenticationMiddleware();
        $email = $req->body['new-email'] ?? null;

        if (!$email) AuthController::redirect('panel', ["message" => "Email musi byc podany."]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) AuthController::redirect('panel', ["message" => "Nieprawidłowy email"]);

        Repositories::$userRepository->updateOneById(["email" => $email], $_SESSION['id']);
        AuthController::redirect('panel', []);
    }

    public static final function logout() {
        unset($_SESSION['id']);
        AuthController::redirect('login');
    }
}