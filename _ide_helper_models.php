<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Admin
 *
 * @property int $id
 * @property string $fname
 * @property string $lname
 * @property string|null $phone_number
 * @property string|null $bio
 * @property string|null $image
 * @property int $department_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereFname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereLname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereUpdatedAt($value)
 */
	class Admin extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Company
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone_number
 * @property string|null $address
 * @property string|null $bio
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedAt($value)
 */
	class Company extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Department
 *
 * @property int $id
 * @property string $name
 * @property string $abbreviation
 * @property int $faculty_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereFacultyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereUpdatedAt($value)
 */
	class Department extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Faculty
 *
 * @property int $id
 * @property string $name
 * @property string $abbreviation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty query()
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faculty whereUpdatedAt($value)
 */
	class Faculty extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Internship extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Notification
 *
 * @property int $id
 * @property string $message
 * @property int $read
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUserId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Student extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Supervisor extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

