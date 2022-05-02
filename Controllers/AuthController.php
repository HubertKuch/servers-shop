<?php

namespace Servers\Controllers;

function redirectToLoginViewWithMessage(array $messages): void {
    $mainDir = getenv('MAIN_DIR');
    $messages  = http_build_query($messages);

    header("Location: /$mainDir/login?$messages");
}

class AuthController {
    public static function isLoggedIn(): bool {
        return isset($_SESSION['id']);
    }

    public static function authenticationMiddleware(array $messages): void {
        $isLoggedIn = self::isLoggedIn();

        if(!$isLoggedIn) {
            redirectToLoginViewWithMessage($messages);
        }
    }
}
