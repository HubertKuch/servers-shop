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

    public static final function availableGame(int $id, string $tile) {
        printf('<div class="available-game"><a href="%s">%s</a></div>', $id, $tile);
    }
}