<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ChildRecord extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'child_id',
        'implementation_id',
        'action_type',
        'action_date',
        'center_from',
        'center_to',
        'funded',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    protected $casts = [
        'funded' => 'boolean',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    public function child()
    {
        return $this->belongsTo(Child::class, 'child_id', 'id');
    }

    public function centerFrom()
    {
        return $this->belongsTo(ChildDevelopmentCenter::class, 'center_from', 'id');
    }

    public function centerTo()
    {
        return $this->belongsTo(ChildDevelopmentCenter::class, 'center_to', 'id');
    }

    public function implementation()
    {
        return $this->belongsTo(Implementation::class, 'implementation_id', 'id');
    }
}
