<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Supervisor
 *
 * @property int $id
 * @property string $fname
 * @property string $lname
 * @property string|null $remember_token
 * @property string|null $phone_number
 * @property string|null $bio
 * @property string|null $image
 * @property int $company_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor whereFname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor whereLname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supervisor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Supervisor extends Model
{
    use HasFactory;
    protected $table = 'supervisors';
    protected $fillable = [
        'id',
        'fname',
        'lname',
        'company_id',
        'phone_number',
    ];
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    public function internships()
    {
        return $this->hasMany(Internship::class, 'supervisor_id', 'id');
    }
    public function applications()
    {
        return $this->hasManyThrough(Application::class, Internship::class, 'supervisor_id', 'internship_id', 'id', 'id');
    }
    // user 
    public function user()
    {
        return $this->belongsTo(User::class,'id','id');
    }

}
