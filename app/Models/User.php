<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ResetPasswordNotification;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Customer;
use App\Models\Trainer;
use App\Models\Gym;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Basic Information
        'name',
        'email',
        'password',
        'user_type',
        'status',
        'id_proof_path',
        'profile_photo_path',
        'gallery_images',
        // Customer Fields
        'age',
        'phone_number',
        'weight',
        'height',
        'goal',
        'customer_city',        // <<< NAYA FIELD
        'customer_state',       // <<< NAYA FIELD


        // Trainer Fields
        'trainer_phone_number',
        'trainer_website_url',
        'specialization',
        'experience',
        'certifications',
        'trainer_city',         // <<< NAYA FIELD
        'trainer_state',        // <<< NAYA FIELD

        // Gym Owner Fields
        'gym_name',             // <<< NAYA FIELD
        'gym_phone_number',
        'gym_email',
        'gym_website_url',
        'address_street',
        'address_city',
        'address_state',
        'address_pincode',
        'gym_age',
        'total_members',

        // Shared Field
        'social_links',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'certifications' => 'array',
        'social_links' => 'array',
        'featured_until' => 'datetime',
        'gallery_images' => 'array',
        'unlock_credits' => 'integer',
    ];

    /**
     * Automatically hash the user's password when setting it.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Send password reset notification
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the payments for the user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class)->orderBy('created_at', 'desc');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class)->orderBy('created_at', 'desc');
    }

    public function customer()
{
    return $this->hasOne(Customer::class);
}

public function trainer()
{
    return $this->hasOne(Trainer::class);
}

public function gym()
{
    return $this->hasOne(Gym::class);
}

public function inquiryMessages()
{
    return $this->hasMany(InquiryMessage::class, 'sender_id');
}

public function sentInquiryReports()
{
    return $this->hasMany(InquiryReport::class, 'reporter_id');
}

public function receivedInquiryReports()
{
    return $this->hasMany(InquiryReport::class, 'reported_user_id');
}

public function unlockCreditLogs()
{
    return $this->hasMany(UnlockCreditLog::class)->latest();
}

public function createdUnlockCreditLogs()
{
    return $this->hasMany(UnlockCreditLog::class, 'created_by')->latest();
}

public function inquiryReadStates()
{
    return $this->hasMany(InquiryReadState::class)->latest();
}

public function supportTickets()
{
    return $this->hasMany(SupportTicket::class)->latest();
}

public function supportTicketMessages()
{
    return $this->hasMany(SupportTicketMessage::class, 'sender_id')->latest();
}

}
