<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Certificate extends Model
{
    use HasFactory;
    protected $table = 'certificates';
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($certificate) {
            $certificate->token = Str::random(32);
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }
}
