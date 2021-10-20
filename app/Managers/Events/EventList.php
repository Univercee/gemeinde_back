<?php

namespace App\Managers\Events;

class EventList {

    private array $event_list;
    private array $array_list;

    //
    function __construct(Event ...$events){
        $this->event_list = $events;

        $this->array_list = array();
        foreach($events as $event){
            array_push($this->array_list, $event->getArray());
        }
    }

    //
    public function get(): array
    {
        return $this->event_list;
    }

    //
    public function getArray(): array
    {
        return $this->array_list;
    }
}

?>