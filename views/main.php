<?php

use Servers\Repositories;
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
    <?php MainPage::nav(); ?>
    <header class="header">
        <p class="header__title">Serwery <span style="color: #AF88E1;">Minecraft</span> pod <span style="color: #AF88E1;">pod ręką</span>.</p>
        <img src="views/icons/console.svg" alt="gaming console" class="header__gaming-console">
    </header>

    <main class="main-page-main">
        <div class="last-added-servers">
            <p>Twoje servery
                <?php
                if (empty($servers)) {
                    echo "<div>Zakup swój pierwszy server w panelu</div>";
                } else {
                    $serversCount = count($servers);
                    echo "<span class=\"main-page-main__counter\">$serversCount</span>";
                    foreach ($servers as $server) {
                        $package = Repositories::$packagesRepository->findOneById($server->package_id);

                        MainPage::server($server->title, $package->image_src);
                    }
                }
                ?>
            </p>
        </div>
    </main>
</body>
</html>
