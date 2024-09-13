<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cgs_hfa_boys extends Model
{
    use HasFactory;

    protected $fillable = [
        'age_month',
        'severly_stunted',
        'stunted_from',
        'stunted_to',
        'normal_from',
        'normal_to',
        'tall'
    ];
}
