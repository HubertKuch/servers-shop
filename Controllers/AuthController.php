<?php

namespace Servers\Controllers;

use Servers\Models\UserRole;
use Servers\Repositories;

class AuthController {
    private static function isLoggedIn(): bool {
        return isset($_SESSION['id']);
    }

    public static function authenticationMiddleware(?array $messages = []): void {
        $isLoggedIn = self::isLoggedIn();

        if(!$isLoggedIn) {
            self::redirect('login', $messages);
            return;
        }

        $user = Repositories::$userRepository->findOneById($_SESSION['id']);

        $isActivated = $user->isActivated;
        if (!$isActivated) {
            self::redirect('activation');
        }
    }

    public static function restrictTo(string ...$roles) {
        $user = Repositories::$userRepository->findOneById($_SESSION['id'] ?? 0);

        if(!in_array($user->role, $roles)) {
            AuthController::redirect('');
        }
    }

    public static function redirect(string $to, ?array $messages = []): void {
        $mainDir = getenv('MAIN_DIR');
        $messages  = http_build_query($messages);

        header("Location: /$mainDir/$to?$messages");
        die();
    }
}
