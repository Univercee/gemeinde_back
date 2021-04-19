<?php
namespace App\Http\Controllers;

use App\Managers\GarbageManager;
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
              }else if($gcJoinKey->channel == 'T'){
                $body = GarbageManager::makeBody($gcKey->type, $gcKey->date, $gcJoinKey->language);
                GarbageManager::addToTgQueue($gcJoinKey->user_id, $body, $gcJoinKey->language, $gcJoinKey->telegram_id);
              }
            }
          }
        }
        return response()->json(['message' => 'Success']);
    }
}
