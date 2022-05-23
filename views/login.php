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
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
    <form action="index.php/api/login" method="post" class="login__form">
        <div class="form__error-container">
            <?php
                foreach ($errors as $error) echo "<span class='error-container__error'>$error</span>";
            ?>
        </div>
        <p>Logowanie</p>
        <label>
            <input type="text" name="username" class="form__input" placeholder="username">
        </label>
        <label>
            <input type="password" name="password" class="form__input form__input--password" placeholder="password">
        </label>
        <button type="submit" class="form__button">Zaloguj się</button>
        <div class="form__bottom-section">
            <a href="views/register.php">Nie masz konta? Zaloz je</a>
        </div>
    </form>
</body>
</html>
