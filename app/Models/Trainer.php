<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'city',
        'state',
        'website_url',
        'specialization',
        'experience',
        'about',
        'certificate_proof_paths',
        'social_links',
    ];

    protected $casts = [
        'about' => 'string',
        'certificate_proof_paths' => 'array',
        'social_links' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
