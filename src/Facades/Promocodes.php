<?php

namespace Zorb\Promocodes\Facades;

use Illuminate\Support\Facades\Facade;
use Zorb\Promocodes\Promocodes as PromocodesService;

class Promocodes extends Facade
{
    //
    protected static function getFacadeAccessor()
    {
        return PromocodesService::class;
    }
}
