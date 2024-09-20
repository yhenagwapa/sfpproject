<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CycleImplementation extends Model
{
    use HasFactory;

    protected $fillable = [
        'cycle_name',
        'cycle_school_year',
        'cycle_target',
        'cycle_allocation',
        'cycle_status',
        'created_by_user_id', 
        'updated_by_user_id'
    ];
}
