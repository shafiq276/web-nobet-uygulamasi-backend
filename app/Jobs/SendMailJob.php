<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;



class SendMailJob implements ShouldQueue
{
    use Queueable;

    private $mail;
    private $resetCode;
    /**
     * Create a new job instance.
     */
    public function __construct($mail, $resetCode)
    {
        $this->mail = $mail;
        $this->resetCode = $resetCode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->mail)->send(new sendEmail($resetCode, $user));
        
    }
}
