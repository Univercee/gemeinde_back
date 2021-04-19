<?php
namespace App\Mail;

use App\Mail\MailjetTemplateMailable;

class GarbageCalendarMail extends MailjetTemplateMailable
{
  //TODO: Replace with garbage calendar template

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($templateId, $subject, $body) {
    $this->templateId = $templateId;
    //$this->html($body);
    //$this->subject($subject);
  }
}