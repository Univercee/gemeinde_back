<?php
namespace App\Mail;

use App\Mail\MailjetTemplateMailable;

class UserWelcomeMail extends MailjetTemplateMailable
{
  protected $templateId = 2732471;
  public $url;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($key) {
    $this->url = env('APP_URL').'/signup/#'.$key; // {{var:url}} in MJ $templateId
  }
}
