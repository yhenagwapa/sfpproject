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
            "Psychosocial Disability",
            "Rare Disease",
            "Speech Impairment",
            "Visual Impairment",
            "Others",
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
        $middleInitial = $this->middlename ? strtoupper(substr($this->middlename, 0, 1)) . '.' : '';
        $extension = $this->extension_name ?? '';

        return trim("{$this->lastname}, {$this->firstname} {$middleInitial} {$extension}");
    }

    public function psgc()
    {
        return $this->belongsTo(Psgc::class, 'psgc_id', 'psgc_id');
    }
    /**
     * All center‐records for this child.
     */
    public function childCenters()
    {
        return $this->hasMany(ChildCenter::class, 'child_id', 'id');
    }
    /**
     * All history for this child.
     */
    public function histories()
    {
        return $this->hasMany(ChildHistory::class);
    }
    // /**
    //  * latest child history
    //  */
    // public function latestHistory()
    // {
    //     return $this->hasOne(ChildHistory::class)->latestOfMany();
    // }

    /**
     * Quick “funded” lookup as “Yes” or “No”.
     */
    public function getFundedAttribute(): string
    {
        // if any related record has funded = true
        $isFunded = $this->childCenters()
            ->where('funded', true)
            ->exists();

        return $isFunded ? 'Yes' : 'No';
    }

    public function calculateAgeAt($date)
    {
        $dob = Carbon::parse($this->date_of_birth);

        $ageInMonths = $dob->diffInMonths($date);
        $ageInYears = floor($ageInMonths / 12);

        return [
            'years' => $ageInYears,
            'months' => $ageInMonths,
        ];
    }
}
