<?php

namespace Servers\views\components;

use Servers\Repositories;
use Servers\Utils\Environment;

class UserPanel {
    public static final function nav() {
        $newNotificationsCounter = count(Repositories::$notificationsRepository->findMany([
            "user_id" => $_SESSION['id'],
            "isRead" => 0
        ]));

        printf('
            <li class="nav-item">
                <a class="nav-link" href="index.php/recharge">
                    <i class="fas fa-solid fa-money-check"></i>
                    <span>Doładuj konto</span>
                </a>
            </li><li class="nav-item">
                <a class="nav-link" href="index.php/recharge-friend">
                    <i class="fas fa-fw fa-hand-holding-usd"></i>
                    <span>Doładuj konto znajomemu</span>
                </a>
            </li><li class="nav-item">
                <a class="nav-link" href="index.php/server-list">
                    <i class="fas fa-fw fa-server"></i>
                    <span>Zakupione serwery</span>
                </a>
            </li><li class="nav-item">
                <a class="nav-link" href="index.php/payments">
                    <i class="fas fa-fw fa-credit-card"></i>
                    <span>Płatności</span>
                </a>
            </li><li class="nav-item">
                <a class="nav-link" href="index.php/notifications">
                    <i class="fas fa-fw fa-bell"></i>
                    <span>Powiadomienia (%s)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php/settings">
                    <i class="fas fa-fw fa-user-astronaut"></i>
                    <span>Użytkownik</span>
                </a>
            </li>
            <div class="text-white text-warning text-center py-3">Stan konta: %sPLN</div>
            ', $newNotificationsCounter, Environment::domainNumberFormat(Repositories::$userRepository->findOneById($_SESSION['id'])->getWallet()));
    }
}
