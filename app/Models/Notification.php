<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
        'id',
        'message',
        'user_id',
        'read',
        'created_at',
        'updated_at',
    ];
}
