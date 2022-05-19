<?php

namespace Servers\Models;

enum ServerStatus: string {
    case IN_MAGAZINE = 'inMagazine';
    case SOLD = 'sold';
}
