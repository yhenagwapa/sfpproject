<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'feeding_no',
        'child_id',
        'feeding_date',
        'with_milk',
        'created_by_user_id'
    ];
}
