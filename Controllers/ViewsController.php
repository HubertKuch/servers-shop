<?php

namespace Servers\Controllers;

use Avocado\ORM\FindForeign;
use Avocado\Router\AvocadoRequest;
use Avocado\Router\AvocadoResponse;
use Servers\Models\ProductStatus;
use Servers\Models\UserRole;
use Servers\Repositories;

class ViewsController {
    public static final function main(): void {
        AuthController::authenticationMiddleware(["error" => "Unauthorized"]);
        require "./views/main.php";
    }

    public static final function userPanel(AvocadoRequest $req): void {
        AuthController::authenticationMiddleware(["error" => "Unauthorized"]);
        $userId = $_SESSION['id'];
        $findForeignPayments = new FindForeign();
        $findForeignBoughtServers = new FindForeign();

        $findForeignPayments
            -> key("user_id")
            -> reference("users")
            -> by("id")
            -> equals($userId);

        $findForeignBoughtServers
            -> key("user_id")
            -> reference("users")
            -> by("id")
            -> equals($userId);

        $user = Repositories::$userRepository->findOneById($userId);
        $payments = Repositories::$paymentsRepository->findOneToManyRelation($findForeignPayments);
        $boughtServers = Repositories::$productsRepository->findOneToManyRelation($findForeignBoughtServers, ["status" => ProductStatus::SOLD->value]);
        $userServers = Repositories::$productsRepository->findOneToManyRelation($findForeignBoughtServers, ["status" => ProductStatus::IN_MAGAZINE->value]);

        $isAdmin = $user->role === UserRole::ADMIN->value;

        require "views/userPanel.php";
    }

    public static final function admin(): void{
        AuthController::authenticationMiddleware();
        AuthController::restrictTo(UserRole::ADMIN->value);

        $payments = Repositories::$paymentsRepository->findMany();
        $soldServers = Repositories::$productsRepository->findMany();
        $logs = Repositories::$logsRepository->findMany();
        $users = Repositories::$userRepository->findMany();

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