<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cgs_wfa_girls extends Model
{
    use HasFactory;

    protected $fillable = [
        'age_month',
        'severly_underweight',
        'underweight_from',
        'underweight_to',
        'normal_from',
        'normal_to',
        'overweight',
    ];
}
