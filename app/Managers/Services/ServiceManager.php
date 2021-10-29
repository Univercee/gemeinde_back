<?php
namespace App\Managers\Services;

use App\Managers\Events\EventList;
use App\Managers\Events\EventManager;
use Illuminate\Support\Facades\Log;

abstract class ServiceManager{

  protected int $SERVICE_ID;
  protected int $TEMPLATE_ID;
  
  abstract protected function getEvents(): EventList;
  abstract protected function beforeAdd(EventList $event_list): EventList;

  //
  public function addEvents()
  {
    $events = $this->getEvents();
    $events = $this->beforeAdd($events);
    return EventManager::addAll($events);
  }

  //
  public function getServiceId(): int
  {
    return $this->SERVICE_ID;
  }

  //
  public function getTemplateId(): int
  {
    return $this->TEMPLATE_ID;
  }
}
?>