<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ChildHistory extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'child_id',
        'implementation_id',
        'action_type',
        'action_date',
        'center_from',
        'center_to',
        'created_by_user_id',
        'updated_by_user_id',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    public function child()
    {
        return $this->belongsTo(Child::class);
    }
    public function center()
    {
        return $this->belongsTo(ChildDevelopmentCenter::class);
    }
    public function implementation()
    {
        return $this->belongsTo(Implementation::class);
    }
}
