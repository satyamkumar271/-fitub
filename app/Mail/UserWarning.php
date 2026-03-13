<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserWarning extends Mailable
{
    use Queueable, SerializesModels;

    protected $warningUser;
    protected $warningMessage;

    public function __construct(User $user, $message)
    {
        $this->warningUser = $user;
        $this->warningMessage = $message;
    }

    public function build()
    {
        return $this->subject('⚠️ Warning Notice - Fitub Platform')
                    ->view('emails.warning', [
                        'user' => $this->warningUser,
                        'warningMessage' => $this->warningMessage,
                    ]);
    }
}