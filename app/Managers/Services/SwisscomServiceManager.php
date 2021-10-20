<?php
namespace App\Managers\Services;

use App\Managers\Events\Event;
use App\Managers\Events\EventList;
use App\Managers\Services\ServiceManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SwisscomServiceManager extends ServiceManager
{
    const GLOBAL_LOCATION_ID = 1;
    const SERVICE_ID = 7;
    const TEMPLATE_ID = -1;

    function __construct()
    {
        parent::__construct(self::SERVICE_ID, self::TEMPLATE_ID);
    }

    //
    public function getEvents(): EventList
    {
        $responses_list = array_merge($this->getGlobal(), $this->getLocal());
        $events = array();
        
        
        for($i=0;$i<count($responses_list);$i++){
            $time_attributes = $this->getTimeAttributes($responses_list[$i]['attributes']);
            $starts_at = $time_attributes["starts_at"];
            $ends_at = $time_attributes["ends_at"]; 
            $notify_earliest_at = $time_attributes["notify_earliest_at"];
            $notify_latest_at = $time_attributes["notify_latest_at"];

            $event = new Event($responses_list[$i]['location'],
                            $this->SERVICE_ID,
                            $responses_list[$i]['id'],
                            $starts_at,
                            $ends_at,
                            $notify_earliest_at,
                            $notify_latest_at,
                            $responses_list[$i]['title'],
                            $responses_list[$i]['description'],
                            $responses_list[$i]['title_de'],
                            $responses_list[$i]['description_de']
            );
            array_push($events, $event);
        }
        return new EventList(...$events);
    }
    
    //
    private function getLocal(): array
    {
        $locations = $this->getLocations();
        $responses_list_local = array();
        foreach($locations as $location){
            $response_en_local = Http::get("https://www.swisscom.ch/outages/guest/?origin=portal&lang=en&zip=".$location->zipcode)->json();
            $response_de_local = Http::get("https://www.swisscom.ch/outages/guest/?origin=portal&lang=de&zip=".$location->zipcode)->json();
            for($i=0; $i<count($response_en_local); $i++){
                $response_en_local[$i]['location'] = $location->id;
                $response_en_local[$i]['title_de'] = $response_de_local[$i]['title'];
                $response_en_local[$i]['description_de'] = $response_de_local[$i]['description'];
                array_push($responses_list_local, $response_en_local[$i]);
            }
        }
        return $responses_list_local;
    }

    //
    private function getGlobal(): array
    {
        $responses_list_global = array();
        $response_en_global = Http::get("https://www.swisscom.ch/outages/guest/?origin=portal&lang=en")->json();
        $response_de_global = Http::get("https://www.swisscom.ch/outages/guest/?origin=portal&lang=de")->json();
        for($i=0; $i<count($response_en_global); $i++){
            $response_en_global[$i]['location'] = self::GLOBAL_LOCATION_ID;
            $response_en_global[$i]['title_de'] = $response_de_global[$i]['title'];
            $response_en_global[$i]['description_de'] = $response_de_global[$i]['description'];
            array_push($responses_list_global, $response_en_global[$i]);
        }
        return $responses_list_global;
    }

    //
    private function getLocations(): array
    {
        $response = DB::select("SELECT DISTINCT locations.id, locations.zipcode
                                FROM user_locations
                                JOIN locations ON user_locations.location_id = locations.id");
        return $response;
    }

    //
    private function getTimeAttributes(array $attributes_list): array
    {
        $time_attributes = [
            "starts_at" => null,
            "ends_at" => null,
            "notify_earliest_at" => null,
            "notify_latest_at" => null
        ];
        foreach($attributes_list as $attribute){
            $date = strtotime($attribute["value"]);
            $converted_date = null;
            if($date){
                $converted_date = date('Y-m-d H:i:s', $date);
            }
            switch($attribute["type"]){
                case "estimatedEndTime":
                    $time_attributes["ends_at"] =  $converted_date;
                    $time_attributes["notify_latest_at"] = $converted_date;
                    break;
                case "startTime":
                    $time_attributes["starts_at"] = $converted_date;
                    $time_attributes["notify_earliest_at"] = $converted_date;
                    break;
                case "endTime":
                    $time_attributes["ends_at"] =  $converted_date;
                    $time_attributes["notify_latest_at"] = $converted_date;
                    break;
            };
        }
        return $time_attributes;
    }
}
?>








