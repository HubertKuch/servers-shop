<?php

use Servers\Utils\Environment;
use Servers\views\components\MainPage;
use Servers\views\components\Servers;

?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <base href="<?= Environment::getBaseURL() ?>">
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
    <?php MainPage::nav(); ?>

    <main class="create-server-main">
        <p style="font-size: 28px;">Stwórz server według swoich potrzeb</p>

        <br><br>
        <form action="index.php/api/create-server" method="post">
            <input type="hidden" class="egg_type" name="egg_type" value="vanilla">
            <input type="hidden" class="package_id" name="package_id" value="0">
            <input type="hidden" class="egg_id" name="egg_id" value="1">

            <?php
                foreach ($errors as $error) {
                    echo "<span class='error'>$error</span>";
                }
            ?>

            <p>Typ servera</p>

            <section class="eggs">

                <?php
                    Servers::minecraftEgg("views/assets/vanilla_mc.png", "Vanilla", 5);
                    Servers::minecraftEgg("views/assets/forge_mc.png", "Forge", 4);
                ?>
            </section>
            <br><br>
            <p>Nazwa servera (możesz ją później zmienić)</p>
            <label>
                <input type="text" class="panel__input" name="server_name" placeholder="Super server">
            </label>

            <br><br>
            <p>Wersja Minecraft</p>
            <label>
                <input type="text" class="panel__input" name="mc_version" placeholder="1.18.2">
            </label>

            <br><br>
            <p>Wersja Javy</p>
            <label>
                <select class="panel__input" name="java_version" >
                    <option value="8">8</option>
                    <option value="11">11</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                </select>
            </label>

            <br><br>
            <p>Pakiet</p>

            <section class="eggs">

                <?php
                    foreach ($packages as $package) {
                        Servers::package($package->id, $package->image_src, $package->name, "{$package->ram_size}MB ram / {$package->disk_size}MB dysku / {$package->processor_power}% procesora");
                    }
                ?>
            </section>

            <br><br>
            <button class="panel__button" type="submit">Kup</button>
        </form>
    </main>

    <script>
        'use strict';

        const eggs = document.querySelectorAll('.egg');
        const eggType = document.querySelector('.egg_type');
        const eggId = document.querySelector('.egg_id');
        const packages = document.querySelectorAll('.package');
        const packageIdElem = document.querySelector('.package_id');

        for (const egg of eggs) {
            egg.addEventListener('click', (e) => {
                eggs.forEach(egg => egg.style.background = '#a187e1');

                egg.style.background = '#8659ea'
                let eggTypeName = e.target.getAttribute('data-egg-name');
                let selectedEggId = e.target.getAttribute('data-egg-id');

                if(!eggTypeName) {
                    eggTypeName = e.target.parentElement.getAttribute('data-egg-name')
                    selectedEggId = e.target.parentElement.getAttribute('data-egg-id');
                }

                eggType.value = eggTypeName;
                eggId.value = selectedEggId;
            });
        }

        for (const mcPackage of packages) {
            mcPackage.addEventListener('click', (e) => {
                packages.forEach(mcPackage => mcPackage.style.background = '#a187e1');

                mcPackage.style.background = '#8659ea'
                let packageId = e.target.getAttribute('data-package-id');

                if(!packageId) {
                    packageId = e.target.parentElement.getAttribute('data-package-id')
                }
                packageIdElem.value = packageId;
            });
        }
    </script>
</body>
</html>
