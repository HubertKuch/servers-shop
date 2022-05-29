<?php

namespace Servers\Models;

enum PaymentMethods: int {
    case PAYPAL = 4;
    case PAY_SAFE_CARD = 8;
    case G2A_PAY = 32;
    case JUST_PAY = 64;
    case CASH_BILL_BANK_TRANSFER = 128;
    case CASH_BILL_SMS = 256;
}
