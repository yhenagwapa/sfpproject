<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExitNutritionalStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'weight',
        'height',
        'actual_date_of_weighing',
        'weight_for_age',
        'weight_for_height',
        'height_for_age',
        'created_by_user_id'
    ];
}
