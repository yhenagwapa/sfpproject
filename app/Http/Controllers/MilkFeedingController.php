<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMilkFeedingRequest;
use App\Http\Requests\UpdateMilkFeedingRequest;
use App\Models\MilkFeeding;
use Illuminate\Http\Request;
use App\Enums\CycleStatus;
use App\Models\ChildDevelopmentCenter;
use App\Models\Child;
use App\Models\CycleImplementation;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Psgc;

class MilkFeedingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $milkFeedings = MilkFeeding::all();

        return view('cycle.index', compact('milkFeedings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cycleStatuses = CycleStatus::cases();

        return view('milkfeedings.create', compact('cycleStatuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMilkFeedingRequest $request)
    {
        $validatedData = $request->validated();

        $cycleExists = MilkFeeding::where('name', $request->name)
                            ->exists();

        if ($cycleExists) {
            return redirect()->back()->with('error', 'Cycle already exists.');
        }

        $cycle = MilkFeeding::create([
            'name' => $request->name,
            'school_year' => $request->school_year,
            'target' => $request->target,
            'allocation' => $request->allocation,
            'status' => $request->status,
            'created_by_user_id' => auth()->id(),
            'updated_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('cycle.index')->with('success', 'Milk Feeding saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $milkfeeding = MilkFeeding::findOrFail($id);
        $cycleStatuses = CycleStatus::cases();

        return view('milkfeedings.edit', compact('milkfeeding', 'cycleStatuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMilkFeedingRequest $request, string $id)
    {
        $cycle = MilkFeeding::findOrFail($id);

        $validatedData = $request->validated();
        $validatedData['updated_by_user_id'] = auth()->id();

        $cycle->update($validatedData);

        return redirect()->route('cycle.index')->with('success', 'Milk feeding updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function reportIndex(Request $request, MilkFeeding $milkfeeding)
    {
        $milkfeeding = MilkFeeding::where('status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $fundedChildren = Child::with('nutritionalStatus', 'sex');

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->where('is_funded', true)
                    ->where('milk_feeding_id', $milkfeeding->id)
                    ->paginate(10);
            } else {
                $isFunded = $fundedChildren->where('is_funded', true)
                    ->where('milk_feeding_id', $milkfeeding->id)
                    ->where('child_development_center_id', $cdcId)
                    ->paginate(10);
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
            }

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->where('is_funded', true)
                    ->where('milk_feeding_id', $milkfeeding->id)
                    ->whereIn('child_development_center_id', $centerIds)
                    ->paginate(10);
            } else {
                $isFunded = $fundedChildren->where('is_funded', true)
                    ->where('milk_feeding_id', $milkfeeding->id)
                    ->where('child_development_center_id', $cdcId)
                    ->paginate(10);
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
            }

        } else {
            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->where('is_funded', true)
                    ->where('milk_feeding_id', $milkfeeding->id)
                    ->whereIn('child_development_center_id', $centerIds)
                    ->paginate(10);
            } else {
                $isFunded = $fundedChildren->where('is_funded', true)
                    ->where('milk_feeding_id', $milkfeeding->id)
                    ->where('child_development_center_id', $cdcId)
                    ->paginate(10);
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
            }

        }

        return view('milkfeedings.report', compact('isFunded', 'centers', 'cdcId', 'selectedCenter', 'milkfeeding'));
    }
    public function printMasterlist(Request $request)
    {
        $milkfeeding = MilkFeeding::where('status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $fundedChildren = Child::with('nutritionalStatus', 'sex')
            ->where('is_funded', true)
            ->where('milk_feeding_id', $milkfeeding->id);

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->get();
            } else {
                $isFunded = $fundedChildren->where('child_development_center_id', $cdcId)->get();
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
            }

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereIn('child_development_center_id', $centerIds)->get();
            } else {
                $isFunded = $fundedChildren->where('child_development_center_id', $cdcId)->get();
                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
            }

        } else {

            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereIn('child_development_center_id', $centerIds)->get();
            } else {
                $isFunded = $fundedChildren->where('child_development_center_id', $cdcId)->get();
                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
            }

        }

        $pdf = PDF::loadView('milkfeedings.print.masterlist', compact('milkfeeding', 'isFunded', 'centers', 'cdcId', 'selectedCenter'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($milkfeeding->name . ' Masterlist.pdf');
    }
    public function printMalnourish(Request $request)
    {
        $milkfeeding = MilkFeeding::where('status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');

        $fundedChildren = Child::with('nutritionalStatus', 'sex', 'center')
            ->where('is_funded', true)
            ->where('milke_feeding_id', $milkfeeding->id)
            ->get();

        $childrenWithNutritionalStatus = [];
        $province = null;
        $city = null;

        foreach ($fundedChildren as $child) {
            $nutritionalStatuses = $child->nutritionalStatus;

            if ($nutritionalStatuses && $nutritionalStatuses->count() > 0) {
                $entry = $nutritionalStatuses->first();
                $exit = $nutritionalStatuses->count() > 1 ? $nutritionalStatuses[1] : null;

            } else {
                $entry = null;
                $exit = null;
            }

            $childrenWithNutritionalStatus[] = [
                'child' => $child,
                'entry' => $entry,
                'exit' => $exit,
            ];
        }

        if (auth()->user()->hasRole('admin')) {
            $isFunded = Child::with('nutritionalStatus', 'sex')
                ->where('is_funded', true)
                ->where('milke_feeding_id', $milkfeeding->id)
                ->orderBy('child_development_center_id')
                ->paginate(10);

            $centers = ChildDevelopmentCenter::all()->keyBy('id');


        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $isFunded = Child::with('nutritionalStatus', 'sex')
                ->where('is_funded', true)
                ->where('milke_feeding_id', $milkfeeding->id)
                ->whereIn('child_development_center_id', $centerIds)
                ->orderBy('child_development_center_id')
                ->paginate(10);
        }

        $pdf = PDF::loadView('milkfeedings.print.malnourished', compact('milkfeeding', 'isFunded', 'centers', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($milkfeeding->name . ' Malnourished.pdf');
    }
    public function printDisabilities()
    {
        $milkfeeding = MilkFeeding::where('status', 'active')->first();
        $province = null;
        $city = null;

        $childrenWithDisabilities = Child::with('center')
            ->where('is_funded', true)
            ->where('is_person_with_disability', true)
            ->where('person_with_disability_details', '!=', '')
            ->where('milk_feeding_id', $milkfeeding->id);


        if (auth()->user()->hasRole('admin')) {
            $isPwdChildren = $childrenWithDisabilities->orderBy('child_development_center_id')->paginate(10);

            $centers = ChildDevelopmentCenter::all()->keyBy('id');

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $isPwdChildren = $childrenWithDisabilities = Child::with('center')
                ->where('is_funded', true)
                ->where('is_person_with_disability', true)
                ->whereIn('child_development_center_id', $centerIds)
                ->where('milk_feeding_id', $milkfeeding->id)
                ->orderBy('child_development_center_id')
                ->paginate(10);

        }

        $pdf = PDF::loadView('milkfeedings.print.disabilities', compact('milkfeeding', 'isPwdChildren', 'centers', 'province', 'city'))
            ->setPaper('folio')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($milkfeeding->cycle_name . ' Persons with Disability.pdf');
    }
}
