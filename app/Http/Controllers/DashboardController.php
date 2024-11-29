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
}
