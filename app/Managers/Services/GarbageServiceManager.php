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
  protected function getValidFrom(array $service_data): string
  {
    $valid_from = new DateTime($service_data["date"]);
    return $valid_from->modify("-1 day")->format("Y-m-d H:i:s");
  }


  //implements
  protected function getValidUntil(array $service_data): string
  {
    return $service_data["date"];
  }


  //implements
  protected function getBody(array $service_data, string $lang): string
  {
    return trans('garbage.title', [], $lang).
          ': '.
          trans('garbage.types.'.$service_data["type"].'.name', [], $lang).
          ' '.
          trans('garbage.next_day', [], $lang).
          ', '.
          strftime("%A %e %B %G", strtotime($service_data["date"])).
          '. '.
          trans('garbage.types.'.$service_data["type"].'.description', [], $lang);
  }


  //implements
  protected function getTitle(array $service_data, string $lang): string
  {
    return trans('garbage.title', [], $lang);
  }

  
  //implements
  protected function getServiceData(): array
  {
    $data = app('db')->select("SELECT location_id, date, type FROM garbage_calendar");
    return json_decode(json_encode($data), true);
  }
}
