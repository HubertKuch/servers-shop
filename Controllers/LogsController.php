<?php

namespace Servers\Controllers;

use Servers\Models\LogType;
use Servers\Utils\LogsTemplates;
use Servers\Models\Log;
use Servers\Repositories;

class LogsController {
    public static final function saveUserLoginLog(int $userId, string $username): void {
        $message = LogsTemplates::userLogin($userId, $username);
        $log = new Log(LogType::AUTH->value, $userId, null, null, $message);

        Repositories::$logsRepository->save($log);
    }

    public static final function saveUserRegisterLog(int $id): void {
        $message = LogsTemplates::userRegister($id);
        $log = new Log(LogType::AUTH->value, $id, null, null, $message);

        Repositories::$logsRepository->save($log);
    }

    public static final function savePaymentLog(int $id): void {
        $message = LogsTemplates::payment($id);
        $log = new Log(LogType::PAYMENT->value, null, null, $id, $message);

        Repositories::$logsRepository->save($log);
    }

    public static final function saveNewProductLog(int $id): void {
        $message = LogsTemplates::newProduct($id);
        $log = new Log(LogType::PRODUCT->value, null, $id, null, $message);

        Repositories::$logsRepository->save($log);
    }

    public static final function saveProductSold(int $id): void {
        $message = LogsTemplates::productSold($id);
        $log = new Log(LogType::PRODUCT->value, null, $id, null, $message);

        Repositories::$logsRepository->save($log);
    }
}