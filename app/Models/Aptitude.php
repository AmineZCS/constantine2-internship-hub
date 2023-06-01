<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aptitude extends Model
{
    use HasFactory;
    protected $table = 'aptitudes';
    protected $fillable = [
        'name',
    ];
    public function studentEvaluations()
    {
        return $this->hasMany(StudentEvaluation::class, 'aptitude_id', 'id');
    }
    

}
