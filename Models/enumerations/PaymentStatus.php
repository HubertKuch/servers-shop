<?php

namespace Servers\Models\enumerations;

enum PaymentStatus: string {
    case REJECTED = 'rejected';
    case INCOMING = 'incoming';
    case RESOLVED = 'resolved';
}
