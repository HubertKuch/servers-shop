<?php

use Avocado\HTTP\JSON\JSON;
use Servers\Controllers\ServersController;
use Servers\Utils\Environment;
use Servers\views\components\MainPage;
use Servers\views\components\Servers;
use Servers\views\components\UserPanel;

?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Stwórz serwer</title>
    <base href="<?= Environment::getBaseURL() ?>">

    <!-- Custom fonts for this template-->
    <link href="style/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="style/app.css" rel="stylesheet">

</head>
<body>
<!--    --><?php //MainPage::nav(); ?>
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php/">

                <div class="sidebar-brand-text mx-3">MC Admin <sup>panel</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php/">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Panel</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Ustawienia
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="index.php/servers">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Stwórz serwer</span>
                </a>
            </li>

            <?php UserPanel::nav(); ?>

            <!-- Divider -->
            <hr class="sidebar-divider mb-0">
            <li class="nav-item">
                <a class="nav-link" href="index.php/api/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Wyloguj się</span>
                </a>
            </li>

            <hr class="sidebar-divider">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <div class="col px-5 ">
                    <div class="row">
                        <div class="col px-0">
                            <h1 class="text-gray-900 mt-5 font-weight-bold">Stwórz server według swoich potrzeb</h1>
                            <hr class="sidebar-divider">
                        </div>
                    </div>
                    <form action="index.php/api/create-server" method="post">
                        <input type="hidden" class="egg_type" name="egg_type" value="vanilla">
                        <input type="hidden" class="package_id" name="package_id" value="0">
                        <input type="hidden" class="egg_id" name="egg_id" value="1">
                        <div class="row">
                            <?php
                            foreach ($errors as $error) {
                                echo "<div style='color: red'>$error !</div>";
                            }
                            ?>
                        </div>

                        <div class="row">

                            <div class="col mt-3">
                                <div class="row">
                                    <h1 class="h4 text-gray-900 font-weight-bold mb-3">Typ serwera</h1>
                                </div>

                                <div class="row">
                                    <div class="card col-12 col-xl-4 mr-xl-5 box-shadow-transition">
                                        <div class="card-body eggs">
                                            <?php
                                            Servers::minecraftEgg("views/assets/vanilla_mc.png", "Vanilla", 5);
                                            ?>
                                        </div>

                                    </div>
                                    <div class="card col-12 col-xl-4 mt-xl-0 mt-3 box-shadow-transition">
                                        <div class="card-body eggs">
                                            <?php
                                            Servers::minecraftEgg("views/assets/forge_mc_v2.jpg", "Forge", 2);
                                            ?>
                                        </div>

                                    </div>
                                </div>
                                <hr class="sidebar-divider">
                            </div>


                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <h1 class="h4 text-gray-900 font-weight-bold">Pakiet</h1>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="eggs ">
                                            <div class="row">
                                                <?php
                                                foreach ($packages as $package) {
                                                    Servers::package($package);
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="sidebar-divider">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <h1 class="h4 text-gray-900 font-weight-bold mb-3">Nazwa serwera </h1>
                                </div>
                                <div class="row">
                                    <div class="col-xl-4 col-lg-6 mt-3 px-0">
                                        <div class="form-group">
                                            <p class="mb-0">Nowa nazwa (możesz ją później zmienić)</p>
                                            <input type="text" class="form-control form-control-user panel__input p-4" style="border-radius:20px " name="server_name" placeholder="Podaj nazwę" required>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <p>Wersja Minecraft</p>
                                                <select class="custom-select" name="mc_version">
                                                    <?php

                                                    foreach(ServersController::getReleasedMinecraftVersions() as $version): ?>
                                                        <option value="<?= $version ?>"><?= $version ?></option>
                                                    <?php endforeach; ?>

                                                </select>
                                            </label>
                                        </div>
                                        <div class="form-group d-none forge-version-version-container">
                                            <p>Wersja Forge</p>
                                            <input type="text"  class="form-control form-control-user panel__input p-4" style="border-radius:20px " name="forge_version" placeholder="1.18.2">
                                        </div>
                                        <div class="form-group">
                                            <p>Wersja Javy</p>
                                                <select class="panel__input form-control" name="java_version" style="border-radius:20px; height: 50px" >
                                                    <option value="8">8</option>
                                                    <option value="11">11</option>
                                                    <option value="16">16</option>
                                                    <option value="17">17</option>
                                                </select>
                                        </div>
                                        <div class="row  mt-3 mb-5">
                                            <div class="col">
                                                <button type="submit" class="btn btn-primary w-50 btn-user btn-block mt-auto mt-5" style="border-radius:20px;height: 50px">
                                                    Kup
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <main class="create-server-main">

    </main>

    <script>
        'use strict';

        const eggs = document.querySelectorAll('.egg');
        const eggType = document.querySelector('.egg_type');
        const eggId = document.querySelector('.egg_id');
        const packagesElements = document.querySelectorAll('.package');
        const packageIdElem = document.querySelector('.package_id');
        const packages = <?= (new JSON($packages, true))->getSerializedData() ?>;
        const submitButton = document.querySelector('form button[type=submit]');

        for (const egg of eggs) {
            egg.addEventListener('click', (e) => {
                eggs.forEach(egg => egg.classList.remove('border-bottom-success'));

                const eggTypeName = e.currentTarget.getAttribute('data-egg-name');
                const selectedEggId = e.currentTarget.getAttribute('data-egg-id');

                egg.classList.add('border-bottom-success')

                eggType.value = eggTypeName;
                eggId.value = selectedEggId;

                const forgeVersionContainer = document.querySelector('.forge-version-version-container');

                if (parseInt(eggId.value) === 2) {
                    forgeVersionContainer.classList.remove('d-none')
                    forgeVersionContainer.classList.add('d-block')
                } else {
                    forgeVersionContainer.classList.remove('d-block')
                    forgeVersionContainer.classList.add('d-none')
                }
            });
        }

        for (const mcPackage of packagesElements) {
            mcPackage.addEventListener('click', (e) => {
                packagesElements.forEach(mcPackage => mcPackage.classList.remove('border-bottom-success'));

                mcPackage.classList.add('border-bottom-success')

                const id = e.currentTarget.getAttribute('data-package-id');

                const packagePrice = packages.find(p => p.id === parseInt(id)).cost;

                submitButton.textContent = `Kup za ${parseFloat(packagePrice).toFixed(2)}PLN`;
                packageIdElem.value = id;
            });
        }
    </script>

    <!-- Bootstrap core JavaScript-->
    <script src="js/jquery/jquery.min.js"></script>
    <script src="js/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="js/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <script>
        // Toggle the side navigation
        $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
            $("body").toggleClass("sidebar-toggled");
            $(".sidebar").toggleClass("toggled");
            if ($(".sidebar").hasClass("toggled")) {
                $('.sidebar .collapse').collapse('hide');
            };
        });
    </script>
</body>
</html>
