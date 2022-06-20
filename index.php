<?php

namespace Servers;

require "vendor/autoload.php";

// DEV
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

use Avocado\ORM\AvocadoModelException;
use Avocado\Router\AvocadoRouter;
use Avocado\ORM\AvocadoORMSettings;
use HCGCloud\Pterodactyl\Pterodactyl;
use Servers\Controllers\ServersController;
use Servers\Controllers\UserController;
use Servers\Controllers\ViewsController;
use Dotenv\Dotenv;
use Servers\Services\PaymentsService;

set_error_handler([ViewsController::class, "internalServerError"], E_ALL);
set_exception_handler([ViewsController::class, "internalServerError"]);

Dotenv::createImmutable(__DIR__)->load();

$mainDir = explode('/', $_SERVER['SCRIPT_NAME'])[1];
putenv("MAIN_DIR=$mainDir");
putenv("ROOT_PTERODACTYL_API_KEY=GIiB37cjhWGRMYwejj0XFsllqDgx5jghvvbdoGctj7dDsPl3");
putenv("PTERODACTYL_IP=178.32.202.241:85");

$pterodactyl = new Pterodactyl(getenv("ROOT_PTERODACTYL_API_KEY"), getenv("PTERODACTYL_IP"));

$dbHost = $_ENV['DB_HOST'];
$dbPort = $_ENV['DB_PORT'];
$dbName = $_ENV['DB_NAME'];

AvocadoORMSettings::useDatabase("mysql:host=$dbHost;dbname=$dbName;port=$dbPort;", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
AvocadoRouter::useJSON();

Repositories::init();
ServersController::init($pterodactyl);
UserController::init($pterodactyl);

// VIEWS
AvocadoRouter::GET("/",                                     [], [ViewsController::class,    "main"]);
AvocadoRouter::GET("/admin",                                [], [ViewsController::class,    "admin"]);
AvocadoRouter::GET("/login",                                [], [ViewsController::class,    "login"]);
AvocadoRouter::GET("/register",                             [], [ViewsController::class,    "register"]);
AvocadoRouter::GET("/settings",                             [], [ViewsController::class,    "userSettings"]);
AvocadoRouter::GET("/payments",                             [], [ViewsController::class,    "userPayments"]);
AvocadoRouter::GET("/server-list",                          [], [ViewsController::class,    "userServerList"]);
AvocadoRouter::GET("/recharge",                             [], [ViewsController::class,    "userRecharge"]);
AvocadoRouter::GET("/recharge-friend",                      [], [ViewsController::class,    "friendRecharge"]);
AvocadoRouter::GET("/panel",                                [], [ViewsController::class,    "userPanel"]);
AvocadoRouter::GET("/account-activation",                   [], [ViewsController::class,    "accountActivation"]);
AvocadoRouter::GET("/account-activated",                    [], [ViewsController::class,    "accountActivated"]);
AvocadoRouter::GET("/servers",                              [], [ViewsController::class,    "servers"]);

// USER PANEL ACTIONS
AvocadoRouter::GET("/api/logout",                           [], [UserController::class,     "logout"]);
AvocadoRouter::GET("/api/generate-activation-code/:email",  [], [UserController::class,     "generateActivationCode"]);
AvocadoRouter::POST("/api/login",                           [], [UserController::class,     "login"]);
AvocadoRouter::POST("/api/register",                        [], [UserController::class,     "register"]);
AvocadoRouter::PATCH("/api/change-password",                [], [UserController::class,     "changePassword"]);
AvocadoRouter::PATCH("/api/activate-account",               [], [UserController::class,     "activateAccount"]);
AvocadoRouter::PATCH("/api/activate-account",               [], [UserController::class,     "activateAccount"]);

// SERVERS ACTIONS
AvocadoRouter::POST("/api/create-server",                   [], [ServersController::class,  "create"]);
AvocadoRouter::PATCH("/api/unsuspend-server/:id",           [], [ServersController::class,  "unSuspendServer"]);

// PAYMENTS ACTIONS
AvocadoRouter::PATCH("/api/add-amount",                     [], [PaymentsService::class,    "createAmountRequest"]);
AvocadoRouter::POST("/api/payment-notify",                  [], [PaymentsService::class,    "paymentNotify"]);
AvocadoRouter::notFoundHandler([ViewsController::class, "pageNotFound"]);
AvocadoRouter::listen();
