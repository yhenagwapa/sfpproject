<?php

namespace App\Http\Controllers;

use App\Models\ChildDevelopmentCenter;
use App\Models\Psgc;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Child;
use App\Models\ChildCenter;
use App\Models\UserCenter;
use App\Models\Implementation;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-cycle-implementation', ['only' => ['view']]);
        $this->middleware('permission:create-cycle-implementation', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-cycle-implementation', ['only' => ['edit', 'update']]);
    }
    public function index(Request $request)
    {
        $cycleID = session('report_cycle_id') ?? $request->cycle_id;
        $cycle = Implementation::where('id', $cycleID)->first();

        // add filter to session
        session(['filter_cdc_id' => $request->center_name]);

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;
        $childCount = null;

        $fundedChildren = Child::with('records','nutritionalStatus', 'sex');

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                                ->where('funded', 1)
                                ->where('status', 'active');
                })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id);
                })
                ->paginate(5);

                $childCount = $isFunded->count();

            } else {

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($request, $cycle) {
                        $query->where('child_development_center_id', $request->center_name)
                                ->where('implementation_id', $cycle->id)
                                ->where('funded', 1)
                                ->where('status', 'active');
                })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id);
                })
                ->paginate(5);
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
                $childCount = $isFunded->count();
            }

        } else {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($centerIDs, $cycle) {
                    if ($cycle) {
                        $query->whereIn('child_development_center_id', $centerIDs)
                            ->where('implementation_id', $cycle->id)
                            ->where('funded', 1)
                            ->where('status', 'active');
                    }
                })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id);
                })
                ->paginate(5);

                $childCount = $isFunded->count();
            } else {

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
                        $query->where('child_development_center_id', $cdcId)
                                ->where('implementation_id', $cycle->id)
                                ->where('funded', 1)
                                ->where('status', 'active');
                })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id);
                })
                ->paginate(5);
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
                $childCount = $isFunded->count();
            }

        }

        return view('reports.index2', compact('isFunded', 'centers', 'cdcId', 'selectedCenter', 'cycle', 'centerNames', 'childCount'));
    }

    public function index2($cycleId, Request $request)
    {
        $cycle = Implementation::where('id', $cycleId)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $fundedChildren = Child::with('records','nutritionalStatus', 'sex');

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('funded', 1);
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->paginate(5);

                $childCount = $isFunded->count();

            } else {

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($request, $cycle) {
                    $query->where('child_development_center_id', $request->center_name)
                        ->where('implementation_id', $cycle->id)
                        ->where('funded', 1);
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->paginate(5);
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
                $childCount = $isFunded->count();
            }

        } else {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($centerIDs, $cycle) {
                    if ($cycle) {
                        $query->whereIn('child_development_center_id', $centerIDs)
                            ->where('implementation_id', $cycle->id)
                            ->where('funded', 1);
                    }
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->paginate(5);

                $childCount = $isFunded->count();
            } else {

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
                    $query->where('child_development_center_id', $cdcId)
                        ->where('implementation_id', $cycle->id)
                        ->where('funded', 1);

                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->paginate(5);
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
                $childCount = $isFunded->count();
            }

        }

        return view('reports.index2', compact('isFunded', 'centers', 'cdcId', 'selectedCenter', 'cycle', 'centerNames', 'childCount'));
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        session(['report_cycle_id' => $request->input('cycle_id')]);

        return redirect()->route('reports.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
