<?php
    use Servers\views\components\MainPage;
?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <base href="http://<?= $_SERVER['HTTP_HOST'] ?>/servers/">
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
    <nav class="main__nav">
        <a href="index.php/panel" class="nav__icon-link">
            <img class="nav__icon" src="views/icons/home.svg" alt="home">
        </a>

        <a href="index.php/panel" class="nav__icon-link">
            <img class="nav__icon" src="views/icons/game.svg" alt="game-panel">
        </a>

        <a href="index.php/panel" class="nav__icon-link">
            <img  class="nav__icon" src="views/icons/account.svg" alt="account-panel">
        </a>
    </nav>

    <header class="header">
        <p class="header__title">Serwery do <span style="color: #AF88E1;">Twoich</span> ulubionych <span style="color: #AF88E1;">gier</span> pod ręką.</p>
        <img src="views/icons/console.svg" alt="gaming console" class="header__gaming-console">
    </header>

    <main class="main-page-main">
        <p>Ostatnio dodane <span class="main-page-main__counter">4</span></p>
        <div class="last-added-servers">

        </div>
    </main>
</body>
</html>
