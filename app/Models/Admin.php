<?php
// scan if there's any error in the code


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    protected $table = 'admins';
    protected $fillable = [
        'fname',
        'lname',
        'department_id',
        'phone_number',
    ];
}
