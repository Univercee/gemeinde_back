<?php
namespace App\Managers;

use App\Managers\Queues\QueueFactory;
use Illuminate\Support\Facades\DB;
class EventManager
{

    //
    public static function addEvent($location_id, $service_id, $valid_from, $valid_until, $title_en, $text_en, $title_de, $text_de, $external_id = null)
    {
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

    //
    public static function dispatch(int $event_id) //TODO: deliver_at depend on frequency; logging?
    {
        $messages = app('db')->select(
            "SELECT u.id as user_id, e.id as event_id, u.email, u.telegram_id, uls.channel, NOW() as deliver_at,
            CASE 
                WHEN u.language = 'en'
                    THEN e.title_en
                WHEN u.language = 'de'
                    THEN e.title_de
                    ELSE e.title_en
            END as title,
            CASE 
                WHEN u.language = 'en'
                    THEN e.text_en
                WHEN u.language = 'de'
                    THEN e.text_de
                    ELSE e.text_en
            END as body
            FROM user_location_services as uls
            JOIN user_locations ul ON ul.id = uls.user_location_id
            JOIN users u ON u.id = ul.user_id
            JOIN events e ON e.location_id = ul.location_id AND e.service_id = uls.service_id 
            WHERE e.id = :event_id AND e.valid_from <= NOW() AND e.valid_until > NOW()",
            ["event_id" => $event_id]
        );
        $messages = json_decode(json_encode($messages), true); //convert stdClass to array

        $produced_email = QueueFactory::email()->addtoQueue($messages); //produsing can be asynchronously
        $produced_tg = QueueFactory::telegram()->addtoQueue($messages);
        
        return response()->json(["produced_email" => $produced_email, "produced_tg" => $produced_tg]);
    }
}
?>