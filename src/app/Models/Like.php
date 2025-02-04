<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table = 'likes';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['uuid', 'user_uuid', 'post_uuid'];

    public $timestamps = true;
}