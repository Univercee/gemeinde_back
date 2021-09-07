<?php
namespace App\Http\Controllers;

use Twilio\Rest\Client;
use App\Managers\GarbageManager;

use App\Managers\SmsManager;
use Illuminate\Support\Facades\Http;
use App\Channels\Messages\WhatsAppMessage;


class GarbageController extends Controller
{
  private const NEXT_DAY = 'daily_digest';

  public function __construct()
  {
    $this->middleware('enforceJson');
  }
    public function garbageInEvents(){
      return response()->json(GarbageManager::addGarbageEvents());
    }
    public function getServiceInfo(){
        $gcGetUsers = GarbageManager::getUsers(self::NEXT_DAY);
        $gcData = GarbageManager::getNextDateEvents();
        foreach ($gcGetUsers as $gcJoinKey){
          $joinLocs = $gcJoinKey->location_id;
          foreach ($gcData as $gcKey){
            $gcLocs = $gcKey->location_id;
            if($joinLocs == $gcLocs){
              if($gcJoinKey->channel == 'E'){
                $body = GarbageManager::makeBody($gcKey->type, $gcKey->date, $gcJoinKey->language);
                $title = GarbageManager::makeTitle($gcJoinKey->language);
                GarbageManager::addToEmailQueue($gcJoinKey->user_id, $title, $body, GarbageManager::TEMPLATE_ID, $gcJoinKey->language, $gcJoinKey->email);
              }else if($gcJoinKey->channel == 'T'){
                $body = GarbageManager::makeBody($gcKey->type, $gcKey->date, $gcJoinKey->language);
                GarbageManager::addToTgQueue($gcJoinKey->user_id, $body, $gcJoinKey->language, $gcJoinKey->telegram_id);
                return response()->json(['message' => 'Success TG']);

              }else if($gcJoinKey->channel == 'W'){
                $body = GarbageManager::makeBody($gcKey->type, $gcKey->date, $gcJoinKey->language);
                $sid = env("TWILIO_AUTH_SID"); // Your Account SID from www.twilio.com/console
                $token = env("TWILIO_AUTH_TOKEN"); // Your Auth Token from www.twilio.com/console

                $client = new Client($sid, $token);
                $client->messages
                  ->create("whatsapp:".env('PHNUMBER'), // to your phone number
                    array(
                      "from" => "whatsapp:".env('TWILIO_WHATSAPP_FROM'), // twillio from
                      "body" => $body
                    )
                  );
                return response()->json(['message' => "Whatsapp sent"]);
              }else if($gcJoinKey->channel == 'S'){
                $text = GarbageManager::makeBody($gcKey->type, $gcKey->date, $gcJoinKey->language);
                SmsManager::sendSMS($text,env('PHNUMBER'));
                return response()->json(['message' => 'SMS send successfully']);
              }
          }
        }
      }

    return response()->json(['message' => 'Success']);
  }
}
