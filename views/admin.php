<?php

use Servers\Models\PaymentMethods;
use Servers\Repositories;
use Servers\Utils\Environment;

?>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin menu</title>
    <base href="<?= Environment::getBaseURL() ?>">
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
    <nav class="admin__navigation">
        <ul class="navigation__list">
            <li data-section-class="users" class="admin__panel--section-option">Uzytkownicy</li>
            <li data-section-class="payments" class="admin__panel--section-option">Platności</li>
            <li data-section-class="sold-servers" class="admin__panel--section-option">Serwery</li>
            <li data-section-class="" class="admin__panel--section-option">Zasilacz</li>
            <li data-section-class="logs" class="admin__panel--section-option">Logi</li>
            <li><a href="index.php/">Główna</a></li>
        </ul>
    </nav>
    <main class="admin__main">
        <section class="admin__panel--section users section--visible">
                <table class="table">
                    <tr class="table__row">
                        <th>ID</th>
                        <th>NAZWA</th>
                        <th>EMAIL</th>
                        <th>WALLET</th>
                    </tr>
                    <?php foreach($users as $user): ?>
                        <tr class="table__row">
                            <td class="table__col"><?= $user->getId() ?></td>
                            <td class="table__col"><?= $user->getUsername() ?></td>
                            <td class="table__col"><?= $user->getEmail() ?></td>
                            <td class="table__col"><?= $user->getWallet() ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
        </section>

        <section class="admin__panel--section payments section--invisible">
            <table class="table">
                <tr class="table__row">
                    <th>ID</th>
                    <th>DATA UTWORZENIA</th>
                    <th>DATA PŁATNOŚCI</th>
                    <th>IP</th>
                    <th>STATUS</th>
                    <th>KWOTA</th>
                    <th>METODA</th>
                    <th>ID UŻYTKOWNIKA</th>
                </tr>
                <?php foreach($payments as $payment): ?>
                    <tr class="table__row">
                        <td class="table__col"><?= $payment->getId() ?></td>
                        <td class="table__col"><?= date("d.m.Y H:i:s", $payment->getCreateDate()) ?></td>
                        <td class="table__col"><?= date("d.m.Y H:i:s", $payment->getPaymentDate()) ?></td>
                        <td class="table__col"><?= $payment->getIpAddress() ?></td>
                        <td class="table__col"><?= match ($payment->getStatus()) {
                                "incoming" => "Przychodząca",
                                "resolved" => "Zaakceptowana",
                                "rejected" => "Odrzucona",
                                default => "Nieznany"
                            }
                            ?></td>
                        <td class="table__col"><?= $payment->getSum() ?></td>
                        <td class="table__col"><?= str_replace('_', ' ', PaymentMethods::tryFrom($payment->getMethod())->name) ?></td>
                        <td class="table__col"><?= $payment->getUserId() ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section class="admin__panel--section sold-servers section--invisible">
            <table class="table">
                <tr class="table__row">
                    <th>ID</th>
                    <th>TYTUŁ</th>
                    <th>STATUS</th>
                    <th>DATA UTWORZENIA</th>
                    <th>DATA WYGAŚNIĘCIA</th>
                    <th>PACZKA</th>
                </tr>
                <?php foreach($soldServers as $server): ?>
                    <tr class="table__row">
                        <td class="table__col"><?= $server->getId() ?></td>
                        <td class="table__col"><?= $server->getTitle() ?></td>
                        <td class="table__col"><?= $server->getStatus() ?></td>
                        <td class="table__col"><?= date("d.m.Y H:i:s", $server->getCreateDate()) ?></td>
                        <td class="table__col"><?= date("d.m.Y H:i:s", $server->getExpireDate()) ?></td>
                        <td class="table__col"><?= Repositories::$packagesRepository->findOneById($server->getPackageId())->getName() ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section class="admin__panel--section logs section--invisible">

            <table class="table">
                <tr class="table__row">
                    <th>ID</th>
                    <th>TYP</th>
                    <th>ID UŻYTKOWNIKA</th>
                    <th>ID PŁATNOSCI</th>
                    <th>ID SERWERA</th>
                    <th>DATA</th>
                    <th>WIADOMOŚĆ</th>
                </tr>
                <?php foreach($logs as $log): ?>
                    <tr class="table__row">
                        <td class="table__col"><?= $log->getId() ?></td>
                        <td class="table__col"><?= $log->getType() ?></td>
                        <td class="table__col"><?= $log->getUserId() ?: "nie dotyczy" ?></td>
                        <td class="table__col"><?= $log->getPaymentId() ?: "nie dotyczy" ?></td>
                        <td class="table__col"><?= $log->getProductId() ?: "nie dotyczy" ?></td>
                        <td class="table__col"><?= $log->getTimestamp() ?></td>
                        <td class="table__col"><?= $log->getMessage() ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        </section>
    </main>

    <script>
        'use strict';

        const options = document.querySelectorAll('.admin__panel--section-option');
        const sections = document.querySelectorAll('.admin__panel--section');

        console.log(2)

        options.forEach(option => option.addEventListener('click', ({ target }) => {
            const sectionClass = target.getAttribute('data-section-class');
            console.log()

            sections.forEach(section => {
                if (!section.classList.contains(sectionClass)) {
                    section.classList.remove('section--visible');
                    section.classList.add('section--invisible');
                } else {
                    section.classList.add('section--visible');
                    section.classList.remove('section--invisible');
                }
            })
        }));

    </script>
</body>
</html>
