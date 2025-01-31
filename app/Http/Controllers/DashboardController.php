<?php

namespace App\Http\Controllers;

use App\Models\Implementation;
use Illuminate\Http\Request;
use App\Models\ChildCenter;
use App\Models\Child;
use App\Models\ChildDevelopmentCenter;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {

        $cycle = Implementation::where('status', 'active')->first();

        $ageCounts = [
            '2_years_old' => ['male' => 0, 'female' => 0, 'total' => 0],
            '3_years_old' => ['male' => 0, 'female' => 0, 'total' => 0],
            '4_years_old' => ['male' => 0, 'female' => 0, 'total' => 0],
            '5_years_old' => ['male' => 0, 'female' => 0, 'total' => 0],
        ];

        $profileCounts = [
            'pantawid' => ['male' => 0, 'female' => 0, 'total' => 0],
            'pwd' => ['male' => 0, 'female' => 0, 'total' => 0],
            'ip' => ['male' => 0, 'female' => 0, 'total' => 0],
            'soloparent' => ['male' => 0, 'female' => 0, 'total' => 0],
            'lactoseintolerant' => ['male' => 0, 'female' => 0, 'total' => 0],
        ];

        $allChildren = Child::all();

        $children = ChildCenter::with('child', 'center', 'implementation');

        $totalChildrenQuery = clone $children;
        $totalMaleQuery = clone $children;
        $totalFemaleQuery = clone $children;
        $fundedChildren = clone $children;

        if(auth()->user()->hasRole('admin')){
            $totalChild = $totalChildrenQuery->whereHas('implementation', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })->count();

            $totalMale = $totalMaleQuery->whereHas('child', function ($query) {
                        $query->where('sex_id', 1);
                    })->whereHas('implementation', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })->count();
            $totalFemale = $totalFemaleQuery->whereHas('child', function ($query) {
                        $query->where('sex_id', 2);
                    })->whereHas('implementation', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })->count();



            // $childrenPerAge = $fundedChildren->whereHas('child', function ($query){
            //         $query->whereNotNull('date_of_birth')->where('date_of_birth', '!=', '');
            //     })->get();


            foreach ($allChildren as $child) {
                $getChildrenInCenter = ChildCenter::whereIn('child_id', $child->pluck('id'))
                    ->whereNotNull('implementation_id')->get();



                foreach($getChildrenInCenter as $childCenter) {
                    $males = Child::where('id', $childCenter->child_id)->where('sex_id', 1);
                    $females = Child::where('id', $childCenter->child_id)->where('sex_id', 2);

                    $dob = Carbon::parse($child->date_of_birth);
                    $ageInYears = $dob->diffInYears(Carbon::now());
                }


                // foreach ($getChildrenInCenter as $childInCenter) {

                    if ($ageInYears == 2) {
                        $males ? $ageCounts['2_years_old']['male']++ : $ageCounts['2_years_old']['female']++;
                        $ageCounts['2_years_old']['total']++;
                    } elseif ($ageInYears == 3) {
                        $males ? $ageCounts['3_years_old']['male']++ : $ageCounts['3_years_old']['female']++;
                        $ageCounts['3_years_old']['total']++;
                    } elseif ($ageInYears == 4) {
                        $males ? $ageCounts['4_years_old']['male']++ : $ageCounts['4_years_old']['female']++;
                        $ageCounts['4_years_old']['total']++;
                    } elseif ($ageInYears == 5) {
                        $males ? $ageCounts['5_years_old']['male']++ : $ageCounts['5_years_old']['female']++;
                        $ageCounts['5_years_old']['total']++;
                    }

                    if ($child->is_pantawid == true) {
                        $males ? $profileCounts['pantawid']['male']++ : $profileCounts['pantawid']['female']++;
                        $profileCounts['pantawid']['total']++;
                    } elseif ($child->is_person_with_disability == true) {
                        $males ? $profileCounts['pwd']['male']++ : $profileCounts['pwd']['female']++;
                        $profileCounts['pwd']['total']++;
                    } elseif ($child->is_indigenous_people == true) {
                        $males ? $profileCounts['ip']['male']++ : $profileCounts['ip']['female']++;
                        $profileCounts['ip']['total']++;
                    } elseif ($child->is_child_of_soloparent == true) {
                        $males ? $profileCounts['soloparent']['male']++ : $profileCounts['soloparent']['female']++;
                        $profileCounts['soloparent']['total']++;
                    } elseif ($child->is_lactose_intolerant == true) {
                        $males ? $profileCounts['lactoseintolerant']['male']++ : $profileCounts['lactoseintolerant']['female']++;
                        $profileCounts['lactoseintolerant']['total']++;
                    }
                // }
            }

        } elseif(auth()->user()->hasRole('lgu focal')){
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            $totalChildCenter = ChildCenter::where('is_funded', true)
                ->where('implementation_id', $cycle->id)
                ->whereIn('ChildCenter_development_center_id', $centerIds)
                ->count();

            $totalMale = ChildCenter::with('sex')
                ->where('is_funded', true)
                ->where('sex_id', '1')
                ->where('implementation_id', $cycle->id)
                ->whereIn('ChildCenter_development_center_id', $centerIds)
                ->count();

            $totalFemale = ChildCenter::with('sex')
                ->where('is_funded', true)
                ->where('sex_id', '2')
                ->where('implementation_id', $cycle->id)
                ->whereIn('ChildCenter_development_center_id', $centerIds)
                ->count();
        } elseif(auth()->user()->hasRole('ChildCenter development worker')){
            $workerID = auth()->id();
            $centers = ChildCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            $totalChildCenter = ChildCenter::where('is_funded', true)
                ->where('implementation_id', $cycle->id)
                ->whereIn('ChildCenter_development_center_id', $centerIds)
                ->count();

            $totalMale = ChildCenter::with('sex')
                ->where('is_funded', true)
                ->where('sex_id', '1')
                ->where('implementation_id', $cycle->id)
                ->whereIn('ChildCenter_development_center_id', $centerIds)
                ->count();

            $totalFemale = ChildCenter::with('sex')
                ->where('is_funded', true)
                ->where('sex_id', '2')
                ->where('implementation_id', $cycle->id)
                ->whereIn('ChildCenter_development_center_id', $centerIds)
                ->count();
        }

        return view('dashboard', compact('totalChild', 'ageCounts'));
    }

    // public function getAgeCountsBySex()
    // {
    //     $ChildCenterren = ChildCenter::all(); // Retrieve all ChildCenterren from the database

    //     // Initialize counts for the required age groups, sexes, and total
    //     $ageCounts = [
    //         '2_years_old' => ['male' => 0, 'female' => 0, 'total' => 0],
    //         '3_years_old' => ['male' => 0, 'female' => 0, 'total' => 0],
    //         '4_years_old' => ['male' => 0, 'female' => 0, 'total' => 0],
    //         '5_years_old' => ['male' => 0, 'female' => 0, 'total' => 0],
    //     ];

    //     foreach ($ChildCenterren as $ChildCenter) {
    //         $dob = Carbon::parse($ChildCenter->dob); // Parse the date of birth
    //         $ageInYears = $dob->diffInYears(Carbon::now()); // Calculate age in years

    //         // Increment the appropriate age group and sex count
    //         if ($ageInYears == 2) {
    //             $ChildCenter->sex === 'Male' ? $ageCounts['2_years_old']['male']++ : $ageCounts['2_years_old']['female']++;
    //             $ageCounts['2_years_old']['total']++;
    //         } elseif ($ageInYears == 3) {
    //             $ChildCenter->sex === 'Male' ? $ageCounts['3_years_old']['male']++ : $ageCounts['3_years_old']['female']++;
    //             $ageCounts['3_years_old']['total']++;
    //         } elseif ($ageInYears == 4) {
    //             $ChildCenter->sex === 'Male' ? $ageCounts['4_years_old']['male']++ : $ageCounts['4_years_old']['female']++;
    //             $ageCounts['4_years_old']['total']++;
    //         } elseif ($ageInYears == 5) {
    //             $ChildCenter->sex === 'Male' ? $ageCounts['5_years_old']['male']++ : $ageCounts['5_years_old']['female']++;
    //             $ageCounts['5_years_old']['total']++;
    //         }
    //     }

    //     return view('dashboard', compact('ageCounts'));
    // }
}
