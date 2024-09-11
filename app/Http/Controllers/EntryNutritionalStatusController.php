<?php

namespace App\Http\Controllers;

use App\Models\EntryNutritionalStatus;
use App\Http\Requests\StoreEntryNutritionalStatusRequest;
use App\Http\Requests\UpdateEntryNutritionalStatusRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Child;
use Carbon\Carbon;

class EntryNutritionalStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:nutrition-status-entry', ['only' => ['index', 'store']]);
        $this->middleware('permission:nutrition-status-entry', ['only' => ['store']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $child = Child::findOrFail($id);
        $dob = $child->date_of_birth;

        $today = Carbon::now();
        $birthDate = Carbon::parse($dob);

        $ageInYears = $today->diffInYears($birthDate);
        $ageInMonths = $today->diffInMonths($birthDate) % 12;

        $results = DB::table('entry_nutritional_statuses')
                    ->join('exit_nutritional_statuses','entry_nutritional_statuses.child_id', '=', 'exit_nutritional_statuses.child_id')
                    ->select(
                        'entry_nutritional_statuses.actual_date_of_weighing as entry_date',
                        'entry_nutritional_statuses.weight as entry_weight',
                        'entry_nutritional_statuses.height as entry_height',
                        'entry_nutritional_statuses.weight_for_age as entry_weight_for_age',
                        'entry_nutritional_statuses.weight_for_height as entry_weight_for_height',
                        'entry_nutritional_statuses.height_for_age as entry_height_for_age',
                        'exit_nutritional_statuses.actual_date_of_weighing as exit_date',
                        'exit_nutritional_statuses.weight as exit_weight',
                        'exit_nutritional_statuses.height as exit_height',
                        'exit_nutritional_statuses.weight_for_age as exit_weight_for_age',
                        'exit_nutritional_statuses.weight_for_height as exit_weight_for_height',
                        'exit_nutritional_statuses.height_for_age as exit_height_for_age'
                        )
                    ->where('entry_nutritional_statuses.child_id', $child->id)
                    ->get();
        return view('nutritionalstatus.index', compact('child', 'results', 'ageInYears', 'ageInMonths'));
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
    public function store(StoreEntryNutritionalStatusRequest $request)
    {

        $validatedData = $request->validated();

        $existingRecord = EntryNutritionalStatus::where('child_id', $request->child_id)
                        ->first();

        if ($existingRecord) {
            return redirect()->back()->with(['error' => 'Upon entry details already exists.']);
        }

        $severelyWasted = 5.8;
        $wastedFrom = 5.9;
        $wastedTo = 6.2;

        if ($validatedData['weight'] <= $severelyWasted){
            $weightForHeight = "Severely Wasted";
        } elseif ($validatedData['weight'] >= $wastedFrom && $validatedData['weight'] <= $wastedTo) {
            $weightForHeight = "Wasted";
        } else {
            $weightForHeight = "Normal"; // You can adjust this based on your classification logic
        }
        $validatedData['weight_for_height'] = $weightForHeight;

        
        $entryNutritionalStatus = EntryNutritionalStatus::create([
            'child_id' => $request->child_id,
            'weight' => $request->weight,
            'height' => $request->height,
            'actual_date_of_weighing' => $request->actual_date_of_weighing,
            'weight_for_height' => $request->weightForHeight,
            'created_by_user_id' => auth()->id(),   
        ]);

        // Redirect or return a response
        return redirect()->route('nutritionalstatus.index', ['id' => $request->child_id])->with('success', 'Nutritional status saved successfully.');


    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // $child = Child::findOrFail($id);

        // $results = DB::table('entry_nutritional_statuses')
        //             ->join('exit_nutritional_statuses','entry_nutritional_statuses.child_id', '=', 'exit_nutritional_statuses.child_id')
        //             ->select(
        //                 'entry_nutritional_statuses.actual_date_of_weighing as entry_date',
        //                 'entry_nutritional_statuses.weight as entry_weight',
        //                 'entry_nutritional_statuses.height as entry_height',
        //                 'entry_nutritional_statuses.weight_for_age as entry_weight_for_age',
        //                 'entry_nutritional_statuses.weight_for_height as entry_weight_for_height',
        //                 'entry_nutritional_statuses.height_for_age as entry_height_for_age',
        //                 'exit_nutritional_statuses.actual_date_of_weighing as exit_date',
        //                 'exit_nutritional_statuses.weight as exit_weight',
        //                 'exit_nutritional_statuses.height as exit_height',
        //                 'exit_nutritional_statuses.weight_for_age as exit_weight_for_age',
        //                 'exit_nutritional_statuses.weight_for_height as exit_weight_for_height',
        //                 'exit_nutritional_statuses.height_for_age as exit_height_for_age'
        //                 )
        //             ->where('entry_nutritional_statuses.child_id', $child->id)
        //             ->get();

        // return view('nutritionalstatus.index', ['results' => $results, 'id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntryNutritionalStatus $entryNutritionalStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntryNutritionalStatusRequest $request, EntryNutritionalStatus $entryNutritionalStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntryNutritionalStatus $entryNutritionalStatus)
    {
        //
    }
}
