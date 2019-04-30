<?php

namespace autoengine\crudpack\Facades;

use Illuminate\Support\Facades\Facade;

class crudpack extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'crudpack';
    }
}
