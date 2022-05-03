<?php

namespace Servers\Models;

use Avocado\ORM\Field;
use Avocado\ORM\Id;
use Avocado\ORM\Table;
use Avocado\ORM\IgnoreFieldType;

#[Table('products')]
class Product {
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
    #[Field]
    private string $package;
    #[Field]
    private int $payment_id;

    /**
     * @param string $title
     * @param string $status
     * @param int $createDate
     * @param int $expireDate
     * @param string $package
     * @param int $payment_id
     */
    public function __construct(string $title, string $status, int $createDate, int $expireDate, string $package, int $payment_id) {
        $this->title = $title;
        $this->status = $status;
        $this->createDate = $createDate;
        $this->expireDate = $expireDate;
        $this->package = $package;
        $this->payment_id = $payment_id;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getStatus(): string {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getCreateDate(): int {
        return $this->createDate;
    }

    /**
     * @return int
     */
    public function getExpireDate(): int {
        return $this->expireDate;
    }

    /**
     * @return string
     */
    public function getPackage(): string {
        return $this->package;
    }

    /**
     * @return int
     */
    public function getPaymentId(): int {
        return $this->payment_id;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void {
        $this->title = $title;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void {
        $this->status = $status;
    }

    /**
     * @param int $createDate
     */
    public function setCreateDate(int $createDate): void {
        $this->createDate = $createDate;
    }

    /**
     * @param int $expireDate
     */
    public function setExpireDate(int $expireDate): void {
        $this->expireDate = $expireDate;
    }

    /**
     * @param string $package
     */
    public function setPackage(string $package): void {
        $this->package = $package;
    }

    /**
     * @param int $payment_id
     */
    public function setPaymentId(int $payment_id): void {
        $this->payment_id = $payment_id;
    }
}