<?php

namespace Servers\Controllers;

use Avocado\Router\AvocadoRequest;
use Avocado\Router\AvocadoResponse;
use Servers\Models\ProductStatus;
use Servers\Repositories;

class ViewsController {
    public static final function main(): void {
        AuthController::authenticationMiddleware(["error" => "Unauthorized"]);
        require "./views/main.php";
    }

    public static final function admin(): void{
        AuthController::authenticationMiddleware(["error" => "Unauthorized"]);

        $payments = Repositories::$paymentsRepository->findMany();

        $soldServers = Repositories::$productsRepository->findMany([
            "status" => ProductStatus::SOLD->value
        ]);

        $logs = Repositories::$logsRepository->findMany();

        require "./views/admin.php";
    }

    public static final function login(AvocadoRequest $req, AvocadoResponse $res): void {
        $errors = $req->query;
        require "views/login.php";
    }

    public static final function register(AvocadoRequest $req): void {
        $errors = $req->query;
        require "views/register.php";
    }
}