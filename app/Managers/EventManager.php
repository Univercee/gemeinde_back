<?php
namespace App\Managers;

use Illuminate\Support\Facades\DB;
class EventManager
{
    public static function addEvent($location_id, $source, $valid_from, $valid_until, $title_en = null, $text_en = null, $title_de = null, $text_de = null, $external_id = null){
        DB::table('events')->insert(['location_id'=>$location_id,
                                    'source'=>$source, 
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