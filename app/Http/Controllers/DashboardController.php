<?php

namespace App\Http\Controllers;

use App\Models\Implementation;
use App\Models\Psgc;
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

        $provinceCounts = [
            'davao_city' => ['served' => 0],
            'davao_del_norte' => ['served' => 0],
            'davao_del_sur' => ['served' => 0],
            'davao_de_oro' => ['served' => 0],
            'davao_occidental' => ['served' => 0],
            'davao_oriental' => ['served' => 0],
        ];

        $allChildren = Child::all();
        $childrenIDs = $allChildren->pluck('id');

        $children = ChildCenter::with('child', 'center', 'implementation')
            ->whereIn('child_id', $childrenIDs)
            ->where('status', 'active');


        $totalChildrenQuery = clone $children;
        $totalMaleQuery = clone $children;
        $totalFemaleQuery = clone $children;
        $fundedChildren = clone $children;

        if(auth()->user()->hasRole('admin')){
            $totalChild = $totalChildrenQuery->whereHas('implementation', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })->get();

            $childIDs = $totalChild->pluck('child_id');
            $totalChildCount = $totalChild->count();

            $males = $totalMaleQuery->whereHas('child', function ($query) {
                        $query->where('sex_id', 1);
                    })->whereHas('implementation', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })->get();
            
            $totalMaleCount = $males->count();

            $females = $totalFemaleQuery->whereHas('child', function ($query) {
                        $query->where('sex_id', 2);
                    })->whereHas('implementation', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })->get();

            $totalFemaleCount = $females->count();

            $allChild = Child::whereIn('id', $childIDs)->get();

            $childPSGCs = $allChild->pluck('psgc_id');

            foreach($allChild as $child) {

                $dob = Carbon::parse($child->date_of_birth);
                $ageInYears = $dob->diffInYears(Carbon::now());

                if ($ageInYears == 2) {
                    $child->sex_id == 1 ? $ageCounts['2_years_old']['male']++ : $ageCounts['2_years_old']['female']++;
                    $ageCounts['2_years_old']['total']++;
                } elseif ($ageInYears == 3) {
                    $child->sex_id == 1 ? $ageCounts['3_years_old']['male']++ : $ageCounts['3_years_old']['female']++;
                    $ageCounts['3_years_old']['total']++;
                } elseif ($ageInYears == 4) {
                    $child->sex_id == 1 ? $ageCounts['4_years_old']['male']++ : $ageCounts['4_years_old']['female']++;
                    $ageCounts['4_years_old']['total']++;
                } elseif ($ageInYears == 5) {
                    $child->sex_id == 1 ? $ageCounts['5_years_old']['male']++ : $ageCounts['5_years_old']['female']++;
                    $ageCounts['5_years_old']['total']++;
                }

                if ($child->pantawid_details != null) {
                    $child->sex_id == 1 ? $profileCounts['pantawid']['male']++ : $profileCounts['pantawid']['female']++;
                    $profileCounts['pantawid']['total']++;
                } elseif ($child->person_with_disability_details != null) {
                    $child->sex_id == 1 ? $profileCounts['pwd']['male']++ : $profileCounts['pwd']['female']++;
                    $profileCounts['pwd']['total']++;
                } elseif ($child->is_indigenous_people == true) {
                    $child->sex_id == 1 ? $profileCounts['ip']['male']++ : $profileCounts['ip']['female']++;
                    $profileCounts['ip']['total']++;
                } elseif ($child->is_child_of_soloparent == true) {
                    $child->sex_id == 1 ? $profileCounts['soloparent']['male']++ : $profileCounts['soloparent']['female']++;
                    $profileCounts['soloparent']['total']++;
                } elseif ($child->is_lactose_intolerant == true) {
                    $child->sex_id == 1 ? $profileCounts['lactoseintolerant']['male']++ : $profileCounts['lactoseintolerant']['female']++;
                    $profileCounts['lactoseintolerant']['total']++;
                }
            }

            $getChildPSGC = Psgc::whereIn('psgc_id', $childPSGCs)->get();

            dd($childPSGCs, $getChildPSGC);
        
            $childCity = $getChildPSGC->pluck('city_name');
            $childProvince = $getChildPSGC->pluck('province_name');

            foreach ($getChildPSGC as $psgc) { 
                if ($psgc->city_name === "DAVAO CITY") {
                    $provinceCounts['davao_city']['served']++;
                }
            
                switch ($psgc->province_name) {
                    case "DAVAO DEL NORTE":
                        $provinceCounts['davao_del_norte']['served']++;
                        break;
                    case "DAVAO DEL SUR":
                        $provinceCounts['davao_del_sur']['served']++;
                        break;
                    case "DAVAO DE ORO":
                        $provinceCounts['davao_de_oro']['served']++;
                        break;
                    case "DAVAO OCCIDENTAL":
                        $provinceCounts['davao_occidental']['served']++;
                        break;
                    case "DAVAO ORIENTAL":
                        $provinceCounts['davao_oriental']['served']++;
                        break;
                }
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


        return view('dashboard', compact('totalChildCount', 'totalMaleCount', 'totalFemaleCount', 'ageCounts', 'profileCounts', 'provinceCounts'));
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
