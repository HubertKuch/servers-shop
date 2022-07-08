<?php

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
                <div class="text-center mt-5">
                    <h1 class="text-gray-900 h4 font-weight-bold">Dziękujemy za założenie konta w naszym poratlu!</h1>
                    <p class="">Żebyś w pełni mógł z niego korzystać musisz aktywować swoje <br>
                        konto kodem wysłanym na Twojego maila <span style="color: #4e73df"><?= $_SESSION['email'] ?? '' ?> </span>.</p>
                </div>

                <form action="index.php/api/activate-account" class="user" method="POST">
                    <input type="hidden" name="_method" value="PATCH">
                    <div class="row justify-content-center flex-column">
                        <?php
                        foreach ($errors as $error) {
                            echo "<div style='color: red; font-size: 0.8rem' class='font-weight-bold text-center mb-3'>$error !</div>";
                        }
                        ?>
                        <div class="form-group d-flex justify-content-center">

                            <label class="font-weight-bold">
                                Kod aktywacyjny:<br>
                                <input type="text" name="activation-code" class="input--pin-code" maxlength="6" style="color: #2d2e33">
                            </label>
                        </div>
                        <div class="form-group d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary btn-user btn-block mt-auto mt-5 col-3">Aktywuj konto</button><br><br>
                        </div>
                        <div class="form-group d-flex justify-content-center">
                            <a href="index.php/api/generate-activation-code/<?= $_SESSION['email'] ?? '' ?>" style="font-size: 18px; color: #4e73df">Wyślij nowy kod aktywacyjny</a>
                        </div>
                    </div>
                </form>
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

