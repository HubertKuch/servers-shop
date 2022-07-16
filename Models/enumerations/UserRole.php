<?php

namespace Servers\Models\enumerations;

enum UserRole: string {
    case USER = 'user';
    case ADMIN = 'admin';
}
