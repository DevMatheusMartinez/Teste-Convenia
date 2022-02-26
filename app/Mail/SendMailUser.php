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
    private array $importReport;
    private array $errorsReport;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(object $user, array $importReport, array $errorsReport )
    {
        $this->user = $user;
        $this->importReport = $importReport;
        $this->errorsReport = $errorsReport;
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
            'importReport' => $this->importReport,
            'errorsReport' => $this->errorsReport
        ]);
    }
}
