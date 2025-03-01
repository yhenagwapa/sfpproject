<?php

namespace App\Http\Controllers;

use App\Models\Implementation;
use App\Models\ChildCenter;
use App\Http\Requests\StoreImplementationRequest;
use App\Http\Requests\UpdateImplementationRequest;
use App\Enums\CycleStatus;
use Illuminate\Http\Request;

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
        $allCycles = Implementation::where('type', 'regular')
            ->get();

        $milkFeedings = Implementation::where('type', 'milk')
            ->get();

        $cycleStatuses = CycleStatus::cases();

        return view('cycle.index', compact('allCycles', 'milkFeedings', 'cycleStatuses'));
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

        $cycleExists = Implementation::where('name', $request->cycle_name)
                            ->exists();

        if ($cycleExists) {
            return redirect()->back()->with('error', 'Cycle already exists.');
        }

        $cycle = Implementation::create([
            'name' => $request->cycle_name,
            'school_year' => $request->cycle_school_year,
            'target' => $request->cycle_target,
            'allocation' => $request->cycle_allocation,
            'type' => $request->cycle_type,
            'status' => $request->cycle_status,
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

        $cycleType = [
            'regular' => 'REGULAR',
            'milk' => 'MILK',
        ];

        return view('cycle.edit', compact('cycle', 'cycleStatuses', 'cycleType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImplementationRequest $request, $id)
    {
        $cycle = Implementation::findOrFail($id);

        $validatedData = $request->validated();

        $cycle->update([
            'name' => $validatedData['cycle_name'],
            'school_year' => $validatedData['cycle_school_year'],
            'target' => $validatedData['cycle_target'],
            'allocation' => $validatedData['cycle_allocation'],
            'type' => $validatedData['cycle_type'],
            'updated_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('cycle.index')->with('success', 'Cycle updated successfully.');
    }


    public function updateStatus(Request $request, $implementation)
    {
        $implementation = Implementation::findOrFail($request->cycle_id);

        $request->validate([
            'cycle_status' => 'required|string|in:active,closed',
        ]);

        $implementation->update([
            'status' => $request->cycle_status,
        ]);

        ChildCenter::where('status', 'active')
            ->where('implementation_id', $request->cycle_id)
            ->update(['status' => 'inactive']);

        return redirect()->back()->with('success', 'Implementation closed.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Implementation $Implementation)
    {
        //
    }
}
