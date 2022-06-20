<?php

namespace Servers\views\components;

class UserPanel {
    public static final function nav() {
        printf('<div class="text-white">Stan konta: %s</div><div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Ustawienia:</h6>
                    <a class="collapse-item" href="index.php/recharge">Doładuj konto</a>
                    <a class="collapse-item" href="index.php/recharge-friend">Doładuj konto znajomemu</a>
                    <a class="collapse-item" href="index.php/server-list">Zakupione serwery</a>
                    <a class="collapse-item" href="index.php/payments">Płatności</a>
                    <a class="collapse-item" href="index.php/settings">Użytkownik</a>
                </div>
            </div>',Repositories::$userRepository->findOneById($_SESSION['id'])->getWallet());
    }
}
