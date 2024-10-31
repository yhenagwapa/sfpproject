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
        'cycle_implementation_id',
        'milk_feeding_id',
        'firstname',
        'middlename',
        'lastname',
        'extension_name',
        'date_of_birth',
        'sex_id',
        'address',
        'psgc_id',
        'zip_code',
        'cdc_id',
        'is_pantawid', 
        'pantawid_details', 
        'is_person_with_disability', 
        'person_with_disability_details', 
        'is_indigenous_people', 
        'is_child_of_soloparent', 
        'is_lactose_intolerant', 
        'deworming_date', 
        'vitamin_a_date', 
        'is_funded',
        'child_development_center_id', 
        'created_by_user_id', 
        'updated_by_user_id'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function nutritionalStatus()
    {
        return $this->hasMany(NutritionalStatus::class);
    }

    public function sex()
    {
        return $this->belongsTo(Sex::class);
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->lastname}, {$this->firstname} {$this->middlename} {$this->extension_name}");
    }
    public function center()
    {
        return $this->belongsTo(ChildDevelopmentCenter::class, 'child_development_center_id', 'id');
    }
    
    public function location()
    {
        return $this->belongsTo(Psgc::class, 'psgc_id', 'psgc_id');
    }
    public function cycleImplementation()
    {
        return $this->belongsTo(CycleImplementation::class);
    }

    public function getAgeAtWeighingAttribute()
    {
        if ($this->dob && $this->nutritionalStatus->entry_actual_date_of_weighing) {
            return Carbon::parse($this->date_of_birth)->diffInYears(Carbon::parse($this->nutritionalStatus->entry_actual_date_of_weighing));
        }
        return null; 
    }

    
        
}
