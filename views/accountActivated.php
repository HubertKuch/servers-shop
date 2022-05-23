<?php

use Servers\Utils\Environment;

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Konto zostało aktywowane</title>
    <base href="<?= Environment::getBaseURL() ?>">
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
    <div class="account-activation-section">
        <div>
            <div style="font-size: 24px;">Konto zostało aktywowane.</div><br>
            <a href="index.php/login">Kliknij aby się zalogować.</a>
        </div>
    </div>
</body>
</html>
