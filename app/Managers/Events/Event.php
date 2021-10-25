<?php
namespace App\Managers\Events;

class Event {

    private int|null $id;
    private int|null $location_id;
    private int $service_id; 
    private int $external_id;
    private string|null $starts_at; 
    private string|null $ends_at;
    private string|null $notify_earliest_at;
    private string|null $notify_latest_at;
    private string $text_en;
    private string $title_en;
    private string $title_de;
    private string $text_de;

    //
    function __construct($location_id,
                        $service_id,
                        $external_id,
                        $starts_at, 
                        $ends_at,
                        $notify_earliest_at, 
                        $notify_latest_at,
                        $title_en, $text_en,
                        $title_de, $text_de)
    {
        $this->id = null;
        $this->location_id = $location_id;
        $this->service_id = $service_id; 
        $this->external_id = $external_id;
        $this->starts_at = $starts_at; 
        $this->ends_at = $ends_at;
        $this->notify_earliest_at = $notify_earliest_at;
        $this->notify_latest_at = $notify_latest_at;
        $this->title_en = $title_en;
        $this->text_en = $text_en;
        $this->title_de = $title_de;
        $this->text_de = $text_de;
    }

    //
    public function __get($property) {
        if (property_exists($this, $property)) {
          return $this->$property;
        }
    }
    public function setId(int|null $id){
        $this->id = $id;
    }

    //
    public function getArray(): array
    {
        return [
            "id" => $this->id,
            "location_id" => $this->location_id,
            "service_id" => $this->service_id, 
            "external_id" => $this->external_id,
            "starts_at" => $this->starts_at, 
            "ends_at" => $this->ends_at,
            "notify_earliest_at" => $this->notify_earliest_at,
            "notify_latest_at" => $this->notify_latest_at,
            "title_en" => $this->title_en,
            "text_en" => $this->text_en,
            "title_de" => $this->title_de,
            "text_de" => $this->text_de
        ];
    }

}


?>