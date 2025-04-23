<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Implementation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'school_year_from',
        'school_year_to',
        'target',
        'allocation',
        'type',
        'status',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
}
