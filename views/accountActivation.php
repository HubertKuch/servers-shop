<?php

use Servers\Utils\Environment;

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Aktywacja konta</title>
    <base href="<?= Environment::getBaseURL() ?>">
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
    <main class="account-activation-section">
        <div class="account-activation-section__container">
            <div class="container__message">
                Dziękujemy za założenie konta w naszym poratlu, żebyś w pełni mógł z niego korzystać musisz aktywować swoje konto kodem wysłanym na Twojego maila <?= $_SESSION['email'] ?? '' ?>.
            </div>
            <div>
                <?php foreach ($errors as $error): ?>
                    <span class="error"><?= $error ?></span>
                <?php endforeach; ?>
            </div>
            <form action="index.php/api/activate-account" class="container__activation-form" method="POST">
                <input type="hidden" name="_method" value="PATCH">
                <label>
                    Kod aktywacyjny: <br>
                    <input type="text" name="activation-code" class="input--pin-code" maxlength="6"><br><br>
                </label>
                <button type="submit" class="panel__button">Aktywuj konto</button><br><br>
                <a href="index.php/api/generate-activation-code/<?= $_SESSION['email'] ?? '' ?>" style="font-size: 18px">Wyślij nowy kod aktywacyjny</a>
            </form>
        </div>
    </main>
</body>
</html>

