<?php


namespace App\Http\Controllers;



class ConfigController extends Controller
{
    public function getKeys(){
        $keys = ["googleRecaptchaSiteKey" => getenv('GOOGLE_RECAPTCHA_SITE_KEY'),
                "googleMapKey" => getenv('GOOGLE_MAP_KEY'),
                "tgBotName" => getenv('TG_BOT_NAME'),
                "appRoot" => getenv('APP_ROOT')];
        return response()->json($keys);
    }
}
