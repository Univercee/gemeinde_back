<?php
namespace App\Managers\Services;

use App\Managers\Events\EventList;
use App\Managers\Events\EventManager;

abstract class ServiceManager{

  protected int $SERVICE_ID;
  protected int $TEMPLATE_ID;
  
  abstract protected function getEvents(): EventList;

  //
  function __construct(int $service_id, int $template_id)
  {
    $this->SERVICE_ID = $service_id;
    $this->TEMPLATE_ID = $template_id;
  }

  //
  public function addEvents()
  {
    return EventManager::addAll($this->getEvents());
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