<?php

namespace Servers\Models;

use Avocado\ORM\Attributes\Field;
use Avocado\ORM\Attributes\Table;
use Avocado\ORM\Attributes\Id;

#[Table('payments')]
class Payment {
    #[Id]
    private int $id;
    #[Field]
    private int $paymentDate;
    #[Field]
    private int $createDate;
    #[Field]
    private string $ipAddress;
    #[Field]
    private string $status;
    #[Field]
    private float $sum;
    #[Field]
    private string $method;
    #[Field]
    private int $user_id;
    #[Field]
    private string $tid;

    public function __construct(int $paymentDate, int $createDate, string $ipAddress, string $status, float $sum, string $method, int $user_id, string $tid) {
        $this->paymentDate = $paymentDate;
        $this->createDate = $createDate;
        $this->ipAddress = $ipAddress;
        $this->status = $status;
        $this->sum = $sum;
        $this->method = $method;
        $this->user_id = $user_id;
        $this->tid = $tid;
    }

    public function getId(): int { return $this->id; }

    public function getPaymentDate(): int { return $this->paymentDate; }

    public function setPaymentDate(int $paymentDate): void { $this->paymentDate = $paymentDate; }

    public function getCreateDate(): int { return $this->createDate; }

    public function setCreateDate(int $createDate): void { $this->createDate = $createDate; }

    public function getIpAddress(): string { return $this->ipAddress; }

    public function setIpAddress(string $ipAddress): void { $this->ipAddress = $ipAddress; }

    public function getStatus(): string { return $this->status; }

    public function setStatus(string $status): void { $this->status = $status; }

    public function getSum(): float { return $this->sum; }

    public function setSum(float $sum): void { $this->sum = $sum; }

    public function getMethod(): string { return $this->method; }

    public function setMethod(string $method): void { $this->method = $method; }

    public function getUserId(): int { return $this->user_id; }

    public function setUserId(int $user_id): void { $this->user_id = $user_id; }
}
