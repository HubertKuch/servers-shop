<?php

namespace Servers\views\components;

class MainPage {
    public static final function server(object $server, object $package): void {
        printf('
            <div class="server__card" onclick="localStorage.setItem(\'user-panel-actual-visible\', \'bought-servers\')" ">
                <a href="index.php/panel" style="font-size: 32px">
                    <img src="%s" alt=""><br>
                    <span>%s</span>
                    <span style="float: right">%s</span>
                </a>
            </div>
        ', $package->getImageSrc(), $server->getTitle(), $package->getName());
    }

    public static final function nav() {
        print '<nav class="main__nav">
            <a href="index.php/" class="nav__icon-link">
                <img class="nav__icon" src="views/icons/home.svg" alt="home">
            </a>
    
            <a href="index.php/servers" class="nav__icon-link">
                <img class="nav__icon" src="views/icons/game.svg" alt="game-panel">
            </a>
    
            <a href="index.php/panel" class="nav__icon-link">
                <img  class="nav__icon" src="views/icons/account.svg" alt="account-panel">
            </a>
        </nav>';
    }
}
