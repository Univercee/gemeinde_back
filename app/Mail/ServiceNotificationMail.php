<?php
namespace App\Mail;

use App\Mail\MailjetTemplateMailable;

class ServiceNotificationMail extends MailjetTemplateMailable
{
  //TODO: Replace with garbage calendar template
  protected $templateId = 2732329;
  public $url; //access to mailjet template params
  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($templateId, $subject, $body) {
    $this->url = $body;
  }
}