<?php

namespace Servers\Models;

use Avocado\ORM\Field;
use Avocado\ORM\Id;
use Avocado\ORM\Table;

#[Table('logs')]
class Log {
    #[Id]
    private int $id;
    #[Field]
    private string $type;
    #[Field]
    private ?int $userId;
    #[Field]
    private ?int $productId;
    #[Field]
    private ?int $paymentId;
    #[Field]
    private string $timestamp;
    #[Field]
    private string $message;

    public function __construct(string $type, ?int $userId, ?int $productId, ?int $paymentId, string $message) {
        $this->type = $type;
        $this->userId = $userId;
        $this->productId = $productId;
        $this->paymentId = $paymentId;
        $this->message = $message;
        $this->timestamp = date( 'Y-m-d H:i:s', time());
    }

    public function getId(): int { return $this->id; }

    public function getType(): int { return $this->type; }

    public function getUserId(): int { return $this->userId; }

    public function getProductId(): int { return $this->productId; }

    public function getPaymentId(): int { return $this->paymentId; }

    public function getTimestamp(): string { return $this->timestamp; }

    public function getMessage(): string { return $this->message; }

    public function setType(string $type): void { $this->type = $type; }

    public function setUserId(int $userId): void { $this->userId = $userId; }

    public function setProductId(int $productId): void { $this->productId = $productId; }

    public function setPaymentId(int $paymentId): void { $this->paymentId = $paymentId; }

    public function setTimestamp(int $timestamp): void { $this->timestamp = date( 'Y-m-d H:i:s', time()); }

    public function setMessage(string $message): void { $this->message = $message; }
}