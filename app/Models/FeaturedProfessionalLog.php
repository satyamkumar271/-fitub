<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedProfessionalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'action',
        'source',
        'featured_until_before',
        'featured_until_after',
        'note',
    ];

    protected $casts = [
        'featured_until_before' => 'datetime',
        'featured_until_after' => 'datetime',
    ];

    public function professional()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
