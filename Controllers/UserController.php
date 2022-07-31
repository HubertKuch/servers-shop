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
        $recaptchaToken = $req->body['recaptcha-token'] ?? null;

        $recaptchaResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?".http_build_query([
            "secret" => $_ENV['SECRET_KEY'],
            "response" => $recaptchaToken
        ]));

        $recaptchaResponse = json_decode($recaptchaResponse);

        if (!($recaptchaResponse->success)) AuthController::redirect('register', ["message" => "Niepoprawna walidacja reCAPTCHA"]);

        if (!$username || !$email || !$password) AuthController::redirect('register', ["message" => "Wszystkie dane muszą być wypełnione"]);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) AuthController::redirect('register', ["message" => "Nieprawidłowy email"]);

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $isEmailIsBusy  = Repositories::$userRepository->findOne(["email" => $email]);
        $isUsernameIsBusy  = Repositories::$userRepository->findOne(["username" => $username]);

        if ($isEmailIsBusy) AuthController::redirect('register', ["message" => "Email jest zajety"]);
        if ($isUsernameIsBusy) AuthController::redirect('register', ["message" => "Nazwa użytkownika jest zajeta"]);

        $mailService = new MailService();

        $verificationCode = ActivationService::generateVerificationCode();

        try {
            $pterodactylUser = self::$pterodactyl->createUser([
                "email" => $email,
                "username" => $username,
                "first_name" => $username,
                "last_name" => $username
            ]);

            $user = new User($username, $email, $passwordHash, $verificationCode, $pterodactylUser->id);
            Repositories::$userRepository->save($user);

            $mailService->sendVerificationMail($user, $verificationCode);

            $userId = Repositories::$userRepository->findOne(["email" => $email])->getId();

            self::$pterodactyl->updateUser($pterodactylUser->id, [
                "email" => $email,
                "username" => $username,
                "first_name" => $username,
                "last_name" => $username,
                "password" => $password
            ]);

            $_SESSION['email'] = $user->getEmail();
            LogsController::saveUserRegisterLog($userId);

            AuthController::redirect("account-activation", ["email" => $user->getEmail()]);
        } catch (ValidationException $e) {
            AuthController::redirect('register', ["message" => "Email jest zajety"]);
        }
    }

    public static final function changePassword(AvocadoRequest $req): void {
        $newPassword = $req->body['new-password'] ?? null;
        $oldPassword = $req->body['old-password'] ?? null;

        /** @var $user User */
        $user = null;

        if (isset($_GET['token'])) {
            $user = Repositories::$userRepository->findOne(["rememberPasswordToken" => $_GET['token']]);
        } else {
            AuthController::authenticationMiddleware();
            /** @var $user User */
            $user = Repositories::$userRepository->findOneById($_SESSION['id']);

            if (!$oldPassword || !$newPassword)
                AuthController::redirect('settings', ['message' => 'Stare i nowe haslo musi byc podane.']);

            if (!password_verify($oldPassword, $user->getPasswordHash()))
                AuthController::redirect('settings', ['message' => 'Niepoprawne stare haslo.']);
        }

        Repositories::$userRepository->updateOneById(["passwordHash" => password_hash($newPassword, PASSWORD_DEFAULT)], $user->getId());

        self::$pterodactyl->updateUser($user->getPterodactylId(), [
            "email" => $user->getEmail(),
            "username" => $user->getUsername(),
            "first_name" => $user->getUsername(),
            "last_name" => $user->getUsername(),
            "password" => $newPassword
        ])->update([
            "email" => $user->getEmail(),
            "username" => $user->getUsername(),
            "first_name" => $user->getUsername(),
            "last_name" => $user->getUsername(),
            "password" => $newPassword
        ]);

        AuthController::redirect('');
    }

    public static final function activateAccount(AvocadoRequest $req): void {
        $code = $req->body['activation-code'] ?? null;
        $email = str_replace('%40', '@', $req->query['email']) ?? '';

        if (!$code || strlen($code) == 0) AuthController::redirect('account-activation', [
            "message"   =>  "Kod aktywacyjny nie jest wprowadzony.",
            "email"     =>  $email
        ]);

        $code = (int)$code;

        if (!ActivationService::isCorrectCode($code)) AuthController::redirect('account-activation', [
            "message"   =>  "Kod aktywacyjny jest niepoprawny",
            "email"     =>  $email
        ]);

        if (ActivationService::isExpired($code)) AuthController::redirect('account-activation', [
            "message"   =>  "Kod aktywacyjny wygasł. Zaloguj się ponownie by wygenerować nowy.",
            "email"     =>   $email
        ]);

        $user = Repositories::$userRepository->findOne(["activationCode" => $code]);
        ActivationService::activeAccountByCode($code);

        $_SESSION['id'] = $user->getId();

        AuthController::redirect('');
    }

    public static final function generateActivationCode(AvocadoRequest $req): void {
        $email = str_replace("%40", "@", $req->query['email']) ?? null;

        if(!$email) AuthController::redirect('account-activation', ["message" => "Kod nie może zostać wysłany ponownie. Skontaktuj się z administratorem domeny.", "email" => $email]);

        $verificationCode = ActivationService::generateVerificationCode();
        Repositories::$userRepository->updateOne(["activationCode" => $verificationCode, "activationCodeExpiresIn" => time() + 60 * 15], ["email" => $email]);

        $mailService = new MailService();
        $user = Repositories::$userRepository->findOne(["email" => $email]);

        $mailService->sendVerificationMail($user, $verificationCode);
        AuthController::redirect('account-activation', [
            "email" => $email
        ]);
    }

    public static final function rememberPasswordToken(AvocadoRequest $req): void {
        $email = $req->body['email'] ?? null;

        if (!$email) {
            AuthController::redirect('remember-password', ['message' => 'Email musi byc podany.']);
        }

        $user = Repositories::$userRepository->findOne(['email' => $email]);

        if (!$user) {
            AuthController::redirect('remember-password', ['message' => 'Niepoprawny email.']);
        }

        (new MailService())->sendRememberPasswordEmail($user);
        AuthController::redirect("login");
    }

    public static final function logout(): void {
        unset($_SESSION['id']);
        AuthController::redirect('login');
    }
}
