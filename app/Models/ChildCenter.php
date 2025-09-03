<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildCenter extends Model
{
    use HasFactory;

    protected $table = 'child_records';

    protected $fillable = [
        'child_id',
        'child_development_center_id',
        'implementation_id',
        'action_type',
        'action_date',
        'funded',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    protected $casts = [
        'funded' => 'boolean',
    ];


    public function child()
    {
        return $this->belongsTo(Child::class, 'child_id', 'id');
    }

    public function center()
    {
        return $this->belongsTo(ChildDevelopmentCenter::class, 'child_development_center_id', 'id');
    }

    public function implementation()
    {
        return $this->belongsTo(Implementation::class, 'implementation_id', 'id');
    }
}
