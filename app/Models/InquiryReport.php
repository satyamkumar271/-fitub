<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_id',
        'reporter_id',
        'reported_user_id',
        'reason',
        'details',
        'status',
        'compensation_requested',
        'admin_note',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'compensation_requested' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}

