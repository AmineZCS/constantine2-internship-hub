<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackApplication extends Model
{
    use HasFactory;
    protected $table = 'feedback_application';
    protected $guarded = [];
    
}
