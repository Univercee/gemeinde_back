<?php
namespace App\Managers;

use Illuminate\Support\Facades\Http;

class RecaptchaManager
{
  public static function getScore($token) {
    $response = Http::asForm()
      ->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
        'response' => $token,
      ])->json();
    return (float) $response['score'];
  }
}
