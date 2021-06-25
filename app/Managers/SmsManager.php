<?php


namespace App\Managers;


use Illuminate\Support\Facades\Http;

class SmsManager
{
  public static function sendSMS($message, $to)
  {
     Http::withHeaders(['Authorization'=> 'Bearer '.env("MAILJETSMS_TOKEN"),'Content-Type'=>'application/json'])->post(
      'https://api.mailjet.com/v4/sms-send',[
      'Text' => $message,
      'To' => '+37258307282',
      'From' => env("MAILJETSMS_FROM"),
    ]);
  }
}
