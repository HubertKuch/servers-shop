<?php

    use Servers\views\components\UserPanel;

?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panel</title>
    <base href="http://localhost/servers/">
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
    <nav class="admin__navigation">
        <ul class="navigation__list">
            <li data-section-class="payments" class="admin__panel--section-option">Platności</li>
            <li data-section-class="user-servers" class="admin__panel--section-option">Wystawione serwery</li>
            <li data-section-class="bought-servers" class="admin__panel--section-option">Kupione serwery</li>
            <li data-section-class="" class="admin__panel--section-option">Zasilacz</li>
        </ul>

        <div>
            <?php if($isAdmin): ?>
                <a href="index.php/admin">Admin</a><br>
            <?php endif; ?>
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

        <section class="user-servers admin__panel--section section--visible">

            <?php
                if (empty($userServers)) {
                    echo "Nie wystawiłeś jeszcze żadnego servera";
                }
            ?>

            <table class="table">
                <tr class="table__row">
                    <th>ID</th>
                    <th>TYTUŁ</th>
                    <th>STATUS</th>
                    <th>DATA UTWORZENIA</th>
                    <th>DATA WYGAŚNIĘCIA</th>
                    <th>PACZKA</th>
                    <th>ID PŁATNOŚCI</th>
                </tr>
                <?php foreach($userServers as $server): ?>
                    <tr class="table__row">
                        <td class="table__col"><?= $server->id ?></td>
                        <td class="table__col"><?= $server->title ?></td>
                        <td class="table__col"><?= $server->status ?></td>
                        <td class="table__col"><?= $server->createDate ?></td>
                        <td class="table__col"><?= $server->expireDate ?></td>
                        <td class="table__col"><?= $server->package ?></td>
                        <td class="table__col"><?= $server->payment_id ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        </section>

        <section class="bought-servers admin__panel--section section--invisible">
            <?php
                if (empty($boughtServers)) {
                    echo "<p>Nie kupiłeś jeszcze żadnego servera</p>";
                }

                foreach ($boughtServers as $server) {
                    UserPanel::serverCard($server->id, $server->title, $server->createDate, $server->expireDate, $server->package, $server->payment->sum);
                }
            ?>
        </section>

    </main>

    <script>
        'use strict';

        const options = document.querySelectorAll('.admin__panel--section-option');
        const sections = document.querySelectorAll('.admin__panel--section');

        options.forEach(option => option.addEventListener('click', ({ target }) => {
            const sectionClass = target.getAttribute('data-section-class');

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