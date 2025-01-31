<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_name',
        'password',
        'last_login_at',
    ];

    protected $primaryKey = 'uuid';

    public $timestamps = true;

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) \Str::uuid();
            }
        });
    }
}
