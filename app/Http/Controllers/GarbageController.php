<?php
namespace App\Http\Controllers;

use App\Managers\GarbageManager;
use App\Managers\SmsManager;
use Illuminate\Support\Facades\Http;

class GarbageController extends Controller
{
  private const NEXT_DAY = 'daily_digest';

  public function __construct()
  {
    $this->middleware('enforceJson');
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
            return response()->json(['message' => 'Success E']);
          }else if($gcJoinKey->channel == 'T'){
            $text = GarbageManager::makeBody($gcKey->type, $gcKey->date, $gcJoinKey->language);
            GarbageManager::addToTgQueue($gcJoinKey->user_id, $text, $gcJoinKey->language, $gcJoinKey->telegram_id);
            return response()->json(['message' => 'Success T',$gcData, $gcGetUsers]);
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
