<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_no',
        'child_id',
        'attendance_date',
        'attendance_type',
        'created_by_user_id'
    ];
}
