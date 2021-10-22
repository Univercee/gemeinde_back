<?php
namespace App\Managers\Events;

use App\Managers\Queues\QueueFactory;
use App\Managers\Services\ServiceManager;
use Illuminate\Support\Facades\DB;

class EventManager
{

    //
    public static function add(Event $event)
    {
        return DB::table('events')->insertOrIgnore($event->getArray());
    }

    //
    public static function addAll(EventList $event_list)
    {
        return DB::table('events')->insertOrIgnore($event_list->getArray());
    }

    //
    public static function dispatch(ServiceManager $sm)
    {
        $service_id = $sm->getServiceId();
        self::dispatchById($service_id);
        //TODO: add self::dispatchCountryWideEvents($service_id);
    }

    //
    public static function dispatchById(int $service_id) //TODO: deliver_at depend on frequency; logging?
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
            WHERE e.service_id = :service_id
            AND e.notify_earliest_at <= NOW()
            AND (e.notify_latest_at > NOW() OR e.notify_latest_at IS NULL)",
            ["service_id" => $service_id]
        );
        $messages = json_decode(json_encode($messages), true); //convert stdClass to array

        $produced_email = QueueFactory::email()->addtoQueue($messages); //produsing can be asynchronously
        $produced_tg = QueueFactory::telegram()->addtoQueue($messages);
        
        return response()->json(["produced_email" => $produced_email, "produced_tg" => $produced_tg]);
    }
}
?>