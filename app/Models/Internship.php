<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Internship
 *
 * @property int $id
 * @property int $department_id
 * @property int $company_id
 * @property int $supervisor_id
 * @property string $position
 * @property string|null $description
 * @property string $location
 * @property string $status
 * @property string|null $requirements
 * @property string $start_date
 * @property string $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Internship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Internship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Internship query()
 * @method static \Illuminate\Database\Eloquent\Builder|Internship whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Internship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Internship whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Internship whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Internship whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Internship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Internship whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Internship wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Internship whereRequirements($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Internship whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Internship whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Internship whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Internship whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Internship extends Model
{
    use HasFactory;
    protected $table = 'internships';
    protected $guarded = [];
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'internship_department', 'internship_id', 'department_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id', 'id');
    }

}
