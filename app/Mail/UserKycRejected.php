<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserKycRejected extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $reason;
    public string $registerUrl;

    public function __construct(User $user, string $reason, string $registerUrl)
    {
        $this->user = $user;
        $this->reason = $reason;
        $this->registerUrl = $registerUrl;
    }

    public function build()
    {
        return $this->subject('Fitub Account Verification Rejected')
            ->view('emails.kyc-rejected');
    }
}
