<?php
namespace App\Managers\Services;

use App\Managers\Services\GarbageServiceManager;

class ServiceFactory{

    //
    public static function garbage()
    {
        return new GarbageServiceManager();
    }

}