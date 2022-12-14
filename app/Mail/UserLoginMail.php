<?php
namespace App\Mail;

use App\Mail\MailjetTemplateMailable;

class UserLoginMail extends MailjetTemplateMailable
{
  protected $templateId = 2732329;
  public $url;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($key) {
    $this->url = env('APP_URL').'/#/verify/'.$key; // {{var:url}} in MJ $templateId // {{var:url}} in MJ $templateId
  }
}
