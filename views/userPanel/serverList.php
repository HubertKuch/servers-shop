
<?php

use Servers\Models\enumerations\ServerStatus;
use Servers\Repositories;
use Servers\Utils\Environment;
use Servers\views\components\UserPanel;

?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panel</title>
    <base href="<?= Environment::getBaseURL() ?>">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="style/app.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/31d2710bc5.js" crossorigin="anonymous"></script>

</head>
<body class="panel">

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
                <div class="container-fluid">
                    <div class="card shadow mb-4 mt-5">
                        <?php
                        foreach ($errors as $error) {
                            echo "<div style='color: red' class='font-weight-bold'>$error !</div>";
                        }
                        ?>
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Lista serwerów</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">

                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <?php
                                    if (empty($payments)) {
                                        echo "Nie dokonnałeś jeszcze żadnego zakupu.";
                                    }
                                    ?>
                                    <thead>
                                    <tr>
                                        <th>TYTUŁ</th>
                                        <th>STATUS</th>
                                        <th>DATA UTWORZENIA</th>
                                        <th>DATA WYGAŚNIĘCIA</th>
                                        <th>PACZKA</th>
                                        <th>ODNÓW</th>
                                        <th>ZARZĄDZAJ</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>TYTUŁ</th>
                                        <th>STATUS</th>
                                        <th>DATA UTWORZENIA</th>
                                        <th>DATA WYGAŚNIĘCIA</th>
                                        <th>PACZKA</th>
                                        <th>ODNÓW</th>
                                        <th>ZARZĄDZAJ</th>
                                    </tr>
                                    </tfoot>
                                    <tbody>
                                    <?php foreach($userServers as $server): ?>
                                        <tr>
                                            <td><?= $server->getTitle() ?></td>
                                            <td><?= match ($server->getStatus()) {
                                                    ServerStatus::SOLD->value => '<span class="server-status server-status--active">Aktywny</span>',
                                                    ServerStatus::IN_MAGAZINE->value,
                                                    ServerStatus::EXPIRED->value => '<span class="server-status server-status--expired">Wygasł</span>'
                                                } ?>
                                            </td>
                                            <td><?= date('m/d/Y H:i:s', $server->getCreateDate()) ?></td>
                                            <td><?= date('m/d/Y H:i:s', $server->getExpireDate()) ?></td>
                                            <td><?php $package = Repositories::$packagesRepository->findOneById($server->getPackageId()); echo $package->getDescription() ?></td>
                                            <td>
                                                <form action="index.php/api/unsuspend-server?id=<?= $server->getId() ?>" method="post">
                                                    <input type="hidden" name="_method" value="PATCH">
                                                    <button type="submit" class="button--renew btn btn-success">Odnów</button>
                                                </form>
                                            </td>
                                            <td>
                                                <button class="btn-warning btn"><a class="text-white" target="_blank" href="http://178.32.202.241:85/server/<?= Repositories::$pterodactyl->server($server->getPterodactylId())->identifier ?>">Zarządzaj</a></button>
                                            </td>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        const options = document.querySelectorAll('.admin__panel--section-option');
        const sections = document.querySelectorAll('.admin__panel--section');
        const beforeActiveSectionClass = localStorage.getItem("user-panel-actual-visible") ?? null;

        sections.forEach(section => {
            if (!section.classList.contains(beforeActiveSectionClass)) {
                section.classList.remove('section--visible');
                section.classList.add('section--invisible');
            } else {
                section.classList.add('section--visible');
                section.classList.remove('section--invisible');
            }
        })

        options.forEach(option => option.addEventListener('click', ({ target }) => {
            const sectionClass = target.getAttribute('data-section-class');

            sections.forEach(section => {
                if (!section.classList.contains(sectionClass)) {
                    section.classList.remove('section--visible');
                    section.classList.add('section--invisible');
                } else {
                    section.classList.add('section--visible');
                    section.classList.remove('section--invisible');
                    localStorage.setItem("user-panel-actual-visible", sectionClass);
                }
            })
        }));
    </script>
    <!-- Bootstrap core JavaScript-->
    <script src="js/jquery/jquery.min.js"></script>
    <script src="js/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="js/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
    <script>
        // Toggle the side navigation
        $(document).ready( function () {
            $("#dataTable").dataTable();
        });

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
