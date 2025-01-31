<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'implementation_id',
        'child_id',
        'attendance_date',
        'attendance_type',
        'created_by_user_id'
    ];
}
