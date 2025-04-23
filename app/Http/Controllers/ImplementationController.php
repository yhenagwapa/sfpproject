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
        return view('cycle.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function checkActiveStatus(StoreImplementationRequest $request)
    {
        $validatedData = $request->validated();

        $cycleExists = Implementation::where('name', $request->cycle_name)
                            ->exists();

        if ($cycleExists) {
            return redirect()->back()->with('error', 'Cycle already exists.');
        }

        if($request->cycle_status == 'active'){
            $activeCyle = Implementation::where('status', 'active')
                        ->where('type', $request->cycle_type)
                        ->first();

            if($activeCyle){
                return redirect()->route('cycle.create')
                    ->with('exists', true)
                    ->with('message', 'An active implementation exists. Do you want to close it?')
                    ->with('active_cycle_id', $activeCyle->id)
                    ->withInput();
            }
        }
        return $this->store($request);
    }

    public function store(Request $request)
    {
        if($request->active_cycle_id){
            $closeCycle = Implementation::findOrFail($request->active_cycle_id);

            $closeCycle->update([
                'status' => 'closed'
            ]);

            ChildCenter::where('status', 'active')
                ->where('implementation_id', $closeCycle->id)
                ->update(['status' => 'inactive']);

            Implementation::create([
                'name' => $request->cycle_name,
                'school_year_from' => $request->cycle_school_year_from,
                'school_year_to' => $request->cycle_school_year_to,
                'target' => $request->cycle_target,
                'allocation' => $request->cycle_allocation,
                'type' => $request->cycle_type,
                'status' => $request->cycle_status,
                'created_by_user_id' => auth()->id(),
            ]);

            return redirect()->route('cycle.index')->with('success', 'Cycle implementation saved successfully');
        }

        Implementation::create([
            'name' => $request->cycle_name,
            'school_year_from' => $request->cycle_school_year_from,
            'school_year_to' => $request->cycle_school_year_to,
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
    public function show(Request $request)
    {
        session(['editing_cycle_id' => $request->input('cycle_id')]);

        return redirect()->route('cycle.edit');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $cycleID = session('editing_cycle_id');

        $cycle = Implementation::findOrFail($cycleID);
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
    public function update(UpdateImplementationRequest $request)
    {
        $cycleID = session('editing_cycle_id');

        $cycle = Implementation::findOrFail($cycleID);

        $validatedData = $request->validated();

        $cycle->update([
            'name' => $validatedData['cycle_name'],
            'school_year_from' => $validatedData['cycle_school_year_from'],
            'school_year_to' => $validatedData['cycle_school_year_to'],
            'target' => $validatedData['cycle_target'],
            'allocation' => $validatedData['cycle_allocation'],
            'type' => $validatedData['cycle_type'],
            'updated_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('cycle.index')->with('success', 'Cycle updated successfully.');
    }


    public function updateCycleStatus(Request $request)
    {
        $cycleID = $request->input('cycle_id');

        $implementation = Implementation::findOrFail($cycleID);

        $request->validate([
            'cycle_status' => 'required|string|in:active,inactive,closed',
        ]);


        $implementation->update([
            'status' => $request->cycle_status,
        ]);

        if($request->cycle_status === 'closed'){
            ChildCenter::where('status', 'active')
            ->where('implementation_id', $cycleID)
            ->update(['status' => 'inactive']);
        }

        return redirect()->back()->with('success', 'Implementation successfully updated.');
    }

    public function updateMilkFeedingStatus(Request $request)
    {
        $milkFeedingID = $request->input('milkfeeding_id');

        $implementation = Implementation::findOrFail($milkFeedingID);

        $request->validate([
            'milkfeeding_status' => 'required|string|in:active,inactive,closed',
        ]);


        $implementation->update([
            'status' => $request->milkfeeding_status,
        ]);

        if($request->milkfeeding_status === 'closed'){
            ChildCenter::where('status', 'active')
            ->where('implementation_id', $milkFeedingID)
            ->update(['status' => 'inactive']);
        }

        return redirect()->back()->with('success', 'Implementation successfully updated.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Implementation $Implementation)
    {
        //
    }
}
