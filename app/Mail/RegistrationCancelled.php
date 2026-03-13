<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationCancelled extends Mailable
{
    use Queueable, SerializesModels;

    protected $cancelledUser;
    protected $cancellationReason;

    public function __construct(User $user, $reason)
    {
        $this->cancelledUser = $user;
        $this->cancellationReason = $reason;
    }

    public function build()
    {
        return $this->subject('❌ Registration Cancelled - Fitub Platform')
                    ->view('emails.registration-cancelled', [
                        'user' => $this->cancelledUser,
                        'reason' => $this->cancellationReason,
                    ]);
    }
}
