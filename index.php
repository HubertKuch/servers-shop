<?php

namespace Servers;

require "vendor/autoload.php";

// DEV
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

use Avocado\Router\AvocadoRouter;
use Avocado\ORM\AvocadoORMSettings;
use HCGCloud\Pterodactyl\Pterodactyl;
use Servers\Controllers\ServersController;
use Servers\Controllers\UserController;
use Servers\Controllers\ViewsController;
use Servers\Models\Server;

$mainDir = explode('/', $_SERVER['SCRIPT_NAME'])[1];
putenv("MAIN_DIR=$mainDir");
putenv("ROOT_PTERODACTYL_API_KEY=GIiB37cjhWGRMYwejj0XFsllqDgx5jghvvbdoGctj7dDsPl3");
putenv("PTERODACTYL_IP=178.32.202.241:85");
putenv("ENVIRONMENT=DEVELOPMENT");

$pterodactyl = new Pterodactyl(getenv("ROOT_PTERODACTYL_API_KEY"), getenv("PTERODACTYL_IP"));

Repositories::init();
ServersController::init($pterodactyl);
UserController::init($pterodactyl);

AvocadoORMSettings::useDatabase("mysql:host=localhost;dbname=servers;port=3306;", "root", "");
AvocadoORMSettings::useFetchOption(\PDO::FETCH_CLASS);

// VIEWS
AvocadoRouter::GET("/",                                     [], [ViewsController::class, "main"]);
AvocadoRouter::GET("/admin",                                [], [ViewsController::class, "admin"]);
AvocadoRouter::GET("/login",                                [], [ViewsController::class, "login"]);
AvocadoRouter::GET("/register",                             [], [ViewsController::class, "register"]);
AvocadoRouter::GET("/panel",                                [], [ViewsController::class, "userPanel"]);
AvocadoRouter::GET("/account-activation",                   [], [ViewsController::class, "accountActivation"]);
AvocadoRouter::GET("/account-activated",                    [], [ViewsController::class, "accountActivated"]);
AvocadoRouter::GET("/servers",                              [], [ViewsController::class, "servers"]);

// USER PANEL ACTIONS
AvocadoRouter::GET("/api/logout",                           [], [UserController::class, "logout"]);
AvocadoRouter::GET("/api/generate-activation-code/:email",  [], [UserController::class, "generateActivationCode"]);
AvocadoRouter::POST("/api/login",                           [], [UserController::class, "login"]);
AvocadoRouter::POST("/api/register",                        [], [UserController::class, "register"]);
AvocadoRouter::PATCH("/api/change-password",                [], [UserController::class, "changePassword"]);
AvocadoRouter::PATCH("/api/change-username",                [], [UserController::class, "changeUsername"]);
AvocadoRouter::PATCH("/api/change-email",                   [], [UserController::class, "changeEmail"]);
AvocadoRouter::PATCH("/api/activate-account",               [], [UserController::class, "activateAccount"]);
AvocadoRouter::PATCH("/api/activate-account",               [], [UserController::class, "activateAccount"]);

// SERVERS ACTIONS
AvocadoRouter::POST("/api/create-server",                   [], [ServersController::class, "create"]);
AvocadoRouter::PATCH("/api/unsuspend-server/:id",           [], [ServersController::class, "unSuspendServer"]);

AvocadoRouter::listen();
