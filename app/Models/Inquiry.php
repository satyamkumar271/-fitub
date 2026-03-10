<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Yeh Laravel ko batata hai ki in columns mein data bhara ja sakta hai.
     * Iske bina Model::create() kaam nahi karega.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'recipient_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'service_needed',
        'message',
        'status',
    ];

    /**
     * Inquiry bhejnewale user ka relationship (agar logged in hai).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Jiske liye inquiry aayi hai (trainer/gym) uska relationship.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
