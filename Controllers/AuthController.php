<?php

namespace Servers\Controllers;

function redirect(string $point, array $messages): void {
    $mainDir = getenv('MAIN_DIR');
    $messages  = http_build_query($messages);

    header("Location: /$mainDir/$point?$messages");
}

class AuthController {
    public static function isLoggedIn(): bool {
        return isset($_SESSION['id']);
    }

    public static function authenticationMiddleware(array $messages): void {
        $isLoggedIn = self::isLoggedIn();

        if(!$isLoggedIn) {
            redirect('login', $messages);
        }
    }

    public static function redirectToLoginWithMessage(array $error) {
        redirect('login', $error);
    }

    public static function redirect(string $to, array $messages) {
        redirect($to, $messages);
    }
}
