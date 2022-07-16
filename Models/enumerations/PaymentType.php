<?php

namespace Servers\Models\enumerations;

enum PaymentType: string {
    case FUND = 'fund';
    case OWN = 'own';
}
