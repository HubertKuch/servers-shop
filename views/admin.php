<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin menu</title>
    <base href="http://localhost/servers/">
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
                            <td class="table__col"><?= $user->id ?></td>
                            <td class="table__col"><?= $user->username ?></td>
                            <td class="table__col"><?= $user->email ?></td>
                            <td class="table__col"><?= $user->wallet ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
        </section>

        <section class="admin__panel--section payments section--invisible">
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

        <section class="admin__panel--section sold-servers section--invisible">
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
                <?php foreach($soldServers as $server): ?>
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
                        <td class="table__col"><?= $log->id ?></td>
                        <td class="table__col"><?= $log->type ?></td>
                        <td class="table__col"><?= $log->user_id ?: "nie dotyczy" ?></td>
                        <td class="table__col"><?= $log->payment_id ?: "nie dotyczy" ?></td>
                        <td class="table__col"><?= $log->product_id ?: "nie dotyczy" ?></td>
                        <td class="table__col"><?= $log->date ?></td>
                        <td class="table__col"><?= $log->message ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

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