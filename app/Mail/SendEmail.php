<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetCode;
    public $user;

    /**
     * Create a new message instance.
     *
     * @param string $resetCode
     * @param mixed $user
     */
    public function __construct($resetCode, $user)
    {
        $this->resetCode = $resetCode;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Şifre Sıfırlama Kodunuz')
                    ->view('reset')
                    ->with([
                        'resetCode' => $this->resetCode,
                        'user' => $this->user,
                    ]);
    }
}