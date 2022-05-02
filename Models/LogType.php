<?php

namespace Servers\Models;

enum LogType: string {
    case AUTH = 'auth';
    case PAYMENT = 'payment';
    case PRODUCT = 'product';
}
