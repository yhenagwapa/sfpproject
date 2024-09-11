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
        'created_by_user_id',
    ];

}
