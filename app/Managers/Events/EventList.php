<?php

namespace App\Managers\Events;

class EventList {

    private array $list;

    //
    function __construct(Event ...$events){
        $this->list = $events;
    }

    //
    public function get(): array
    {
        return $this->list;
    }
}

?>