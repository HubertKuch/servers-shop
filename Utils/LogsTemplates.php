<?php

namespace Servers\Utils;

class LogsTemplates {
    public static function userLogin(int $id, string $username): string {
        return "User with $id and $username was logged in.";
    }

    public static function userRegister(int $id): string {
        return "New user: username: id - $id";
    }

    public static function payment(int $id): string {
        return "New payment: id - $id";
    }

    public static function productSold(int $id): string {
        return "Server id - $id was sold";
    }

    public static function newProduct(int $id): string {
        return "New server with id - $id";
    }
}
