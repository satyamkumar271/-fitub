<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Gym extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gym_name',
        'gym_phone_number',
        'gym_email',
        'gym_website_url',
        'address_city',
        'address_state',
        'address_pincode',
        'total_members'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}