<?php

namespace Servers\views\components;

class MainPage {
    public static final function game(int $id, string $imageURL): void {
        printf('
            <div class="server__card" style="background: url(%s)">
                <a href="%s">
                </a>
            </div>
        ', $imageURL, $id);
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
