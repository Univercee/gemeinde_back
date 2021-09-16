<?php
namespace App\Managers\Services;
use App\Managers\Services\ServiceManager;
use DateTime;

class GarbageServiceManager extends ServiceManager
{
  protected $SERVICE_ID = 1;
  protected $TEMPLATE_ID = 11;

  //implements
  protected function getLocationId(array $service_data): int
  {
    return $service_data["location_id"];
  }


  //implements
  //garbage calendar event valid from 1 day before it expires
  //TODO: it must be changed for weekly events
  protected function getStartsAt(array $service_data): string
  {
     $dt = new DateTime($service_data["date"]);
     return $dt->modify("midnight")->format("Y-m-d H:i:s");
  }

  //implements
  protected function getEndsAt(array $service_data): string
  {
    $dt =  new DateTime($service_data["date"]);
    return $dt->modify("tomorrow -1 second")->format("Y-m-d H:i:s");
  }

  //implements
  protected function getNotifyEarliestAt(array $service_data): string
  {
    $dt = new DateTime($service_data["date"]);
    return $dt->modify("-1 day midnight")->format("Y-m-d H:i:s");
  }

  //implements
  protected function getNotifyLatestAt(array $service_data): string
  {
    $dt = new DateTime($service_data["date"]);
    return $dt->modify("noon -5 hours")->format("Y-m-d H:i:s");
  }

  //implements
  protected function getBody(array $service_data, string $lang): string
  {
    return trans('garbage.types.'.$service_data["type"].'.name', [], $lang).' '.
          strftime("%A %e %B %G", strtotime($service_data["date"])).'. '.
          trans('garbage.types.'.$service_data["type"].'.description', [], $lang);
  }


  //implements
  protected function getTitle(array $service_data, string $lang): string
  {
    return trans('garbage.title', [], $lang);
  }

  //implements
  protected function getExternalId(array $service_data): int
  {
    return $service_data["id"];
  }
  
  //implements
  protected function getServiceData(): array
  {
    $data = app('db')->select("SELECT id, location_id, date, type FROM garbage_calendar");
    return json_decode(json_encode($data), true);
  }
}
