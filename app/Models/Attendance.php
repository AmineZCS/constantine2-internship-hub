<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendance';
    // relationship between attendance and student
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
    
}
