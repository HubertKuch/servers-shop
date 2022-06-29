<?php

use Servers\Models\ServerStatus;
use Servers\Utils\Environment;
use Servers\Repositories;
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

                <div class="row">
                    <div class="col px-5 mt-5">
                        <h1 class="text-gray-900">Ustawienia konta</h1>
                    </div>
                </div>
                <div class="col px-5">
                    <p class="settings__errors" style="color: red;">
                        <?php
                        foreach ($_GET as $error => $message) {
                            echo $message;
                        }
                        ?>
                    </p>

                    <hr style="color: white">
                    <h4 class="text-gray-900 mb-2">Hasło</h4>
                    <form action="index.php/api/change-password" method="POST" class="user">
                        <input type="hidden" name="_method" value="PATCH">
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 mt-3">
                                <div class="form-group">
                                    <p class=" mb-0">Stare hasło</p>
                                    <input type="password" class="form-control form-control-user" name="old-password" required>
                                </div>
                                <div class="form-group">
                                    <span>Nowe hasło</span>
                                    <input type="password" class="form-control form-control-user" name="new-password" required>
                                </div>

                                <div class="row mt-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-primary w-50 btn-user btn-block mt-auto mt-5">
                                            Zapisz
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr style="color: white">
                    <hr style="color: white">
                </div>
            </div>
        </div>

    </div>


    <script>
        'use strict';
        const options = document.querySelectorAll('.admin__panel--section-option');
        const sections = document.querySelectorAll('.admin__panel--section');
        const beforeActiveSectionClass = localStorage.getItem("user-panel-actual-visible") ?? null;
        console.log(2)

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
