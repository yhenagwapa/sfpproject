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
        'implementation_id',
        'child_id',
        'weight',
        'height',
        'actual_weighing_date',
        'age_in_months',
        'age_in_years',
        'weight_for_age',
        'weight_for_height',
        'height_for_age',
        'is_malnourish',
        'is_undernourish',
        'deworming_date',
        'vitamin_a_date',
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

    public function implementation()
    {
        return $this->belongsTo(Implementation::class);
    }
}
