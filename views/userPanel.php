<?php

use Servers\Models\enumerations\ServerStatus;
use Servers\Repositories;
use Servers\Utils\Environment;

?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panel</title>
    <base href="<?= Environment::getBaseURL() ?>">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="style/app.css">
    <script src="https://kit.fontawesome.com/31d2710bc5.js" crossorigin="anonymous"></script>
</head>
<body class="panel">
<nav class="admin__navigation">
    <ul class="navigation__list">
        <li>
            <a href="index.php/">
                <i class="fa-solid fa-house"></i>
            </a>
        </li>
        <li>
            <i class="fa-solid fa-credit-card admin__panel--section-option" data-section-class="payments"></i>
        </li>
        <li>
            <i class="fa-solid fa-wallet admin__panel--section-option" data-section-class="wallet"></i>
        </li>
        <li>
            <i class="fa-solid fa-server admin__panel--section-option" data-section-class="bought-servers"></i>
        </li>
        <li>
            <i class="fa-solid fa-gear admin__panel--section-option" data-section-class="settings"></i>
        </li>
        <?php if($isAdmin): ?>
            <li>
                <a href="index.php/admin">
                    <i class="fa-solid fa-user-astronaut"></i>
                </a>
            </li>
        <?php endif; ?>
        <li>
            <a href="index.php/api/logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </a>
        </li>
    </ul>
</nav>

<main class="admin__main">

    <section class="wallet admin__panel--section section--invisible">
        <p style="font-size: 24px">Doładuj swoje konto</p>
        <p class="waller__errros" style="color: red;">
            <?php
            foreach ($_GET as $error => $message) {
                echo $message;
            }
            ?>
        </p>

        <p>Wybierz metodę płatności</p>
        <form action="index.php/api/add-amount" method="post">
            <input type="hidden" class="payment_id" name="payment_id" value="0">
            <input type="hidden" name="_method" value="PATCH">

            <section class="payment__methods">
                <div data-payment-due-name="PSC_DUE" data-payment-method="8" class="methods__method"><img src="views/assets/psc.png" alt="psc"></div>
                <div data-payment-due-name="PAYPAL_DUE" data-payment-method="4" class="methods__method"><img src="views/assets/paypal.webp" alt="paypal"></div>
                <div data-payment-due-name="G2A_DUE" data-payment-method="32" class="methods__method"><img src="views/assets/g2apay.jpeg" alt="g2apay"></div>
                <div data-payment-due-name="SMS_PLUS_DUE" data-payment-method="64" class="methods__method"><img src="views/assets/justpay.jpg" alt="justpay"></div>
                <div data-payment-due-name="CASH_BILL_DUE" data-payment-method="128" class="methods__method"><img src="views/assets/cashbill.jpg" alt="cashbill"></div>
                <div data-payment-due-name="SMS_DUE" data-payment-method="256" class="methods__method"><img src="views/assets/cashbillsms.webp" alt="sms"></div>
            </section>
            <br>
            <section class="add__amount">
                <span>Kwota</span>
                <span class="after-commission">(Po odjęciu prowizji <spaFn class="after-commission__amount">0</spaFn>)</span><br>
                <input type="text" name="amount" class="panel__input">
            </section>
            <br>
            <button type="submit" class="panel__button">Dodaj środki</button>

            <script>
                const paymentMethods = document.querySelectorAll('.methods__method');
                const paymentId = document.querySelector('.payment_id');
                const realAmount = document.querySelector('.after-commission__amount');
                const amount = document.querySelector('.add__amount [name=amount]');

                const dues =<?php echo "[";
                foreach ($_ENV as $key => $value) {
                    if (str_contains($key, "DUE")) {
                        printf('{name: "%s", due: %s},', $key, $value);
                    }
                } echo "]";?>

                const showRealAmount = (methodEl) => {
                    const paymentMethodDueName = methodEl.getAttribute('data-payment-due-name');
                    const failAmount = parseFloat(amount.value) ?? 0;
                    const { due } = dues.find(due => due.name === paymentMethodDueName);
                    realAmount.textContent = `${(failAmount * due)/ 100}`;
                }

                for (const method of paymentMethods) {
                    method.addEventListener('click', (e) => {
                        paymentMethods.forEach(el => el.removeAttribute('style'));

                        let methodEl = e.target;
                        let paymentMethodId = e.target.getAttribute('data-payment-method');

                        if (!paymentMethodId) {
                            paymentMethodId = e.target.parentElement.getAttribute('data-payment-method');
                            methodEl = e.target.parentElement;
                        }

                        methodEl.style.background = '#8659ea';
                        methodEl.style.border = "2px solid white";
                        paymentId.value = paymentMethodId;

                        amount.addEventListener('input', () => showRealAmount(methodEl));
                        e.target.addEventListener('click', () => showRealAmount(methodEl));
                    });
                }
            </script>
        </form>
    </section>

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
            </tr>
            <?php foreach($payments as $payment): ?>
                <tr class="table__row">
                    <td class="table__col"><?= $payment->getId() ?></td>
                    <td class="table__col"><?= $payment->getPaymentDate() ?></td>
                    <td class="table__col"><?= $payment->getCreateDate() ?></td>
                    <td class="table__col"><?= $payment->getIpAddress() ?></td>
                    <td class="table__col"><?= match ($payment->getStatus()) {
                            "incoming" => "Przychodząca",
                            "resolved" => "Zaakceptowana",
                            "rejected" => "Odrzucona",
                            default => "Nieznany"
                        }
                        ?></td>
                    <td class="table__col"><?= $payment->getSum() ?></td>
                    <td class="table__col"><?= $payment->getMethod() ?></td>
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
                    <td class="table__col"><?= $server->getId() ?></td>
                    <td class="table__col"><?= $server->getTitle() ?></td>
                    <td class="table__col"><?= match ($server->getStatus()) {
                            ServerStatus::SOLD->value => '<span class="server-status server-status--active">Aktywny</span>',
                            ServerStatus::IN_MAGAZINE->value,
                            ServerStatus::EXPIRED->value => '<span class="server-status server-status--expired">Wygasł</span>'
                        } ?>
                    </td>
                    <td class="table__col"><?= date('m/d/Y', $server->getCreateDate()) ?></td>
                    <td class="table__col"><?= date('m/d/Y', $server->getExpireDate()) ?></td>
                    <td class="table__col"><?php $package = Repositories::$packagesRepository->findOneById($server->getPackageId()); echo "{$package->getName()} ({$package->getRamSize()}MB / {$package->getDiskSize()}MB)" ?></td>
                    <td class="table__col"><?= $server->getPaymentId() ?? "BRAK" ?></td>
                    <td class="table__col">
                        <form action="index.php/api/unsuspend-server/<?= $server->getId() ?>" method="post">
                            <input type="hidden" name="_method" value="PATCH">
                            <button type="submit" class="button--renew">Odnów</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>

    <section class="settings admin__panel--section section--invisible">
        <div class="row">
            <div class="col mt-5">
                <h1 class="text-gray-900">Ustawienia konta</h1>
            </div>
        </div>

        <p class="settings__errors" style="color: red;">
            <?php
            foreach ($_GET as $error => $message) {
                echo $message;
            }
            ?>
        </p>

        <hr style="color: white">
        <h4 class="text-gray-900 mb-2">Hasło</h4>
        <form action="index.php/api/change-password" method="POST" class="user">
            <input type="hidden" name="_method" value="PATCH">
            <div class="row">
                <div class="col-xl-4 col-lg-6 mt-3">
                    <div class="form-group">
                        <p class=" mb-0">Stare hasło</p>
                        <input type="password" class="form-control form-control-user" name="old-password" required>
                    </div>
                    <div class="form-group">
                        <span>Nowe hasło</span>
                        <input type="password" class="form-control form-control-user" name="new-password" required>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <button type="submit" class="btn btn-primary w-50 btn-user btn-block mt-auto mt-5">
                                Zapisz
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>


        <hr style="color: white">

        <h4 class="text-gray-900 mb-2 pt-3">Nazwa użytkownika</h4>
        <form action="index.php/api/change-username" method="POST" class="user">
            <input type="hidden" name="_method" value="PATCH">
            <div class="row">
                <div class="col-xl-4 col-lg-6 mt-3">
                    <div class="form-group">
                        <p class="mb-0">Nowa nazwa</p>
                        <input type="text" class="form-control form-control-user" name="new-username" required>
                    </div>
                    <div class="row  mt-3">
                        <div class="col">
                            <button type="submit" class="btn btn-primary w-50 btn-user btn-block mt-auto mt-5">
                                Zapisz
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>


        <hr style="color: white">

        <h4 class="text-gray-900 mb-2 pt-3">Email</h4>
        <form action="index.php/api/change-email" method="POST" class="user">
            <input type="hidden" name="_method" value="PATCH">
            <div class="row">
                <div class="col-xl-4 col-lg-6 mt-3">
                    <div class="form-group">
                        <p class="mb-0">Nowy email</p>
                        <input type="text" class="form-control form-control-user" name="new-email" required>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <button type="submit" class="btn btn-primary w-50 btn-user btn-block mt-auto mt-5">
                                Zapisz
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>

</main>

<script>
    'use strict';
    const options = document.querySelectorAll('.admin__panel--section-option');
    const sections = document.querySelectorAll('.admin__panel--section');
    const beforeActiveSectionClass = localStorage.getItem("user-panel-actual-visible") ?? null;
    console.log(2)

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
