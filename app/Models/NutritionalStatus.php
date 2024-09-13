<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutritionalStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'entry_weight',
        'entry_height',
        'entry_actual_date_of_weighing',
        'entry_weight_for_age',
        'entry_weight_for_height',
        'entry_height_for_age',
        'exit_weight',
        'exit_height',
        'exit_actual_date_of_weighing',
        'exit_weight_for_age',
        'exit_weight_for_height',
        'exit_height_for_age',
        'updated_by_user_id',
        'created_by_user_id'
    ];
}
