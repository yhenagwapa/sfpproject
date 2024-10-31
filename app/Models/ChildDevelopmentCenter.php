<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ChildDevelopmentCenter extends Model
{
    use HasFactory, LogsActivity;
    
    
    protected $fillable = [
        'center_name',
        'psgc_id',
        'address',
        'zip_code',
        'assigned_focal_user_id',
        'assigned_worker_user_id',
        'created_by_user_id',
        'updated_by_user_id',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_worker_user_id');
    }

    public function focal()
    {
        return $this->belongsTo(User::class, 'assigned_focal_user_id');
    }

    public function psgc()
    {
        return $this->belongsTo(Psgc::class, 'psgc_id', 'psgc_id');
    }

    public function children()
    {
        return $this->hasMany(Child::class, 'child_development_center_id');
    }

    public function getFullAddress()
    {
        return "{$this->address}, {$this->psgc->getBrgyCityProvince()}, {$this->zip_code}";
    }

    

}
