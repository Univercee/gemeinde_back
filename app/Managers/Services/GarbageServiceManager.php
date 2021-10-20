<?php
namespace App\Managers\Services;

use App\Managers\Events\Event;
use App\Managers\Events\EventList;
use App\Managers\Services\ServiceManager;
use DateTime;



class GarbageServiceManager extends ServiceManager
{
  const SERVICE_ID = 1;
  const TEMPLATE_ID = 11;

  function __construct()
  {
    parent::__construct(self::SERVICE_ID, self::TEMPLATE_ID);
  }

  //implements
  public function getEvents(): EventList
  {
    $events = array();
    foreach($this->getServiceData() as $service_data){
      $event = new Event(
          $this->getLocationId($service_data),
          $this->SERVICE_ID,
          $this->getExternalId($service_data),
          $this->getStartsAt($service_data),
          $this->getEndsAt($service_data),
          $this->getNotifyEarliestAt($service_data),
          $this->getNotifyLatestAt($service_data),
          $this->getTitle($service_data, 'en'),
          $this->getBody($service_data, 'en'),
          $this->getTitle($service_data, 'de'),
          $this->getBody($service_data, 'de')
      );
      array_push($events, $event);
    }
    return new EventList(...$events);
  }

  //
  private function getLocationId(array $service_data): int
  {
    return $service_data["location_id"];
  }

  //
  //garbage calendar event valid from 1 day before it expires
  //TODO: it must be changed for weekly events
  private function getStartsAt(array $service_data): string
  {
     $dt = new DateTime($service_data["date"]);
     return $dt->modify("midnight")->format("Y-m-d H:i:s");
  }

  //
  private function getEndsAt(array $service_data): string
  {
    $dt =  new DateTime($service_data["date"]);
    return $dt->modify("tomorrow -1 second")->format("Y-m-d H:i:s");
  }

  //
  private function getNotifyEarliestAt(array $service_data): string
  {
    $dt = new DateTime($service_data["date"]);
    return $dt->modify("-1 day midnight")->format("Y-m-d H:i:s");
  }

  //
  private function getNotifyLatestAt(array $service_data): string
  {
    $dt = new DateTime($service_data["date"]);
    return $dt->modify("noon -5 hours")->format("Y-m-d H:i:s");
  }

  //
  private function getBody(array $service_data, string $lang): string
  {
    return trans('garbage.types.'.$service_data["type"].'.name', [], $lang).' '.
          strftime("%A %e %B %G", strtotime($service_data["date"])).'. '.
          trans('garbage.types.'.$service_data["type"].'.description', [], $lang);
  }

  //
  private function getTitle(array $service_data, string $lang): string
  {
    return trans('garbage.title', [], $lang);
  }

  //
  private function getExternalId(array $service_data): int
  {
    return $service_data["id"];
  }
  
  //
  private function getServiceData(): array
  {
    $data = app('db')->select("SELECT id, location_id, date, type FROM garbage_calendar");
    return json_decode(json_encode($data), true);
  }
}
