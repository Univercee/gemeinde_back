<?php
namespace App\Managers;

use Illuminate\Support\Facades\DB;
class EventManager
{
    public static function addEvent($location_id, $service_id, $valid_from, $valid_until, $title_en, $text_en, $title_de, $text_de, $external_id = null){
        DB::table('events')->insert(['location_id'=>$location_id,
                                    'service_id'=>$service_id, 
                                    'valid_from'=>$valid_from, 
                                    'valid_until'=>$valid_until,
                                    'title_en'=>$title_en,
                                    'text_en'=>$text_en,
                                    'title_de'=>$title_de,
                                    'text_de'=>$text_de,
                                    'external_id'=>$external_id]);
    }
}
?>