<?php
namespace App\Http\Controllers;

class ConfigController extends Controller
{
    public function __construct() {
      $this->middleware('enforceJson');
    }

    public function getKeys() {
        $keys = [
                "googleRecaptchaSiteKey" => getenv('GOOGLE_RECAPTCHA_SITE_KEY'),
                "googleMapKey" => getenv('GOOGLE_MAP_KEY'),
                "tgBotName" => getenv('TG_BOT_NAME'),
                "appRoot" => getenv('APP_URL'),
                "locationRequestForm" => 'https://docs.google.com/forms/d/e/1FAIpQLScqylrgpCicOf3k3NNkKDdF7Q3MX7XBdfsFmvZbzuWItZOt1A/viewform?vc=0&c=0&w=1&flr=0&gxids=7628&entry.839337160='
              ];
        return response()->json($keys);
    }

}
