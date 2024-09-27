<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cgs_wfh_boys extends Model
{
    use HasFactory;

    protected $fillable = [
        'length_in_cm',
        'severly_wasted',
        'wasted_from',
        'wasted_to',
        'normal_from',
        'normal_to',
        'overweight_from',
        'overweight_to',
        'obese',
    ];
}
