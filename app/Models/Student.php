<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Student
 *
 * @property int $id
 * @property string $fname
 * @property string $lname
 * @property int $department_id
 * @property string|null $bio
 * @property string|null $cv_path
 * @property string|null $photo_path
 * @property string|null $phone_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCvPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereFname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereLname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student wherePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Student extends Model
{
    use HasFactory;
    protected $table = 'students';
    protected $fillable = [

        'fname',
        'lname',
        'department_id',
        'phone_number',
    ];
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function applications()
    {
        return $this->hasMany(Application::class, 'student_id', 'id');
    }
    public function internships()
    {
        return $this->hasManyThrough(Internship::class, Application::class, 'student_id', 'id', 'id', 'internship_id');
    }
    
}
