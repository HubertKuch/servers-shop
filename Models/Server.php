<?php

namespace Servers\Models;

use Avocado\ORM\Field;
use Avocado\ORM\Id;
use Avocado\ORM\Table;
use Avocado\ORM\IgnoreFieldType;

#[Table('servers')]
class Server {
    #[Id]
    private int $id;
    #[Field]
    private string $title;
    #[Field]
    #[IgnoreFieldType]
    private string $status;
    #[Field]
    private int $createDate;
    #[Field]
    private int $expireDate;
    #[Field('package_id')]
    private int $packageId;
    #[Field]
    private ?int $payment_id = null;
    #[Field('user_id')]
    private int $user_id;
    #[Field('pterodactyl_id')]
    private int $pterodactyl_id;

    public function __construct(string $title, string $status, int $createDate, int $expireDate, int $packageId, int $userId, int $pterodactylId) {
        $this->title = $title;
        $this->status = $status;
        $this->createDate = $createDate;
        $this->expireDate = $expireDate;
        $this->packageId = $packageId;
        $this->user_id = $userId;
        $this->pterodactyl_id = $pterodactylId;
    }

    public function getId(): int { return $this->id; }

    public function getTitle(): string { return $this->title; }

    public function getStatus(): string { return $this->status; }

    public function getCreateDate(): int { return $this->createDate; }

    public function getExpireDate(): int { return $this->expireDate; }

    public function getPackage(): string { return $this->package; }

    public function getPaymentId(): int { return $this->payment_id; }

    public function setTitle(string $title): void { $this->title = $title; }

    public function setStatus(string $status): void { $this->status = $status; }

    public function setCreateDate(int $createDate): void { $this->createDate = $createDate; }

    public function setExpireDate(int $expireDate): void { $this->expireDate = $expireDate; }

    public function setPackage(string $package): void { $this->package = $package; }

    public function setPaymentId(int $payment_id): void { $this->payment_id = $payment_id; }
}
