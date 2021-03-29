<?php
namespace App\Mail;

use App\Mail\MailjetTemplateMailable;

class UserRegistrationMail extends MailjetTemplateMailable
{
  protected $templateId = 2716228;
  public $url;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($key) {
    $this->url = env('APP_URL').'/api/auth/email/verify/'.$key; // {{var:url}} in MJ $templateId
  }
}
