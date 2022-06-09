<?php

use Servers\Utils\Environment;

?>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rejestracja</title>
    <base href="<?= Environment::getBaseURL() ?>">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="style/app.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">

                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Zaloguj się!</h1>
                                    </div>
                                    <form class="user" action="index.php/api/register" method="post">
                                        <div class="form-group mb-3">
                                            <input type="text" class="form-control form-control-user" name="username" aria-describedby="emailHelp" placeholder="Nazwa użytkownika" required>
                                        </div>
                                        <div class="form-group mb-3">
                                                <?php
                                                foreach ($errors as $error) {
                                                    echo "<div style='color: red; font-size: 0.8rem' class='font-weight-bold'>$error !</div>";
                                                }
                                                ?>
                                            <input type="email" class="form-control form-control-user" name="email" aria-describedby="emailHelp" placeholder="Adres email" required>
                                        </div>
                                        <div class="form-group mb-5">
                                            <input type="password" class="form-control form-control-user" name="password" placeholder="Podaj hasło" required>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block mt-auto mt-5">
                                            Zarejestruj się
                                        </button>
                                        <hr>
                                    </form>
                                    <div class="text-center">
                                        <a class="small" href="index.php/login">Masz już konto? Zaloguj się!</a>
                                    </div>
                                    <hr>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

<!--    <form action="index.php/api/register" method="post" class="login__form">-->
<!--        <div class="form__error-container">-->
<!--            --><?php
//                foreach ($errors as $error) echo "<span class='error-container__error'>$error</span>";
//            ?>
<!--        </div>-->
<!--        <p>Rejestracja</p>-->
<!--        <label>-->
<!--            <input type="text" name="username" class="form__input" placeholder="username">-->
<!--        </label>-->
<!--        <label>-->
<!--            <input type="text" name="email" class="form__input" placeholder="email">-->
<!--        </label>-->
<!--        <label>-->
<!--            <input type="password" name="password" class="form__input form__input--password" placeholder="password">-->
<!--        </label>-->
<!--        <button type="submit" class="form__button">Zarejestruj się</button>-->
<!--        <div class="form__bottom-section">-->
<!--            <a href="index.php/login">Logowanie</a>-->
<!--        </div>-->
<!--    </form>-->
</body>
</html>
