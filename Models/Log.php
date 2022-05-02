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
    private LogType $type;
    #[Field]
    private int $userId;
    #[Field]
    private int $productId;
    #[Field]
    private int $paymentId;
    #[Field]
    private int $timestamp;
    #[Field]
    private string $message;

    /**
     * @param LogType $type
     * @param int $userId
     * @param int $productId
     * @param int $paymentId
     * @param int $timestamp
     * @param string $message
     */
    public function __construct(LogType $type, int $userId, int $productId, int $paymentId, int $timestamp, string $message) {
        $this->type = $type;
        $this->userId = $userId;
        $this->productId = $productId;
        $this->paymentId = $paymentId;
        $this->timestamp = $timestamp;
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getUserId(): int {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getProductId(): int {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getPaymentId(): int {
        return $this->paymentId;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * @param LogType $type
     */
    public function setType(LogType $type): void {
        $this->type = $type;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void {
        $this->userId = $userId;
    }

    /**
     * @param int $productId
     */
    public function setProductId(int $productId): void {
        $this->productId = $productId;
    }

    /**
     * @param int $paymentId
     */
    public function setPaymentId(int $paymentId): void {
        $this->paymentId = $paymentId;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp(int $timestamp): void {
        $this->timestamp = $timestamp;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void {
        $this->message = $message;
    }
}