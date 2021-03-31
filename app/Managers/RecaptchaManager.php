<?php
namespace App\Managers;

use Illuminate\Support\Facades\Http;

class RecaptchaManager
{
  public static function getScore($token) {
    $score = 0.0;
    if(env('APP_ENV') == 'local') {
      $score = 1.0;
    } else {
      if($token) {
        $response = Http::asForm()
        ->post('https://www.google.com/recaptcha/api/siteverify', [
          'secret' => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
          'response' => $token,
        ])->json();
        if(isset($response['score'])) {
          $score = (float) $response['score'];
        }
      }
    }
    return $score;
  }
}
