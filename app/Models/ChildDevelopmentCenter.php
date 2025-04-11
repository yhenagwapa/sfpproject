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
        'address',
        'psgc_id',
        'created_by_user_id',
        'updated_by_user_id',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function psgc()
    {
        return $this->belongsTo(Psgc::class, 'psgc_id', 'psgc_id');
    }

    public function getFullAddress()
    {
        return "{$this->address}, {$this->psgc->getBrgyCityProvince()}";
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_centers', 'child_development_center_id', 'user_id')->withTimestamps();
    }



}
