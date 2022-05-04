<?php

namespace Servers\Models;

enum UserRole: string {
    case USER = 'user';
    case ADMIN = 'admin';
}
