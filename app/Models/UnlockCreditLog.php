<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnlockCreditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'delta',
        'balance_after',
        'source_type',
        'source_id',
        'note',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
