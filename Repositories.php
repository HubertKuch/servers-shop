<?php

namespace Servers;

use Servers\Models\Package;
use Servers\Models\Payment;
use Servers\Models\Server;
use Servers\Models\User;
use Avocado\ORM\AvocadoRepository;
use Servers\Models\Log;

class Repositories {
    public static AvocadoRepository $userRepository;
    public static AvocadoRepository $paymentsRepository;
    public static AvocadoRepository $productsRepository;
    public static AvocadoRepository $logsRepository;
    public static AvocadoRepository $packagesRepository;

    public static function init(): void {
        self::$userRepository = new AvocadoRepository(User::class);
        self::$paymentsRepository = new AvocadoRepository(Payment::class);
        self::$productsRepository = new AvocadoRepository(Server::class);
        self::$logsRepository = new AvocadoRepository(Log::class);
        self::$packagesRepository = new AvocadoRepository(Package::class);
    }
}
