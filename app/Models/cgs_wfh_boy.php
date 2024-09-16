<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cgs_wfh_boy extends Model
{
    use HasFactory;

    protected $fillable = [
        'age_month',
        'severly_wasted',
        'wasted_from',
        'wasted_to',
        'normal_from',
        'normal_to',
        'overweight_from',
        'overweight_to'
    ];
}
