<?php

namespace Servers\Services;

use Servers\Repositories;

class ActivationService {
    public static function generateVerificationCode(): int {
        return rand(100001, 999999);
    }

    public static function isCorrectCode(int $code): bool {
        return Repositories::$userRepository->findOne(["activationCode" => $code]) !== null;
    }

    public static function isExpired(int $code): bool {
        $user = Repositories::$userRepository->findOne(["activationCode" => $code]);
        $expiresTimestamp = $user->getActivationCodeExpiresIn();

        return !(time() < intval($expiresTimestamp));
    }

    public static function activeAccountByCode(int $code): void {
        Repositories::$userRepository->updateOne(["activationCode" => 0, "isActivated" => 1], ["activationCode" => $code]);
    }
}
