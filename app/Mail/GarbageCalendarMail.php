<?php
namespace App\Mail;

use App\Mail\MailjetTemplateMailable;

class GarbageCalendarMail extends MailjetTemplateMailable
{
  protected $templateId = 2732329;
  public $url;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($subject, $body) {
    $this->url = $body;
  }
}