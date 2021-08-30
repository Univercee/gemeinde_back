<?php
namespace App\Managers;
class GarbageManager
{
  private const SERVICE_ID = 1;
  public const TEMPLATE_ID = 11;

  public static function makeTitle($lang){
    setlocale(LC_ALL, $lang);
    return __('garbage.title');
  }
  public static function makeBody($type, $gDate, $lang)
  {
    setlocale(LC_ALL, $lang);
    app('translator')->setLocale($lang);
    return __('garbage.title').': '.__('garbage.types.'.$type.'.name').' '.__('garbage.next_day').', '.strftime("%A %e %B %G", strtotime($gDate)).'. '.__('garbage.types.'.$type.'.description');
  }
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
}
