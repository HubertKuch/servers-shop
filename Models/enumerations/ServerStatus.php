<?php

namespace Servers\Models\enumerations;

enum ServerStatus: string {
    case IN_MAGAZINE = 'inMagazine';
    case SOLD = 'sold';
    case EXPIRED = 'expired';
}
