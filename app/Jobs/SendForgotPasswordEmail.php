<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendForgotPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $email;
    protected $username;
    protected $forgot_password_code;

    public function __construct($email, $username, $forgot_password_code)
    {
        $this->email = $email;
        $this->username = $username;
        $this->forgot_password_code = $forgot_password_code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = $this->email;
        $username = $this->username;
        $forgot_password_code = $this->forgot_password_code;
        Mail::send('view_send_email_forgotpass', ['username' => $username, 'forgot_password_code' => $forgot_password_code], function ($emailMessage) use ($email) {
            $emailMessage->subject('FoodShare - Forgot password Code');
            $emailMessage->to($email);
        });
    }
}