<?php

namespace Servers\views\components;

class Servers {
    public static final function minecraftEgg(string $imgSource, string $name, int $eggId): void {
        printf('
            <div class="egg" data-egg-name="%s" data-egg-id="%s">
                <img src="%s" alt="%s" class="egg__img">
                <div class="egg__name">%s</div>
            </div>
        ', $name, $eggId, $imgSource, $name, $name);
    }

    public static final function package(int $packageId, string $packageImgSource, string $name, string $desc): void {
        printf('
            <div class="package" data-package-id="%s">
                <img src="%s" alt="%s" class="egg__img">
                <div class="egg__name">%s</div>
                <div class="egg__desc">%s</div>
            </div>
        ', $packageId, $packageImgSource, $name, $name, $desc);
    }
}
