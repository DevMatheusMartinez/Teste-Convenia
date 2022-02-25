<?php

namespace App\Mail;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailUser extends Mailable
{
    use Queueable, SerializesModels;

    private object $user;
    private int $numberRegistered;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(object $user, int $numberRegistered)
    {
        $this->user = $user;
        $this->numberRegistered = $numberRegistered;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->to($this->user->email, $this->user->name);
        return $this->view('mail.send-email-user',
        [
            'user' => $this->user,
            'numberRegistered' => $this->numberRegistered,
            'dateRegistration' => date("d/m/Y")
        ]);
    }
}
