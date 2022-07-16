<?php

namespace Servers\Models;

use Avocado\ORM\Attributes\Field;
use Avocado\ORM\Attributes\Table;
use Avocado\ORM\Attributes\Id;
use http\Header;
use http\Params;
use Servers\Models\enumerations\PaymentMethods;
use Servers\Models\enumerations\PaymentType;
use Servers\Services\PaymentsService;

#[Table('payments')]
class Payment {
    #[Id]
    private int $id;
    #[Field("paymentDate")]
    private ?int $paymentDate;
    #[Field]
    private int $createDate;
    #[Field]
    private string $ipAddress;
    #[Field]
    private string $status;
    #[Field]
    private float $sum;
    #[Field('after_due')]
    private float $afterDue;
    #[Field]
    private ?float $wallet_after_operation;
    #[Field]
    private ?string $method;
    #[Field]
    private int $user_id;
    #[Field]
    private string $tid;
    #[Field('payment_status')]
    private int $payment_status = 0;
    #[Field('payment_type')]
    private ?PaymentType $paymentType;
    #[Field("charged_user_id")]
    private ?int $chargedUserId;

    public function __construct(?int $paymentDate,
                                int $createDate,
                                string $ipAddress,
                                string $status,
                                float $sum,
                                ?float $wallet_after_operation,
                                ?PaymentMethods $method,
                                int $user_id,
                                string $tid,
                                ?int $chargedUserId = null,
                                PaymentType $paymentType = PaymentType::OWN,
    ) {
        $this->paymentDate = $paymentDate;
        $this->createDate = $createDate;
        $this->ipAddress = $paymentType == PaymentType::OWN ? $ipAddress : "";
        $this->status = $status;
        $this->sum = $sum;
        $this->wallet_after_operation = $wallet_after_operation;
        $this->afterDue = $method ? $this->calculateDue(PaymentsService::PAYMENT_DUE_ENV_NAMES[$method->value]) : 0;
        $this->method = $method instanceof PaymentMethods ? $method->value : $method;
        $this->user_id = $user_id;
        $this->tid = $tid;
        $this->paymentType = $paymentType;
        $this->chargedUserId = $chargedUserId;
    }

    public function getId(): int { return $this->id; }

    public function getPaymentDate(): int|null { return $this->paymentDate; }
    public function setPaymentDate(int $paymentDate): void { $this->paymentDate = $paymentDate; }

    public function getCreateDate(): int { return $this->createDate; }
    public function setCreateDate(int $createDate): void { $this->createDate = $createDate; }

    public function getIpAddress(): string { return $this->ipAddress; }
    public function setIpAddress(string $ipAddress): void { $this->ipAddress = $ipAddress; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function getSum(): float { return $this->sum; }
    public function setSum(float $sum): void { $this->sum = $sum; }

    public function getMethod(): ?string { return $this->method; }
    public function setMethod(string $method): void { $this->method = $method; }

    public function getUserId(): int { return $this->user_id; }
    public function setUserId(int $user_id): void { $this->user_id = $user_id; }

    public function getTid(): string { return $this->tid; }

    public function getPaymentStatus(): int { return $this->payment_status; }

    public function getPaymentType(): PaymentType { return $this->paymentType; }
    public function setPaymentType(PaymentType $paymentType): void { $this->paymentType = $paymentType; }

    public function getAfterDue(): float { return $this->afterDue; }
    public function setAfterDue(float $afterDue): void { $this->afterDue = $afterDue; }

    public function getChargedUserId(): ?int { return $this->chargedUserId; }
    public function setChargedUserId(?int $chargedUserId): void { $this->chargedUserId = $chargedUserId; }

    public function getWalletAfterOperation(): ?float { return $this->wallet_after_operation; }
    public function setWalletAfterOperation(?float $wallet_after_operation): void { $this->wallet_after_operation = $wallet_after_operation; }

    public function calculateDue(string $paymentDueEnvName): float {
        $duePercent = floatval($_ENV[$paymentDueEnvName]);

        return ($duePercent/100) * $this->sum;
    }
}
