<?php

namespace Servers\views\components;

class UserPanel {
    public static function serverCard(int $id, string $title, string $createDate, string $expireDate, string $package, float $cost): void {
        printf('
            <div class="server__card">
                <div class="card__image">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTeHevG6-E4Dnz5f5f1_wsqlQkXg78F-3Jp2w&usqp=CAU" alt="">
                </div>
                <div class="card__footer">
                    <div class="footer__title">%s</div>
                    <div class="footer__cost">Cena: %d</div>
                    <div class="footer__create-date">Kupinono: %s</div>
                    <div class="footer__expire-date">Wygasa: %s</div>
                </div>
            </div>
        ', $title, $cost, $createDate, $expireDate);
    }
}