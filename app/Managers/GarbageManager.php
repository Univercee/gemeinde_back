<?php
namespace App\Managers;
use App\Managers\EventManager;
class GarbageManager
{
  private const SERVICE_ID = 1;
  public const TEMPLATE_ID = 11;

  public static function getNextDateEvents(){
    return app('db') -> select("SELECT * FROM garbage_calendar WHERE date = date(NOW()) + INTERVAL 2 DAY"); //TODO +2 days
  }
  public static function getUsers($frequency){
     return app('db')->select("SELECT location_id, channel, user_id, title, u.language, email, telegram_id
                        FROM user_location_services as uls
                        JOIN user_locations ul ON ul.id = uls.user_location_id
                        JOIN locations l ON l.id = ul.location_id
                        JOIN users u ON u.id = ul.user_id
                        WHERE service_id = :service_id AND frequency = '$frequency'",['service_id' => self::SERVICE_ID]);
  }
  public static function addToEmailQueue($userId, $subj, $body, $template_id, $lang, $email){
    app('db')->insert("INSERT INTO email_queue
                    (user_id, subject, body, template ,lang, email)
                    VALUES($userId, '$subj','$body', $template_id, '$lang', '$email')");

  }
  public static function addToTgQueue($userId, $body, $lang, $tgId){
    app('db')->insert("INSERT INTO telegram_queue
                (user_id, body, lang, telegram_id)
                VALUES ($userId, '$body', '$lang', $tgId)");
  }

  public static function makeBody($type, $date, $lang){
    return trans('garbage.title',[],$lang).': '.trans('garbage.types.'.$type.'.name', [], $lang).' '.trans('garbage.next_day',[], $lang).', '.strftime("%A %e %B %G", strtotime($date)).'. '.trans('garbage.types.'.$type.'.description',[], $lang);
  }
  public static function makeTitle($lang){
    return trans('garbage.title',[], $lang);
  }

  public static function putGarbCalendarInEvents(){
    $gcData = self::getGarbCalendar();
    foreach ($gcData as $key){
      $enTitle = self::makeTitle('en');
      $enBody = self::makeBody($key->type, $key->date, 'en');
      $deTitle = self::makeTitle('de');
      $deBody = self::makeBody($key->type, $key->date, 'de');
      EventManager::addEvent($key->location_id, self::SERVICE_ID, now(),$key->date, $enTitle, $enBody, $deTitle, $deBody);
    }
  }

  public static function getGarbCalendar(){
    return app('db')->select("SELECT location_id, date, type FROM garbage_calendar");
  }
}
