<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $key;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sbj)
    {
        $path = getenv('APP_ROOT');
        $this->subject = 'Confirm login/signup';
        $this->key = $path.'/api/auth/email/verify/'.$sbj['key'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.welcome');
    }
}