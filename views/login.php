<?php

use Servers\Utils\Environment;

?>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Logowanie</title>
    <base href="<?= Environment::getBaseURL() ?>">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="style/app.css" rel="stylesheet">

</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row" style="min-height: 500px">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Zaloguj się!</h1>
                                    </div>
                                    <form class="user" action="index.php/api/login" method="post" >
                                        <div class="form-group mb-3">
                                            <?php
                                            foreach ($errors as $error) {
                                                echo "<div style='color: red; font-size: 0.8rem' class='font-weight-bold'>$error !</div>";
                                            }
                                            ?>
                                            <input type="text" class="form-control form-control-user" name="username" aria-describedby="emailHelp" placeholder="Nazwa użytkownika" required>
                                        </div>
                                        <div class="form-group mb-5">
                                            <input type="password" class="form-control form-control-user" name="password" id="exampleInputPassword" placeholder="Podaj hasło" required>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block mt-auto mt-5">
                                            Zaloguj się
                                        </button>

                                        <hr>
                                    </form>
    <!--                                <div class="text-center">-->
    <!--                                    <a class="small" href="forgot-password.html">Forgot Password?</a>-->
    <!--                                </div>-->
                                    <div class="text-center">
                                        <a class="small" href="index.php/register">Nie masz konta? Załóż je!</a>
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
<script src="js/jquery/jquery.min.js"></script>
<script src="js/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="js/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/app/app.min.js"></script>
</body>
</html>
