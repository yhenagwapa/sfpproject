<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'implementation_id',
        'attendance_date',
        'created_by_user_id',
        'updated_by_user_id',
    ];
}
