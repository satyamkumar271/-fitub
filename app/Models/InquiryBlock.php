<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_id',
        'blocker_id',
        'blocked_user_id',
        'reason',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    public function blockedUser()
    {
        return $this->belongsTo(User::class, 'blocked_user_id');
    }
}

