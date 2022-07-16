<?php

namespace Servers\Models\enumerations;

enum LogType: string {
    case AUTH = 'auth';
    case PAYMENT = 'payment';
    case PRODUCT = 'product';
}
