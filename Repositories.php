<?php

namespace Servers;

use Servers\Models\Payment;
use Servers\Models\User;
use Avocado\ORM\AvocadoRepository;

class Repositories {
    public static AvocadoRepository $userRepository;
    public static AvocadoRepository $paymentsRepository;

    public static function init(): void {
        self::$userRepository = new AvocadoRepository(User::class);
        self::$paymentsRepository = new AvocadoRepository(Payment::class);
    }
}
