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
        'business_doc_path',
        'address_street',
        'address_city',
        'address_state',
        'address_pincode',
        'gym_age',
        'total_members',
        'social_links',
        'allow_visit_booking',
        'lead_services_note',
    ];

    protected $casts = [
        'allow_visit_booking' => 'boolean',
        'social_links' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
