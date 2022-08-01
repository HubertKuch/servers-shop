<?php

namespace Servers\Models;


use Avocado\ORM\Attributes\Field;
use Avocado\ORM\Attributes\Id;
use Avocado\ORM\Attributes\Table;
use Carbon\Carbon;
use Exception;
use Servers\Models\enumerations\UserRole;
use Servers\Repositories;
use Servers\Services\ActivationService;
use function Symfony\Component\Translation\t;

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
    private ?string $rememberPasswordToken = null;
    #[Field]
    private int $pterodactylId;

    public function __construct(string $username, string $email, string $password, int $activationCode, int $pterodactylId) {
        $this->username = $username;
        $this->email = $email;
        $this->passwordHash = $password;
        $this->role = UserRole::USER->value;
        $this->activationCode = $activationCode;
        $this->activationCodeExpiresIn = time() + 60 * 15;
        $this->pterodactylId = $pterodactylId;
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

    public function generateActivationCode(): int {
        $code = ActivationService::generateVerificationCode();
        $expiresIn = Carbon::now()->addMinutes(15)->getTimestamp();


        $this->activationCode = $code;
        $this->activationCodeExpiresIn = $expiresIn;

        Repositories::$userRepository->updateOne([
            "activationCode" => $code,
            "activationCodeExpiresIn" => $expiresIn
        ], ["email" => $this->email]);

        return $code;
    }

    public function getRememberPasswordToken(): string|null { return $this->rememberPasswordToken; }
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

        return "http://".$_SERVER['SERVER_NAME']."/index.php/remember-password?".http_build_query(['token' => $token]);
    }

    public static final function isValidRememberPasswordToken(string $token): bool {
        return !is_null(Repositories::$userRepository->findOne(['rememberPasswordToken' => $token]));
    }

    public function getPterodactylId(): int {
        return $this->pterodactylId;
    }

    public function setPterodactylId(int $pterodactylId): void {
        $this->pterodactylId = $pterodactylId;
    }
}
