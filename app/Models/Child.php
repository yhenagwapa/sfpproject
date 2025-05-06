<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Child extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'children';

    protected $fillable = [
        'lastname',
        'firstname',
        'middlename',
        'extension_name',
        'date_of_birth',
        'sex_id',
        'address',
        'psgc_id',
        'pantawid_details',
        'person_with_disability_details',
        'is_indigenous_people',
        'is_child_of_soloparent',
        'is_lactose_intolerant',
        'edit_counter',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public static function disabilityOptions(): array
    {
        return [
            "Autism Spectrum Disorder",
            "Chronic Illness",
            "Hearing Impairment",
            "Intellectual Disability",
            "Learning Disability",
            "Mental Disorder",
            "Multiple Disabilities",
            "Orthopedic Disability",
            "Others",
            "Psychosocial Disability",
            "Rare Disease",
            "Speech Impairment",
            "Visual Impairment",
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function nutritionalStatus()
    {
        return $this->hasMany(NutritionalStatus::class);
    }
    public function records()
    {
        return $this->hasMany(ChildCenter::class);
    }

    public function sex()
    {
        return $this->belongsTo(Sex::class);
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->lastname}, {$this->firstname} {$this->middlename} {$this->extension_name}");
    }

    public function psgc()
    {
        return $this->belongsTo(Psgc::class, 'psgc_id', 'psgc_id');
    }

    public function getAgeAtWeighingAttribute()
    {
        if ($this->dob && $this->nutritionalStatus->entry_actual_date_of_weighing) {
            return Carbon::parse($this->date_of_birth)->diffInYears(Carbon::parse($this->nutritionalStatus->entry_actual_date_of_weighing));
        }
        return null;
    }



}
