<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

 
class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email;
    protected $full_name;
    protected $verificationCode;
    public function __construct($email, $full_name, $verificationCode)
    {
        $this->email = $email;
        $this->full_name = $full_name;
        $this->verificationCode = $verificationCode;
    }
    public function handle()
    {
        $email = $this->email;
        $full_name = $this->full_name;
        $verificationCode = $this->verificationCode;
        Mail::send('view_send_email', ['full_name' => $full_name, 'verificationCode' => $verificationCode], function ($emailMessage) use ($email) {
            $emailMessage->subject('FoodShare - Verifi Code');
            $emailMessage->to($email);
        });
    }
}