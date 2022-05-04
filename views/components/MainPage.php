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
}