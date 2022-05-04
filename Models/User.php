<?php

namespace Servers\Models;

use Avocado\ORM\Field;
use Avocado\ORM\Id;
use Avocado\ORM\Table;

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

    public function __construct(string $username, string $email, string $password) {
        $this->username = $username;
        $this->email = $email;
        $this->passwordHash = $password;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function getPasswordHash(): string {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void {
        $this->passwordHash = $passwordHash;
    }

    public function getWallet(): float {
        return $this->wallet;
    }

    public function setWallet(float $wallet): void {
        $this->wallet = $wallet;
    }
}