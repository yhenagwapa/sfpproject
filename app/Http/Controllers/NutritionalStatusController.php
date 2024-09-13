<?php

namespace App\Http\Controllers;

use App\Models\NutritionalStatus;
use App\Http\Requests\StoreNutritionalStatusRequest;
use App\Http\Requests\UpdateNutritionalStatusRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Child;
use Carbon\Carbon;

class NutritionalStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:nutrition-status-entry', ['only' => ['index', 'store']]);
        $this->middleware('permission:nutrition-status-entry', ['only' => ['store']]);
    }

    public function index($id)
    {
        $child = Child::findOrFail($id);

        // Fetch the actual date of weighing for the entry
        $entryData = NutritionalStatus::where('child_id', $id)
                        ->whereNotNull('entry_weight') // Ensure that entry_weight is not null
                        ->whereNotNull('entry_height') // Ensure that entry_height is not null
                        ->whereNotNull('entry_actual_date_of_weighing') // Ensure that entry_actual_date_of_weighing is not null
                        ->first(); // Fetch the first matching record

        // Handle the case where no entry data is found
        $hasUponEntryData = $entryData ? true : false;

        // Corrected the syntax by removing the named argument usage
        $exitData = NutritionalStatus::where('child_id', $id)
                        ->whereNotNull('exit_weight') // Ensure that exit_weight is not null
                        ->whereNotNull('exit_height') // Ensure that exit_height is not null
                        ->whereNotNull('exit_actual_date_of_weighing') // Ensure that exit_actual_date_of_weighing is not null
                        ->first(); // Fetch the first matching record

        // Handle the case where no exit data is found
        $hasUponExitData = $exitData ? true : false;

        // Fetch all nutritional status records for the child
        $results = NutritionalStatus::where('child_id', $id)->get();

        // Calculate age only if entry data is present
        $dob = $child->date_of_birth;
        $entryAgeInYears = null;
        $entryAgeInMonths = null;
        $exitAgeInYears = null;
        $exitAgeInMonths = null;

        if ($entryData) {
            $entyWeighingDate = Carbon::parse($entryData->entry_actual_date_of_weighing);
            $entryAgeInYears = $entyWeighingDate->diffInYears(Carbon::parse($dob));
            $entryAgeInMonths = $entyWeighingDate->diffInMonths(Carbon::parse($dob)) % 12;
        }

        if ($exitData) {
            $exitWeighingDate = Carbon::parse($entryData->exit_actual_date_of_weighing);
            $exitAgeInYears = $exitWeighingDate->diffInYears(Carbon::parse($dob));
            $exitAgeInMonths = $exitWeighingDate->diffInMonths(Carbon::parse($dob)) % 12;
        }

        return view('nutritionalstatus.index', compact('child', 'results', 'entryAgeInYears', 'entryAgeInMonths', 'exitAgeInYears', 'exitAgeInMonths','hasUponEntryData', 'hasUponExitData'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeUponEntryDetails(StoreNutritionalStatusRequest $request)
    {
        $validatedData = $request->validated();

        $eentryRecord = NutritionalStatus::where('child_id', $request->child_id)
                        ->first();

        if ($eentryRecord) {
            return redirect()->back()->with(['error' => 'Upon entry details already exists.']);
        }
        
        $entryNutritionalStatus = NutritionalStatus::create([
            'child_id' => $request->child_id,
            'entry_weight' => $request->entry_weight,
            'entry_height' => $request->entry_height,
            'entry_actual_date_of_weighing' => $request->entry_actual_date_of_weighing,
            'created_by_user_id' => auth()->id(),
            'updated_by_user_id' => auth()->id(),   
        ]);

        // Redirect or return a response
        return redirect()->route('nutritionalstatus.index', ['id' => $request->child_id])->with('success', 'Upon entry details saved successfully.');
    }


    public function storeExitDetails(UpdateNutritionalStatusRequest $request)
    {
        $validatedData = $request->validated();

        $exitRecord = NutritionalStatus::where('child_id', $request->child_id)
                    ->whereNotNull('exit_weight') // Ensure that exit_weight is not null
                    ->whereNotNull('exit_height') // Ensure that exit_height is not null
                    ->whereNotNull('exit_actual_date_of_weighing') // Ensure that exit_actual_date_of_weighing is not null
                    ->first();

        if ($exitRecord) {
            return redirect()->back()->with(['error' => 'Exit details already exists.']);
        }
        
        $exitRecord->update([
            'exit_weight' => $request->exit_weight,
            'exit_height' => $request->exit_height,
            'exit_actual_date_of_weighing' => $request->exit_actual_date_of_weighing,
            'updated_by_user_id' => auth()->id(),   
        ]);

        // Redirect or return a response
        return redirect()->route('nutritionalstatus.index', ['id' => $request->child_id])
                     ->with('success', 'After 120 feeding days details updated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(NutritionalStatus $nutritionalStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NutritionalStatus $nutritionalStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNutritionalStatusRequest $request, NutritionalStatus $nutritionalStatus)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NutritionalStatus $nutritionalStatus)
    {
        //
    }
}
