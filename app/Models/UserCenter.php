<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'child_development_center_id',
    ];

    public function users()
{
    return $this->belongsToMany(User::class, 'user_centers', 'child_development_center_id', 'user_id');
}


    public function center()
    {
        return $this->belongsTo(ChildDevelopmentCenter::class, 'child_development_center_id');
    }
}
