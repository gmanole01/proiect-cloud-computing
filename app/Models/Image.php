<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = [
        'id',
        'user_id',
        'url',
        'face_url'
    ];
}
