<?php

use Servers\Models\ServerStatus;
use Servers\Utils\Environment;
use Servers\views\components\UserPanel;

?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panel</title>
    <base href="<?= Environment::getBaseURL() ?>">
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
    <nav class="admin__navigation">
        <ul class="navigation__list">
            <li data-section-class="payments" class="admin__panel--section-option">Platności</li>
            <li data-section-class="bought-servers" class="admin__panel--section-option">Kupione serwery</li>
            <li data-section-class="" class="admin__panel--section-option">Zasilacz</li>
            <li data-section-class="settings" class="admin__panel--section-option">Ustawienia konta</li>
        </ul>

        <div class="navigation__bottom-settings">
            <?php if($isAdmin): ?>
                <a href="index.php/admin">Admin</a>
            <?php endif; ?>
            <a href="index.php/">Głowna</a>
            <a href="index.php/api/logout">Logout</a>
        </div>
    </nav>

    <main class="admin__main">

        <section class="payments admin__panel--section section--visible">
            <?php
                if (empty($payments)) {
                    echo "Nie dokonnałeś jeszcze żadnego zakupu.";
                }
            ?>

            <table class="table">
                <tr class="table__row">
                    <th>ID</th>
                    <th>DATA PŁATNOŚCI</th>
                    <th>DATA UTWORZENIA</th>
                    <th>IP</th>
                    <th>STATUS</th>
                    <th>KWOTA</th>
                    <th>METODA</th>
                    <th>ID UŻYTKOWNIKA</th>
                </tr>
                <?php foreach($payments as $payment): ?>
                    <tr class="table__row">
                        <td class="table__col"><?= $payment->id ?></td>
                        <td class="table__col"><?= $payment->paymentDate ?></td>
                        <td class="table__col"><?= $payment->createDate ?></td>
                        <td class="table__col"><?= $payment->ipAdress ?></td>
                        <td class="table__col"><?= $payment->sum ?></td>
                        <td class="table__col"><?= $payment->method ?></td>
                        <td class="table__col"><?= $payment->user_id ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section class="user-servers admin__panel--section section--invisible">

            <?php
                if (empty($userServers)) {
                    echo "Nie wystawiłeś jeszcze żadnego servera";
                }
            ?>

        </section>

        <section class="bought-servers admin__panel--section section--invisible">
            <p>
                Dostęp do zarządzania zakupionymi serwerami jest dostępny w
                <a target="_blank" href="http://178.32.202.241:85/" style="color: lightcoral">panelu</a>.
                Zaloguj się za pomocą swojego loginu/maila i hasła.
            </p>
            <table class="table">
                <tr class="table__row">
                    <th>ID</th>
                    <th>TYTUŁ</th>
                    <th>STATUS</th>
                    <th>DATA UTWORZENIA</th>
                    <th>DATA WYGAŚNIĘCIA</th>
                    <th>PACZKA</th>
                    <th>ID PŁATNOŚCI</th>
                    <th>Odnów</th>
                </tr>
                <?php foreach($userServers as $server): ?>
                    <tr class="table__row">
                        <td class="table__col"><?= $server->id ?></td>
                        <td class="table__col"><?= $server->title ?></td>
                        <td class="table__col"><?= match ($server->status) {
                                ServerStatus::SOLD->value => "Aktywny",
                                ServerStatus::IN_MAGAZINE->value,
                                ServerStatus::EXPIRED->value => "Wygasł"
                        } ?></td>
                        <td class="table__col"><?= date('m/d/Y', $server->createDate) ?></td>
                        <td class="table__col"><?= date('m/d/Y', $server->expireDate) ?></td>
                        <td class="table__col"><?= $server->package_id ?></td>
                        <td class="table__col"><?= $server->payment_id ?></td>
                        <td class="table__col">
                            <form action="index.php/api/unsuspend-server/<?= $server->id ?>" method="post">
                                <input type="hidden" name="_method" value="PATCH">
                                <button type="submit">Odnów</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section class="settings admin__panel--section section--invisible">


            <p class="settings-form-label">Ustawienia konta</p>

            <p class="settings__errors" style="color: red;">
                <?php
                foreach ($_GET as $error => $message) {
                    echo $message;
                }
                ?>
            </p>

            <hr style="color: white">
            <p class="settings-form-label">Hasło</p>
            <form action="index.php/api/change-password" method="POST">
                <input type="hidden" name="_method" value="PATCH">
                <label>
                    <span>Stare haslo</span>
                    <input class="panel__input" type="password" name="old-password">
                </label><br>

                <label>
                    <span>Nowe haslo</span>
                    <input class="panel__input" type="password" name="new-password">
                </label><br>

                <button type="submit" class="panel__button">Zapisz</button>
            </form>

            <hr style="color: white">

            <p class="settings-form-label">Nazwa uzytkownika</p>
            <form action="index.php/api/change-username" method="POST">
                <input type="hidden" name="_method" value="PATCH">
                <label>
                    <span>Nowa nazwa</span>
                    <input class="panel__input" type="text" name="new-username">
                </label><br>

                <button type="submit" class="panel__button">Zapisz</button>
            </form>

            <hr style="color: white">

            <p class="settings-form-label">Email</p>
            <form action="index.php/api/change-email" method="POST">
                <input type="hidden" name="_method" value="PATCH">
                <label>
                    <span>Nowy email</span>
                    <input class="panel__input" type="text" name="new-email">
                </label><br>

                <button type="submit" class="panel__button">Zapisz</button>
            </form>
        </section>
    </main>

    <script>
        'use strict';

        const options = document.querySelectorAll('.admin__panel--section-option');
        const sections = document.querySelectorAll('.admin__panel--section');
        const beforeActiveSectionClass = localStorage.getItem("user-panel-actual-visible") ?? null;

        sections.forEach(section => {
            if (!section.classList.contains(beforeActiveSectionClass)) {
                section.classList.remove('section--visible');
                section.classList.add('section--invisible');
            } else {
                section.classList.add('section--visible');
                section.classList.remove('section--invisible');
            }
        })

        options.forEach(option => option.addEventListener('click', ({ target }) => {
            const sectionClass = target.getAttribute('data-section-class');

            sections.forEach(section => {
                if (!section.classList.contains(sectionClass)) {
                    section.classList.remove('section--visible');
                    section.classList.add('section--invisible');
                } else {
                    section.classList.add('section--visible');
                    section.classList.remove('section--invisible');
                    localStorage.setItem("user-panel-actual-visible", sectionClass);
                }
            })
        }));
    </script>
</body>
</html>
