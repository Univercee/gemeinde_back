<?php
namespace App\Managers\Services;

use App\Managers\Events\Event;
use App\Managers\Events\EventList;
use App\Managers\Services\ServiceManager;
use DateTime;

class GarbageServiceManager extends ServiceManager
{
  protected static $SERVICE_ID = 1;
  protected static $TEMPLATE_ID = 11;


  //implements
  public static function getEvents(): EventList
  {
    $events = array();
    foreach(self::getServiceData() as $service_data){
      $event = new Event(
          self::getLocationId($service_data),
          self::$SERVICE_ID,
          self::getExternalId($service_data),
          self::getStartsAt($service_data),
          self::getEndsAt($service_data),
          self::getNotifyEarliestAt($service_data),
          self::getNotifyLatestAt($service_data),
          self::getTitle($service_data, 'en'),
          self::getBody($service_data, 'en'),
          self::getTitle($service_data, 'de'),
          self::getBody($service_data, 'de')
      );
      array_push($events, $event);
    }
    return new EventList(...$events);
  }

  //
  private static function getLocationId(array $service_data): int
  {
    return $service_data["location_id"];
  }

  //
  //garbage calendar event valid from 1 day before it expires
  //TODO: it must be changed for weekly events
  private static function getStartsAt(array $service_data): string
  {
     $dt = new DateTime($service_data["date"]);
     return $dt->modify("midnight")->format("Y-m-d H:i:s");
  }

  //
  private static function getEndsAt(array $service_data): string
  {
    $dt =  new DateTime($service_data["date"]);
    return $dt->modify("tomorrow -1 second")->format("Y-m-d H:i:s");
  }

  //
  private static function getNotifyEarliestAt(array $service_data): string
  {
    $dt = new DateTime($service_data["date"]);
    return $dt->modify("-1 day midnight")->format("Y-m-d H:i:s");
  }

  //
  private static function getNotifyLatestAt(array $service_data): string
  {
    $dt = new DateTime($service_data["date"]);
    return $dt->modify("noon -5 hours")->format("Y-m-d H:i:s");
  }

  //
  private static function getBody(array $service_data, string $lang): string
  {
    return trans('garbage.types.'.$service_data["type"].'.name', [], $lang).' '.
          strftime("%A %e %B %G", strtotime($service_data["date"])).'. '.
          trans('garbage.types.'.$service_data["type"].'.description', [], $lang);
  }

  //
  private static function getTitle(array $service_data, string $lang): string
  {
    return trans('garbage.title', [], $lang);
  }

  //
  private static function getExternalId(array $service_data): int
  {
    return $service_data["id"];
  }
  
  //
  private static function getServiceData(): array
  {
    $data = app('db')->select("SELECT id, location_id, date, type FROM garbage_calendar");
    return json_decode(json_encode($data), true);
  }
}
