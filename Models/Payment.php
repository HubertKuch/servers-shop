<?php

namespace Servers\Models;

use Avocado\ORM\Field;
use Avocado\ORM\Id;
use Avocado\ORM\Table;
use Servers\Models\PaymentStatus;

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

    /**
     * @param int $paymentDate
     * @param int $createDate
     * @param string $ipAddress
     * @param string $status
     * @param float $sum
     * @param string $method
     * @param int $user_id
     */
    public function __construct(int $paymentDate, int $createDate, string $ipAddress, string $status, float $sum, string $method, int $user_id) {
        $this->paymentDate = $paymentDate;
        $this->createDate = $createDate;
        $this->ipAddress = $ipAddress;
        $this->status = $status;
        $this->sum = $sum;
        $this->method = $method;
        $this->user_id = $user_id;
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
    public function getPaymentDate(): int {
        return $this->paymentDate;
    }

    /**
     * @param int $paymentDate
     */
    public function setPaymentDate(int $paymentDate): void {
        $this->paymentDate = $paymentDate;
    }

    /**
     * @return int
     */
    public function getCreateDate(): int {
        return $this->createDate;
    }

    /**
     * @param int $createDate
     */
    public function setCreateDate(int $createDate): void {
        $this->createDate = $createDate;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress(string $ipAddress): void {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return string
     */
    public function getStatus(): string {
        return $this->status;
    }

    /**
     * @param string
     */
    public function setStatus(string $status): void {
        $this->status = $status;
    }

    /**
     * @return float
     */
    public function getSum(): float {
        return $this->sum;
    }

    /**
     * @param float $sum
     */
    public function setSum(float $sum): void {
        $this->sum = $sum;
    }

    /**
     * @return string
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void {
        $this->method = $method;
    }

    /**
     * @return int
     */
    public function getUserId(): int {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void {
        $this->user_id = $user_id;
    }
}