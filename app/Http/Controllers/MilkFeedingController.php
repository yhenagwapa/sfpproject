<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMilkFeedingRequest;
use App\Http\Requests\UpdateMilkFeedingRequest;
use App\Models\MilkFeeding;
use Illuminate\Http\Request;
use App\Enums\CycleStatus;

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
}
