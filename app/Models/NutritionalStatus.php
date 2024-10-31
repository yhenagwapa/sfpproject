<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class NutritionalStatus extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'cycle_implementation_id',
        'milk_feeding_id',
        'child_id',
        'weight',
        'height',
        'weighing_date',
        'age_in_months',
        'age_in_years',
        'weight_for_age',
        'weight_for_height',
        'height_for_age',
        'is_malnourish',
        'is_undernourish',
        'updated_by_user_id',
        'created_by_user_id'
    ];

    protected $casts = [
        'is_malnourish' => 'boolean',
        'is_undernourish' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
}
