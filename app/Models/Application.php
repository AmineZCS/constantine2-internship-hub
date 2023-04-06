<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    protected $table = 'applications';
    protected $guarded = [];
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
    public function internship()
    {
        return $this->belongsTo(Internship::class, 'internship_id', 'id');
    }
}
