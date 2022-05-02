<?php

namespace Servers\Models;

enum ProductStatus: string {
    case IN_MAGAZINE = 'inMagazine';
    case SOLD = 'sold';
}
