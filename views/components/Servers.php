<?php

namespace Servers\views\components;

class Servers {
    public static final function minecraftEgg(string $imgSource, string $name, int $eggId): void {
        printf('
            <div class="egg row" data-egg-name="%s" data-egg-id="%s">
                <img src="%s" alt="%s" class="egg__img">
                <div class="egg__name d-flex align-items-center justify-content-center ml-3">
                    <h1 class="text-gray-900">%s</h1>    
                </div>
            </div>
        ', $name, $eggId, $imgSource, $name, $name);
    }

    public static final function package(int $packageId, string $packageImgSource, string $name, string $desc, float $price): void {
        printf('
            <div class="package row" data-package-id="%s" data-package-price="%s">
                <img src="%s" alt="%s" class="egg__img">
                <div class="egg__name d-flex align-items-center justify-content-center ml-3">%s</div>
                <div class="egg__desc d-flex align-items-center justify-content-center ml-3">%s</div>
            </div>
        ', $packageId, $price, $packageImgSource, $name, $name, $desc);
    }
}
