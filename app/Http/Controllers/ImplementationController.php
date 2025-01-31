<?php

namespace App\Http\Controllers;

use App\Models\Implementation;
use App\Models\MilkFeeding;
use App\Http\Requests\StoreImplementationRequest;
use App\Http\Requests\UpdateImplementationRequest;
use App\Enums\CycleStatus;

class ImplementationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-cycle-implementation', ['only' => ['view']]);
        $this->middleware('permission:add-cycle-implementation', ['only' => ['create','store']]);
        $this->middleware('permission:edit-cycle-implementation', ['only' => ['edit','update']]);
    }
    public function index()
    {
        $allCycles = Implementation::all();
        $milkFeedings = MilkFeeding::all();

        return view('cycle.index', compact('allCycles', 'milkFeedings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cycleStatuses = CycleStatus::cases();

        return view('cycle.create', compact('cycleStatuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreImplementationRequest $request)
    {
        $validatedData = $request->validated();

        $cycleExists = Implementation::where('cycle_name', $request->cycle_name)
                            ->exists();

        if ($cycleExists) {
            return redirect()->back()->with('error', 'Cycle already exists.');
        }

        $cycle = Implementation::create([
            'cycle_name' => $request->cycle_name,
            'cycle_school_year' => $request->cycle_school_year,
            'cycle_target' => $request->cycle_target,
            'cycle_allocation' => $request->cycle_allocation,
            'cycle_status' => $request->cycle_status,
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('cycle.index')->with('success', 'Cycle implementation saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Implementation $Implementation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Implementation $cycle, $id)
    {
        $cycle = Implementation::findOrFail($id);
        $cycleStatuses = CycleStatus::cases();


        return view('cycle.edit', compact('cycle', 'cycleStatuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImplementationRequest $request, $id)
    {
        $cycle = Implementation::findOrFail($id);

        $validatedData = $request->validated();
        $validatedData['updated_by_user_id'] = auth()->id();

        $cycle->update($validatedData);

        return redirect()->route('cycle.index')->with('success', 'Cycle updated successfully.');
    }

    public function updateStatus(UpdateImplementationRequest $request, Implementation $Implementation)
    {
        $request->validate([
            'cycle_status' => ['required', 'enum:' . CycleStatus::class],  // Validate the enum value
        ]);

        $cycle = Implementation::firstWhere('id', $Implementation);

        if (!$cycle) {
            // Handle the case where no record is found
            return redirect()->route('cycles.index')->with('error', 'No data found in the Cycle Implementation table.');
        }

        $cycle->cycle_status = CycleStatus::from($request->cycle_status);  // Set the status
        $cycle->save();  // Save the updated status

        return redirect()->route('cycle.index', ['id' => $cycle->id])->with('success', 'Cycle status updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Implementation $Implementation)
    {
        //
    }
}
