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

$mainDir = explode('/', $_SERVER['SCRIPT_NAME'])[1];
putenv("MAIN_DIR=$mainDir");

const __USER__CONTROLLER__NAMESPACE__ = __NAMESPACE__."\Controllers\UserController::";
const __VIEWS__CONTROLLER__NAMESPACE__ = __NAMESPACE__."\Controllers\ViewsController::";

Repositories::init();

AvocadoORMSettings::useDatabase('mysql:host=localhost;dbname=servers;', 'root', '');
AvocadoORMSettings::useFetchOption(\PDO::FETCH_CLASS);

AvocadoRouter::GET("/", [], __VIEWS__CONTROLLER__NAMESPACE__."main");
AvocadoRouter::GET('/admin', [], __VIEWS__CONTROLLER__NAMESPACE__."admin");
AvocadoRouter::GET('/login', [], __VIEWS__CONTROLLER__NAMESPACE__."login");
AvocadoRouter::GET('/register', [], __VIEWS__CONTROLLER__NAMESPACE__."register");
AvocadoRouter::GET('/panel', [], __VIEWS__CONTROLLER__NAMESPACE__."userPanel");

AvocadoRouter::GET('/api/logout', [], __USER__CONTROLLER__NAMESPACE__."logout");
AvocadoRouter::POST('/api/login', [], __USER__CONTROLLER__NAMESPACE__."login");
AvocadoRouter::POST('/api/register', [], __USER__CONTROLLER__NAMESPACE__."register");
AvocadoRouter::POST('/api/change-password', [], __USER__CONTROLLER__NAMESPACE__."changePassword");
AvocadoRouter::POST('/api/change-username', [], __USER__CONTROLLER__NAMESPACE__."changeUsername");
AvocadoRouter::POST('/api/change-email', [], __USER__CONTROLLER__NAMESPACE__."changeEmail");

AvocadoRouter::listen();
