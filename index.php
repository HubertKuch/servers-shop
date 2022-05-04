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

const __CONTROLLER_NAMESPACE = __NAMESPACE__."\Controllers";

$mainDir = explode('/', $_SERVER['SCRIPT_NAME'])[1];
putenv("MAIN_DIR=$mainDir");

Repositories::init();

AvocadoORMSettings::useDatabase('mysql:host=localhost;dbname=servers;', 'root', '');
AvocadoORMSettings::useFetchOption(\PDO::FETCH_CLASS);

AvocadoRouter::GET("/", [], __CONTROLLER_NAMESPACE."\ViewsController::main");
AvocadoRouter::GET('/admin', [], __CONTROLLER_NAMESPACE."\ViewsController::admin");
AvocadoRouter::GET('/login', [], __CONTROLLER_NAMESPACE."\ViewsController::login");
AvocadoRouter::GET('/register', [], __CONTROLLER_NAMESPACE."\ViewsController::register");
AvocadoRouter::GET('/panel', [], __CONTROLLER_NAMESPACE."\ViewsController::userPanel");

AvocadoRouter::GET('/api/logout', [], __CONTROLLER_NAMESPACE."\UserController::logout");
AvocadoRouter::POST('/api/login', [], __CONTROLLER_NAMESPACE."\UserController::login");
AvocadoRouter::POST('/api/register', [], __CONTROLLER_NAMESPACE."\UserController::register");
AvocadoRouter::POST('/api/change-password', [], __CONTROLLER_NAMESPACE."\UserController::changePassword");

AvocadoRouter::listen();
