<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $table = 'feedbacks';
    protected $guarded = [];
    public function applications()
    {
        return $this->belongsToMany(Application::class, 'feedback_application');
    }
}
