<?php

namespace Servers\views\components;

class Servers {
    public static final function minecraftEgg(string $imgSource, string $name, int $eggId): void {
//  temp comment class: egg
        printf('
            <div class="row egg" data-egg-name="%s" data-egg-id="%s">
                <div class="col-12">
                    <div class="row justify-content-center">
                        <div class="egg__name d-flex align-items-center justify-content-center">
                            <h1 class="text-gray-900">%s</h1>    
                        </div>
                    </div>
                </div>
                <div class="col-12 ">
                    <div class="row justify-content-center">
                        <img src="%s" alt="%s" class="egg__img">
                    </div>
                </div>
            </div>
        ', $name, $eggId, $name, $imgSource, $name);
    }

    public static final function package(int $packageId, string $packageImgSource, string $name, string $desc, float $price): void {
        printf('
            <div class="package col-xl-4 mt-xl-0 mt-3 mr-5 card col-12 box-shadow-transition " data-package-id="%s" data-package-price="%s">
                <div class="card-body ">
                    <div class="col">
                        <div class="row justify-content-center">
                            <img src="%s" alt="%s" class="egg__img">
                        </div>
                    </div>
                    
                    <div class="egg__name mt-3 d-flex align-items-center justify-content-center ml-3 font-weight-bold">%s</div>
                    <div class="egg__desc d-flex align-items-center justify-content-center ml-3 text-center">%s</div>
                </div> 
            </div>
        ', $packageId, $price, $packageImgSource, $name, $name, $desc);
    }
}
