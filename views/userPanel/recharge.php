
<?php

use Servers\Models\ServerStatus;
use Servers\Utils\Environment;
use Servers\Repositories;
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
    <link rel="stylesheet" href="style/app.css">
    <script src="https://kit.fontawesome.com/31d2710bc5.js" crossorigin="anonymous"></script>
</head>
<body class="panel">

    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php/">

                <div class="sidebar-brand-text mx-3">MC Admin <sup>panel</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php/">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Panel</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Ustawienia
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="index.php/servers">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Stwórz serwer</span>
                </a>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Ustawienia konta</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Ustawienia:</h6>
                        <a class="collapse-item" href="index.php/recharge">Doładuj konto</a>
                        <a class="collapse-item" href="index.php/server-list">Zakupione serwery</a>
                        <a class="collapse-item" href="index.php/payments">Płatności</a>
                        <a class="collapse-item" href="index.php/settings">Użytkownik</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider mb-0">
            <li class="nav-item">
                <a class="nav-link" href="index.php/api/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Wyloguj się</span>
                </a>
            </li>

            <hr class="sidebar-divider">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <div class="row">
                    <div class="col px-5">
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

                            <div class="payment__methods row">
                                <div class="col">
                                    <div class="row ">
                                        <div  class="card shadow mr-5" style="cursor: pointer">
                                            <div data-payment-due-name="PSC_DUE" data-payment-method="8" class="card-body methods__method ">
                                                <img src="views/assets/psc.png" alt="psc" style="aspect-ratio: 16/9 " height="120px">
                                            </div>
                                        </div>
                                        <div class="card shadow mr-5" style="cursor: pointer">
                                            <div data-payment-due-name="PAYPAL_DUE" data-payment-method="4" class="card-body methods__method">
                                                <img src="views/assets/paypal.webp" alt="paypal"  style="aspect-ratio: 16/9 " height="120px" style="cursor: pointer">
                                            </div>
                                        </div>
                                        <div class="card shadow mr-5" style="cursor: pointer">
                                            <div data-payment-due-name="G2A_DUE" data-payment-method="32"  class="card-body methods__method">
                                                <img src="views/assets/g2apay.jpeg" alt="g2apay"  style="aspect-ratio: 16/9 " height="120px">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="card shadow mr-5 mt-5" style="cursor: pointer">
                                            <div data-payment-due-name="SMS_PLUS_DUE" data-payment-method="64"  class="card-body methods__method ">
                                                <img src="views/assets/justpay.jpg" alt="justpay" style="aspect-ratio: 16/9 " height="120px">
                                            </div>
                                        </div>
                                        <div class="card shadow mr-5 mt-5" style="cursor: pointer">
                                            <div data-payment-due-name="CASH_BILL_DUE" data-payment-method="128" class="card-body methods__method ">
                                                <img src="views/assets/cashbill.jpg" alt="cashbill" style="aspect-ratio: 16/9 " height="120px">
                                            </div>
                                        </div>
                                        <div class=" card shadow mr-5 mt-5" style="cursor: pointer">
                                            <div data-payment-due-name="SMS_DUE" data-payment-method="256" class="card-body methods__method">
                                                <img src="views/assets/cashbillsms.webp" alt="sms" style="aspect-ratio: 16/9" height="120px">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <br>
                            <section class="add__amount">
                                <span>Kwota</span>
                                <span class="after-commission">(Po odjęciu prowizji <spaFn class="after-commission__amount">0</spaFn>)</span><br>
                                <input type="text" name="amount" class="panel__input">
                            </section>
                            <br>

                            <button type="submit" class="panel__button btn btn-success">Dodaj środki</button>
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
                                        paymentMethods.forEach(el => el.classList.remove('border-bottom-success'));

                                        let methodEl = e.target;
                                        let paymentMethodId = e.target.getAttribute('data-payment-method');

                                        if (!paymentMethodId) {
                                            paymentMethodId = e.target.parentElement.getAttribute('data-payment-method');
                                            methodEl = e.target.parentElement;
                                        }

                                        methodEl.classList.add("border-bottom-success")
                                        // methodEl.style.border = "2px solid white";
                                        paymentId.value = paymentMethodId;

                                        amount.addEventListener('input', () => showRealAmount(methodEl));
                                        e.target.addEventListener('click', () => showRealAmount(methodEl));
                                    });
                                }
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="js/jquery/jquery.min.js"></script>
    <script src="js/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="js/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
    <script>
        // Toggle the side navigation
        $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
            $("body").toggleClass("sidebar-toggled");
            $(".sidebar").toggleClass("toggled");
            if ($(".sidebar").hasClass("toggled")) {
                $('.sidebar .collapse').collapse('hide');
            };
        });
    </script>
</body>
</html>
