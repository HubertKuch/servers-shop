<?php

namespace Servers\Models;

enum PaymentStatus: string {
    case REJECTED = 'rejected';
    case INCOMING = 'incoming';
    case RESOLVED = 'resolved';
}
