<?php
namespace App\Managers\Services;

use App\Managers\Events\EventList;
use App\Managers\Events\EventManager;

abstract class ServiceManager{

  protected static $SERVICE_ID;
  protected static $TEMPLATE_ID;
  
  abstract protected static function getEvents(): EventList;

  public function addEvents()
  {
    EventManager::addAll($this->getEvents());
  }
}
?>