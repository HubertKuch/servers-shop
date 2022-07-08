<?php

use Servers\Controllers\AuthController;
use Servers\Models\User;
use Servers\Utils\Environment;
?>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Aktywacja konta</title>
    <base href="<?= Environment::getBaseURL() ?>">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="style/app.css" rel="stylesheet">
    <link href="style/main.css" rel="stylesheet">
    <style>
        body {
            overflow-y: hidden ;
        }
    </style>
</head>
<body>
<div id="content">
    <div class="container-fluid">
        <?php if (!isset($_GET['token'])): ?>
            <div>
                <p class="h4 text-center p-4">Na Twoj email zostanie wyslany link po ktorym bedziesz mogl zmienic swoje haslo.</p>

                <?php if(isset($_GET['message'])): ?>
                <p class="h4 text-center p-4 text-warning"><?= $_GET['message'] ?></p>
                <?php endif; ?>
            </div>
            <form action="index.php/api/remember-password" class="user" method="POST">
                <input type="hidden" name="_method" value="PATCH">
                <div class="row justify-content-center flex-column">
                    <div class="form-group d-flex justify-content-center">
                        <label class="font-weight-bold">
                            Email:<br>
                            <input type="email" name="email" class="form-control" style="color: #2d2e33">
                        </label>
                    </div>
                    <div class="form-group d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary btn-user btn-block mt-auto mt-5 col-3">Wyslij email</button><br><br>
                    </div>
                </div>
            </form>
        <?php endif; ?>
        <?php if (isset($_GET['token'])): ?>
            <?php if(!User::isValidRememberPasswordToken($_GET['token'])): AuthController::redirect('remember-password'); endif; ?>

            <div>
                <p class="h4 text-center p-4">Zmien haslo.</p>

                <?php if(isset($_GET['message'])): ?>
                    <p class="h4 text-center p-4 text-warning"><?= $_GET['message'] ?></p>
                <?php endif; ?>
            </div>
            <form action="index.php/api/change-password" class="user" method="POST">
                <input type="hidden" name="_method" value="PATCH">
                <div class="row justify-content-center flex-column">
                    <div class="form-group d-flex justify-content-center">
                        <label class="font-weight-bold">
                            Nowe haslo:<br>
                            <input type="password" name="email" class="form-control" style="color: #2d2e33">
                        </label>
                    </div>
                    <div class="form-group d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary btn-user btn-block mt-auto mt-5 col-3">Wyslij email</button><br><br>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
<!-- Bootstrap core JavaScript-->
<script src="js/jquery/jquery.min.js"></script>
<script src="js/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="js/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/app/app.min.js"></script>
</html>

