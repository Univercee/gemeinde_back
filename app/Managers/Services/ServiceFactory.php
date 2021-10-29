<?php
namespace App\Managers\Services;
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

    //
    public static function ggamaur()
    {
        return new GgamaurServiceManager();
    }
    

}