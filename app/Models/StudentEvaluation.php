<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEvaluation extends Model
{
    use HasFactory;
    protected $table = 'student_evaluations';
    protected $fillable = [
        'student_id',
        'supervisor_id',
        'aptitude_id',
        'evaluation',
        'total_mark',
        'global_appreciation',
    ];
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id', 'id');
    }
    public function aptitude()
    {
        return $this->belongsTo(Aptitude::class, 'aptitude_id', 'id');
    }
}
