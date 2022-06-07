<?php

namespace Servers\views\components;

class MainPage {

    public static final function server(object $server, object $package): void {

        printf('
            <div class="row mb-3 " onclick="localStorage.setItem(\'user-panel-actual-visible\', \'bought-servers\')">
                <div class="col-12 box-shadow-transition px-0 mb-4">
                    <div class="card border-left-success shadow h-100 py-2 ">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col-lg-4 col-12 mr-2">
                                     <img src="%s" alt="" style="aspect-ratio: 16/9 " height="120px" >
                                </div>
                                <div class="col mr-2 mt-lg-0 mt-3">
                                    <div class="row">
                                           <a class="col" style="font-size: 22px; cursor: pointer; text-decoration-color: #1cc88a; color: #2d2e33" href="index.php/server-list">
                                          <p class="col">
                                            <span class="font-weight-bold px-0">Nazwa serwera:</span>
                                            <span class="col px-0">%s</span>
                                          </p>
                                       </a>
                                       <a class="col" style="font-size: 22px; cursor: pointer; text-decoration-color: #1cc88a; color: #2d2e33" href="index.php/server-list">
                                          <p class="col">
                                            <span class="font-weight-bold px-0">Typ serwera:</span>
                                            <span  class="col px-0">%s</span>
                                          </p>
                                       </a>
                                   </div>
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
