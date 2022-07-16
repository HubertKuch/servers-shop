<?php

namespace Servers\Models\enumerations;

enum PaymentType: string {
    case FUND = 'fund';
    case OWN = 'own';
    case BOUGHT_SERVER = 'server_bought';
    case RENEW_SERVER = 'server_renew';
}
