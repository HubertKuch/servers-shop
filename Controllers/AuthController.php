<?php

namespace Servers\Controllers;

use Servers\Models\User;
use Servers\Repositories;
use Servers\Services\MailService;
use function GuzzleHttp\Psr7\_parse_request_uri;

class AuthController {
    private static function isLoggedIn(): bool {
        return isset($_SESSION['id']);
    }

    public static function notForLoggedIn(): void {
        if (isset($_SESSION['id'])) {
            self::redirect('');
        }
    }

    public static function authenticationMiddleware(?array $messages = []): void {
        $isLoggedIn = self::isLoggedIn();

        if(!$isLoggedIn) {
            self::redirect('login', $messages);
            return;
        }

        /** @var $user User */
        $user = Repositories::$userRepository->findOneById($_SESSION['id']);
        $isActivated = $user->getIsActivated();

        if (!$isActivated) {
            (new MailService())->sendVerificationMail($user, $user->generateRememberPasswordToken());
            self::redirect("account-activation?email={$user->getEmail()}");
        }
    }

    public static function restrictTo(string ...$roles) {
        $user = Repositories::$userRepository->findOneById($_SESSION['id'] ?? 0);

        if(!in_array($user->getRole(), $roles)) {
            AuthController::redirect('');
        }
    }

    public static function redirect(string $to, ?array $messages = []): void {
        $mainDir = getenv('MAIN_DIR');
        $messages  = http_build_query($messages);

        if (!empty($messages)) {
            header("Location: /$mainDir/$to?$messages");
            die();
        }

        header("Location: /$mainDir/$to");
        die();
    }
}
