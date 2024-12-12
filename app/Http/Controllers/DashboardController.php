<?php

namespace App\Http\Controllers;

use App\Models\CycleImplementation;
use Illuminate\Http\Request;
use App\Models\Child;
use App\Models\ChildDevelopmentCenter;

class DashboardController extends Controller
{
    public function index()
    {
        $cycle = CycleImplementation::where('cycle_status', 'active')->first();

        if(auth()->user()->hasRole('admin')){
            $totalChild = Child::where('is_funded', true)
                ->where('cycle_implementation_id', $cycle->id)->count();

            $totalMale = Child::with('sex')
                ->where('is_funded', true)
                ->where('sex_id', '1')
                ->where('cycle_implementation_id', $cycle->id)
                ->count();

            $totalFemale = Child::with('sex')
                ->where('is_funded', true)
                ->where('sex_id', '2')
                ->where('cycle_implementation_id', $cycle->id)
                ->count();

        } elseif(auth()->user()->hasRole('lgu focal')){
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            $totalChild = Child::where('is_funded', true)
                ->where('cycle_implementation_id', $cycle->id)
                ->whereIn('child_development_center_id', $centerIds)
                ->count();

            $totalMale = Child::with('sex')
                ->where('is_funded', true)
                ->where('sex_id', '1')
                ->where('cycle_implementation_id', $cycle->id)
                ->whereIn('child_development_center_id', $centerIds)
                ->count();

            $totalFemale = Child::with('sex')
                ->where('is_funded', true)
                ->where('sex_id', '2')
                ->where('cycle_implementation_id', $cycle->id)
                ->whereIn('child_development_center_id', $centerIds)
                ->count();
        } elseif(auth()->user()->hasRole('child development worker')){
            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            $totalChild = Child::where('is_funded', true)
                ->where('cycle_implementation_id', $cycle->id)
                ->whereIn('child_development_center_id', $centerIds)
                ->count();

            $totalMale = Child::with('sex')
                ->where('is_funded', true)
                ->where('sex_id', '1')
                ->where('cycle_implementation_id', $cycle->id)
                ->whereIn('child_development_center_id', $centerIds)
                ->count();

            $totalFemale = Child::with('sex')
                ->where('is_funded', true)
                ->where('sex_id', '2')
                ->where('cycle_implementation_id', $cycle->id)
                ->whereIn('child_development_center_id', $centerIds)
                ->count();
        }

        return view('dashboard', compact('totalChild', 'totalMale', 'totalFemale'));
    }
    
    public function undernourishedUponEntry(CycleImplementation $cycle)
    {

        if (auth()->user()->hasRole('admin')) {
            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycle->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $centers = ChildDevelopmentCenter::paginate(15);
            $centers->getCollection()->keyBy('id');

        } else if (auth()->user()->hasRole('lgu focal')) {

            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->paginate(15);
            $centerIds = $centers->pluck('id');

            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycle->id)
            ->whereIn('children.child_development_center_id', $centerIds)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();
        }

        $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
            if ($child->nutritionalStatus->isEmpty()) {
                return [
                    'child_id' => $child->id,
                    'entry' => null,
                ];
            }
            $statuses = $child->nutritionalStatus->where('is_undernourish', true)
                ->whereIn('age_in_years', [2, 3, 4, 5]);
            $entry = $statuses->first();

            return [
                'child_id' => $child->id,
                'entry' => $entry,
            ];
        });

        $ageGroupsPerCenter = $fundedChildren->groupBy('child_development_center_id')->map(function ($childrenByCenter) use ($nutritionalStatusOccurrences) {
            return [
                '2_years_old' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $firstStatus->age_in_years == 2 && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $firstStatus->age_in_years == 2 && $child->sex_id == 2;
                    })->count(),
                ],
                '3_years_old' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $firstStatus->age_in_years == 3 && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $firstStatus->age_in_years == 3 && $child->sex_id == 2;
                    })->count(),
                ],
                '4_years_old' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $firstStatus->age_in_years == 4 && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $firstStatus->age_in_years == 4 && $child->sex_id == 2;
                    })->count(),
                ],
                '5_years_old' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $firstStatus->age_in_years == 5 && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $firstStatus->age_in_years == 5 && $child->sex_id == 2;
                    })->count(),
                ],
                'indigenous_people' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->is_indegenous_people == true && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->is_indegenous_people == true && $child->sex_id == 2;
                    })->count(),
                ],
                'pantawid' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->pantawid_details != null && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->pantawid_details != null && $child->sex_id == 2;
                    })->count(),
                ],
                'pwd' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->person_with_disability_details != null && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->person_with_disability_details != null && $child->sex_id == 2;
                    })->count(),
                ],
                'lactose_intolerant' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->is_lactose_intolerant == true && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->is_lactose_intolerant == true && $child->sex_id == 2;
                    })->count(),
                ],
                'child_of_solo_parent' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->is_child_of_soloparent == true && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $child->is_child_of_soloparent == true && $child->sex_id == 2;
                    })->count(),
                ],
                'dewormed' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->deworming_date != null && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->deworming_date != null && $child->sex_id == 2;
                    })->count(),
                ],
                'vitamin_a' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->vitamin_a_date != null && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->vitamin_a_date != null && $child->sex_id == 2;
                    })->count(),
                ],

            ];
        });

        return view('dashboard', compact('centers', 'ageGroupsPerCenter', 'cycle'));
    }
}
