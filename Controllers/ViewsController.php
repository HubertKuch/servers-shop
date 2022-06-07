<?php

namespace Servers\Controllers;

use Avocado\ORM\FindForeign;
use Avocado\Router\AvocadoRequest;
use Avocado\Router\AvocadoResponse;
use Servers\Models\ServerStatus;
use Servers\Models\UserRole;
use Servers\Repositories;

class ViewsController {
    public static final function main(): void {
        AuthController::authenticationMiddleware(["error" => "Unauthorized"]);

        $servers = Repositories::$productsRepository->findMany([
            "user_id" => $_SESSION['id']
        ]);

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
        $userServers = Repositories::$productsRepository->findOneToManyRelation($findForeignBoughtServers, ["status" => ServerStatus::IN_MAGAZINE->value]);

        $isAdmin = $user->getRole() === UserRole::ADMIN->value;

        foreach ($userServers as $server)
            if ($server->getExpireDate() < time())
                ServersController::suspendServer($server);

        $userServers = Repositories::$productsRepository->findOneToManyRelation($findForeignBoughtServers, ["status" => ServerStatus::IN_MAGAZINE->value]);

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

    public static final function login(AvocadoRequest $req): void {
        $errors = $req->query;
        require "views/login.php";
    }

    public static final function register(AvocadoRequest $req): void {
        $errors = $req->query;
        require "views/register.php";
    }

    public static final function userSettings(AvocadoRequest $req): void {
        $errors = $req->query;
        require "views/userPanel/settings.php";
    }

    public static final function userPayments(AvocadoRequest $req): void {
        $errors = $req->query;

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
        $userServers = Repositories::$productsRepository->findOneToManyRelation($findForeignBoughtServers, ["status" => ServerStatus::IN_MAGAZINE->value]);

        $isAdmin = $user->getRole() === UserRole::ADMIN->value;

        foreach ($userServers as $server)
            if ($server->getExpireDate() < time())
                ServersController::suspendServer($server);

        $userServers = Repositories::$productsRepository->findOneToManyRelation($findForeignBoughtServers, ["status" => ServerStatus::IN_MAGAZINE->value]);
        require "views/userPanel/payments.php";
    }

    public static final function userServerList(AvocadoRequest $req): void {
        $errors = $req->query;

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
        $userServers = Repositories::$productsRepository->findOneToManyRelation($findForeignBoughtServers, ["status" => ServerStatus::IN_MAGAZINE->value]);

        $isAdmin = $user->getRole() === UserRole::ADMIN->value;

        foreach ($userServers as $server)
            if ($server->getExpireDate() < time())
                ServersController::suspendServer($server);

        $userServers = Repositories::$productsRepository->findOneToManyRelation($findForeignBoughtServers, ["status" => ServerStatus::IN_MAGAZINE->value]);
        require "views/userPanel/serverList.php";
    }

    public static final function userRecharge(AvocadoRequest $req): void {
        $errors = $req->query;
        require "views/userPanel/recharge.php";
    }

    public static final function accountActivation(): void {
        $errors = $_GET;
        require "views/accountActivation.php";
    }

    public static final function accountActivated(): void {
        AuthController::notForLoggedIn();

        require "views/accountActivated.php";
    }

    public static final function servers(): void {
        $errors = $_GET;
        $packages = Repositories::$packagesRepository->findMany();
        require "views/servers.php";
    }
}
