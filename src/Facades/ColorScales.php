<?php

namespace SocialDept\ColorScales\Facades;

use Illuminate\Support\Facades\Facade;

class ColorScales extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'color-scales';
    }
}
