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

    public function psgc()
    {
        return $this->belongsTo(PSGC::class, 'psgc_id', 'psgc_id');
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
