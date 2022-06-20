<?php

namespace Servers\Controllers;

use Avocado\Router\AvocadoRequest;
use HCGCloud\Pterodactyl\Exceptions\ValidationException;
use HCGCloud\Pterodactyl\Pterodactyl;
use PDO;
use Servers\Models\User;
use Servers\Repositories;
use Servers\Services\ActivationService;
use Servers\Services\MailService;

class UserController {
    private static Pterodactyl $pterodactyl;
    private static PDO $pterodactylDatabase;

    public static final function init(Pterodactyl $pterodactyl): void {
        self::$pterodactyl = $pterodactyl;
        self::$pterodactylDatabase = new PDO("mysql:dbname=panel;host=178.32.202.241;port=3306", "PAWCIOxKOKS", "E0O(N*Jhbv)m@Rnl");
    }

    public static final function login(AvocadoRequest $req): void {
        if (!isset($req->body['username']) || !isset($req->body['password'])) AuthController::redirect('login', ["message" => "Nazwa użytkownika lub email i hasło muszą być prowadzone."]);

        $user = Repositories::$userRepository->findOne(["username" => $req->body['username']]);

        if (!$user) AuthController::redirect('login', ["message" => "Nieprawidlowe dane"]);

        $isCorrectPassword = password_verify($req->body['password'], $user->getPasswordHash());

        if (!$isCorrectPassword) AuthController::redirect('login', ["message" => "Nieprawidlowe dane"]);
        $pterodactylUser = self::$pterodactylDatabase->prepare("SELECT * FROM users WHERE users.email = :email");
        $userEmail = $user->getEmail();

        $pterodactylUser->bindParam(":email", $userEmail);
        $pterodactylUser->execute();
        $pterodactylUser = $pterodactylUser->fetchAll(PDO::FETCH_CLASS)[0];

        $_SESSION['pterodactyl_user_id'] = $pterodactylUser->id;
        $_SESSION['id'] = $user->getId();
        LogsController::saveUserLoginLog($user->getId(), $user->getUsername());
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
        $isUsernameIsBusy  = Repositories::$userRepository->findOne(["username" => $username]);

        if ($isEmailIsBusy) AuthController::redirect('register', ["message" => "Email jest zajety"]);
        if ($isUsernameIsBusy) AuthController::redirect('register', ["message" => "Nazwa użytkownika jest zajeta"]);

        $verificationCode = ActivationService::generateVerificationCode();
        $user = new User($username, $email, $passwordHash, $verificationCode);
        $mailService = new MailService();

        try {
            $pterodactylUser = self::$pterodactyl->createUser([
                "email" => $email,
                "username" => $username,
                "first_name" => $username,
                "last_name" => $username
            ]);

            Repositories::$userRepository->save($user);
            $userId = Repositories::$userRepository->findOne(["email" => $email])->getId();
            $mailService->sendVerificationMail($user->getEmail(), $verificationCode);

            self::$pterodactyl->updateUser($pterodactylUser->id, [
                "email" => $email,
                "username" => $username,
                "first_name" => $username,
                "last_name" => $username,
                "password" => $password
            ]);

            $_SESSION['email'] = $user->getEmail();
            LogsController::saveUserRegisterLog($userId);
            AuthController::redirect('');
        } catch (ValidationException $e) {
            AuthController::redirect('register', ["message" => "Email jest zajety"]);
        }
    }

    public static final function changePassword(AvocadoRequest $req): void {
        AuthController::authenticationMiddleware([]);

        $oldPassword = $req->body['old-password'] ?? null;
        $newPassword = $req->body['new-password'] ?? null;

        if (!$oldPassword || !$newPassword) AuthController::redirect('panel', ["message" => "Stare i nowe hasło muszą byc podane."]);

        $userId = $_SESSION['id'];
        $user = Repositories::$userRepository->findOneById($userId);
        $isOldPasswordIsCorrect = password_verify($oldPassword, $user->getPasswordHash());
        $isOldPasswordEqualsNew = password_verify(password_hash($newPassword, PASSWORD_DEFAULT), $user->getPasswordHash());

        if (!$isOldPasswordIsCorrect) AuthController::redirect('panel', ["message" => "Stare hasło jest nieprawidłowe."]);
        if ($isOldPasswordEqualsNew) AuthController::redirect('panel', ["message" => "Stare hasło jest identyczne jak stare."]);

        Repositories::$userRepository->updateOneById(["passwordHash" => password_hash($newPassword, PASSWORD_DEFAULT)], $userId);
        self::$pterodactyl->user($_SESSION['pterodactyl_user_id'], [
            "email" => $user->getEmail(),
            "username" => $user->getUsername(),
            "first_name" => $user->getUsername(),
            "last_name" => $user->getUsername(),
            "language" => "pl",
            "password" => $newPassword
        ]);
        AuthController::redirect('panel');
    }

    public static final function activateAccount(AvocadoRequest $req): void {
        $code = $req->body['activation-code'] ?? null;

        if (!$code || strlen($code) == 0) AuthController::redirect('account-activation', ["message" => "Kod aktywacyjny nie jest wprowadzony."]);

        $code = (int)$code;

        if (!ActivationService::isCorrectCode($code)) AuthController::redirect('account-activation', ["message" => "Kod aktywacyjny jest niepoprawny"]);
        if (ActivationService::isExpired($code)) AuthController::redirect('account-activation', ["message" => "Kod aktywacyjny wygasł. Zaloguj się ponownie by wygenerować nowy."]);

        ActivationService::activeAccountByCode($code);
        AuthController::redirect('login');
    }

    public static final function generateActivationCode(AvocadoRequest $req): void {
        $email = $req->params['email'] ?? null;

        if(!$email) AuthController::redirect('account-activation', ["message" => "Kod nie może zostać wysłany ponownie. Skontaktuj się z administratorem domeny."]);

        $verificationCode = ActivationService::generateVerificationCode();
        Repositories::$userRepository->updateOne(["activationCode" => $verificationCode, "activationCodeExpiresIn" => time() + 60 * 15], ["email" => $email]);

        $mailService = new MailService();
        $mailService->sendVerificationMail($email, $verificationCode);
        AuthController::redirect('account-activation');
    }

    public static final function logout(): void {
        unset($_SESSION['id']);
        AuthController::redirect('login');
    }
}
