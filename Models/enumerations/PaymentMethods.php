<?php

namespace Servers\Models\enumerations;

enum PaymentMethods: int {
    case PAYPAL = 4;
    case PAY_SAFE_CARD = 8;
    case G2A_PAY = 32;
    case JUST_PAY = 64;
    case CASH_BILL_BANK_TRANSFER = 128;
    case CASH_BILL_SMS = 256;

    public static function get(string $name): null|PaymentStatus {
        $name = strtoupper(trim($name));
        if(empty($name))
            return null;

        foreach(PaymentStatus::cases() as $status)
        {
            if($status->name == $name)
                return $status;
        }
        return null;
    }
}
