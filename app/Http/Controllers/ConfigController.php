<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function getKeys(Request $request){
        $keys = ["googleRecaptchaSiteKey" => "6LehX2caAAAAAKKV2_vQTCGkh4-y4LHTjkTEfAQC",
                "googleMapKey" => "lool", "tgBotName" => "1592850338:AAH1NeLiWUW9jy6861AZmjuG6JRdqtywyhw"];
        return response()->json($keys);
    }
}
