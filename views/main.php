<?php

use Servers\Repositories;
use Servers\Utils\Environment;
use Servers\views\components\MainPage;
use Servers\views\components\UserPanel;

?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Strona Główna</title>
    <base href="<?= Environment::getBaseURL() ?>">

    <!-- Custom fonts for this template-->
    <link href="style/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="style/app.css" rel="stylesheet">

</head>
<body>
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

        <!-- Nav Item - Utilities Collapse Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                <i class="fas fa-fw fa-wrench"></i>
                <span>Ustawienia konta</span>
            </a>
            <?php UserPanel::nav(); ?>
        </li>

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
            <div class="row">
                <div class="col-12 mt-5 px-5">
                    <h1 class="text-gray-900 font-weight-bold">Serwery Minecraft pod ręką! </h1>
                    <hr class="sidebar-divider">
                </div>
            </div>
            <div class="row w-100 mt-3">
                <div class="col-xl-10 col-12 px-5">
                    <p class="font-weight-bold"> Ilość serwerów: <?= count($servers) ?></p>

                    <?php

                    if (empty($servers)) {
                        echo "<div>Zakup swój pierwszy server w panelu</div>";
                    } else {
                        $serversCount = count($servers);
                        foreach ($servers as $server) {
                            $package = Repositories::$packagesRepository->findOneById($server->getPackageId());

                            MainPage::server($server, $package);
                        }
                    }
                    ?>
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
