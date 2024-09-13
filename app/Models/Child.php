<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    protected $table = 'children';

    protected $fillable = [
        'firstname',
        'middlename',
        'lastname',
        'extension_name',
        'date_of_birth',
        'sex',
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
        'created_by_user_id', 
        'updated_by_user_id'];

    public function nutritionalStatus()
    {
        return $this->hasOne(EntryNutritionalStatus::class, 'child_id');
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->firstname} {$this->middlename} {$this->lastname}");
    }
    public function center()
    {
        return $this->belongsTo(ChildDevelopmentCenter::class, 'child_development_center_id');
    }
}
