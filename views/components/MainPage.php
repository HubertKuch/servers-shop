<?php

namespace Servers\views\components;

class MainPage {
    public static final function server(string $name, string $imgSrc): void {
        printf('
            <div class="server__card">
                <img src="%s" alt=""><br>
                <a href="index.php/panel" style="font-size: 32px">%s</a>
            </div>
        ', $imgSrc, $name);
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
