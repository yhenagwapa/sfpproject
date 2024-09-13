<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildDevelopmentCenter extends Model
{
    use HasFactory;
    
    
    protected $fillable = [
        'center_name',
        'psgc_id',
        'address',
        'zip_code',
        'assigned_user_id',
        'created_by_user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    // public function region()
    // {
    //     return $this->belongsTo(PSGC::class, 'region_psgc', 'psgc_id');
    // }

    public function province()
    {
        return $this->belongsTo(PSGC::class, 'province_psgc', 'psgc_id');
    }

    public function city()
    {
        return $this->belongsTo(PSGC::class, 'city_name_psgc', 'psgc_id');
    }

    public function barangay()
    {
        return $this->belongsTo(PSGC::class, 'brgy_psgc', 'psgc_id');
    }

    public function children()
    {
        return $this->hasMany(Child::class, 'child_development_center_id');
    }

}
