<?php

namespace App\Facades;

class ProductAdServiceFacade {

    public static function resolveFacade() {
        return resolve('ad-service');
    }


    public static function __callStatic($name, $arguments)
    {
        return self::resolveFacade()
        ->$name(...$arguments);
    }
}