<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'age',
        'weight',
        'height',
        'goal',
        'city',
        'state'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}