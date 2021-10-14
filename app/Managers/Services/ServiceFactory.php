<?php
namespace App\Managers\Services;

use App\Managers\Services\GarbageServiceManager;
use App\Managers\Services\SwisscomServiceManager;

class ServiceFactory{

    //
    public static function garbage()
    {
        return new GarbageServiceManager();
    }

    //
    public static function swisscom()
    {
        return new SwisscomServiceManager();
    }

}