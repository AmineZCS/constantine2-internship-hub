<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;
    protected $table = 'evaluations';
    protected $guarded = [];
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
