<?php
namespace App\Managers\Services;
use App\Managers\EventManager;

abstract class ServiceManager{

  protected $SERVICE_ID;
  protected $TEMPLATE_ID;
  
  abstract protected function getLocationId(array $service_data): int;
  abstract protected function getStartsAt(array $service_data): string;
  abstract protected function getEndsAt(array $service_data): string;
  abstract protected function getNotifyEarliestAt(array $service_data): string;
  abstract protected function getNotifyLatestAt(array $service_data): string;
  abstract protected function getBody(array $service_data, string $lang): string;
  abstract protected function getTitle(array $service_data, string $lang): string;
  abstract protected function getExternalId(array $service_data): int;
  abstract protected function getServiceData(): array;

  public function addEvents()
  {
    foreach($this->getServiceData() as $service_data){
        EventManager::addEvent(
            $this->getLocationId($service_data),
            $this->SERVICE_ID,
            $this->getStartsAt($service_data),
            $this->getEndsAt($service_data),
            $this->getNotifyEarliestAt($service_data),
            $this->getNotifyLatestAt($service_data),
            $this->getTitle($service_data, 'en'),
            $this->getBody($service_data, 'en'),
            $this->getTitle($service_data, 'de'),
            $this->getBody($service_data, 'de'),
            $this->getExternalId($service_data)
        );
    }
  }
}
?>