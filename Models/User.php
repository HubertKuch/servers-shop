<?php

namespace Servers\Models;


use Avocado\ORM\Attributes\Field;
use Avocado\ORM\Attributes\Table;
use Avocado\ORM\Attributes\Id;
use Carbon\Carbon;
use Exception;
use Servers\Repositories;

#[Table('users')]
class User {
    #[Id]
    private int $id;
    #[Field]
    private string $username;
    #[Field]
    private string $email;
    #[Field]
    private string $passwordHash;
    #[Field]
    private float $wallet = 0;
    #[Field]
    private string $role;
    #[Field]
    private int $isActivated = 0;
    #[Field]
    private int $activationCode;
    #[Field]
    private int $activationCodeExpiresIn;
    #[Field]
    private ?string $rememberPasswordToken;

    public function __construct(string $username, string $email, string $password, int $activationCode) {
        $this->username = $username;
        $this->email = $email;
        $this->passwordHash = $password;
        $this->role = UserRole::USER->value;
        $this->activationCode = $activationCode;
        $this->activationCodeExpiresIn = time() + 60 * 15;
    }

    public function getId(): int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): void { $this->username = $username; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getPasswordHash(): string { return $this->passwordHash; }
    public function setPasswordHash(string $passwordHash): void { $this->passwordHash = $passwordHash; }

    public function getWallet(): float { return $this->wallet; }
    public function setWallet(float $wallet): void { $this->wallet = $wallet; }

    public function getRole(): string { return $this->role; }

    public function getIsActivated(): int { return $this->isActivated; }
    public function getActivationCode(): int { return $this->activationCode; }
    public function getActivationCodeExpiresIn(): int { return $this->activationCodeExpiresIn; }

    public function getRememberPasswordToken(): string { return $this->rememberPasswordToken; }
    public function generateRememberPasswordToken(): string {
        try {
            $token = bin2hex(random_bytes(64));

            $this->rememberPasswordToken = $token;
            Repositories::$userRepository->updateOneById(['rememberPasswordToken' => $token], $this->id);

            return $token;
        } catch (Exception) {
            return hex2bin(Carbon::now()->getTimestamp());
        }
    }

    public function generateRememberPasswordURL(string $token = null): string {
        if (!$token) {
            $token = $this->generateRememberPasswordToken();
        }

        return $_SERVER['SERVER_NAME']."/remember-password?".http_build_query(['token' => $token]);
    }

    public static final function isValidRememberPasswordToken(string $token): bool {
        return !is_null(Repositories::$userRepository->findOne(['rememberPasswordToken' => $token]));
    }
}
