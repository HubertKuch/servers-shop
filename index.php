<?php

namespace Servers;

require "vendor/autoload.php";

// DEV
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

use Avocado\Router\AvocadoRequest;
use Avocado\Router\AvocadoResponse;
use Avocado\Router\AvocadoRoute;
use Avocado\Router\AvocadoRouter;
use Avocado\ORM\AvocadoORMSettings;
use PharIo\Version\AbstractVersionConstraint;
use Servers\Services\ActivationService;

$mainDir = explode('/', $_SERVER['SCRIPT_NAME'])[1];
putenv("MAIN_DIR=$mainDir");

const __USER__CONTROLLER__NAMESPACE__ = __NAMESPACE__."\Controllers\UserController::";
const __VIEWS__CONTROLLER__NAMESPACE__ = __NAMESPACE__."\Controllers\ViewsController::";

Repositories::init();

AvocadoORMSettings::useDatabase("mysql:host=localhost;dbname=servers;port=3306;", "root", "");
AvocadoORMSettings::useFetchOption(\PDO::FETCH_CLASS);

// VIEWS
AvocadoRouter::GET("/", [], __VIEWS__CONTROLLER__NAMESPACE__."main");
AvocadoRouter::GET('/admin', [], __VIEWS__CONTROLLER__NAMESPACE__."admin");
AvocadoRouter::GET('/login', [], __VIEWS__CONTROLLER__NAMESPACE__."login");
AvocadoRouter::GET('/register', [], __VIEWS__CONTROLLER__NAMESPACE__."register");
AvocadoRouter::GET('/panel', [], __VIEWS__CONTROLLER__NAMESPACE__."userPanel");
AvocadoRouter::GET('/account-activation', [], __VIEWS__CONTROLLER__NAMESPACE__."accountActivation");
AvocadoRouter::GET('/account-activated', [], __VIEWS__CONTROLLER__NAMESPACE__."accountActivated");

// USER PANEL ACTIONS
AvocadoRouter::GET('/api/logout', [], __USER__CONTROLLER__NAMESPACE__."logout");
AvocadoRouter::GET('/api/generate-activation-code/:email', [], __USER__CONTROLLER__NAMESPACE__."generateActivationCode");
AvocadoRouter::POST('/api/login', [], __USER__CONTROLLER__NAMESPACE__."login");
AvocadoRouter::POST('/api/register', [], __USER__CONTROLLER__NAMESPACE__."register");
AvocadoRouter::PATCH('/api/change-password', [], __USER__CONTROLLER__NAMESPACE__."changePassword");
AvocadoRouter::PATCH('/api/change-username', [], __USER__CONTROLLER__NAMESPACE__."changeUsername");
AvocadoRouter::PATCH('/api/change-email', [], __USER__CONTROLLER__NAMESPACE__."changeEmail");
AvocadoRouter::PATCH('/api/activate-account', [], __USER__CONTROLLER__NAMESPACE__."activateAccount");
AvocadoRouter::PATCH('/api/activate-account', [], __USER__CONTROLLER__NAMESPACE__."activateAccount");

AvocadoRouter::listen();
