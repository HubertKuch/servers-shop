<?php

namespace Servers\views\components;

class MainPage {

    public static final function server(object $server, object $package): void {

        printf('
            <div class="row mb-3" onclick="localStorage.setItem(\'user-panel-actual-visible\', \'bought-servers\')" ">
                <div class="col-12  mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                
                                <div class="col mr-2">
                                     <img src="%s" alt="" style="aspect-ratio: 16/9 " height="120px" >
                                </div>
                                <div class="col mr-2">
                                   <a class="row" href="index.php/panel" style="font-size: 22px">    
                                      <span class="col">%s</span>
                                      <span  class="col">%s</span>
                                   </a>
                                </div>
                                
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                                
                            </div>
                            
                        </div>
                    </div>
                </div>                
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
