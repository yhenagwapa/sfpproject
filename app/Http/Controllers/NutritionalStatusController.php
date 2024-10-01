<?php

namespace App\Http\Controllers;


use App\Models\NutritionalStatus;
use App\Models\cgs_wfa_girls;
use App\Models\cgs_wfa_boys;
use App\Models\cgs_hfa_girls;
use App\Models\cgs_hfa_boys;
use App\Models\cgs_wfh_girls;
use App\Models\cgs_wfh_boys;
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

        $entryData = NutritionalStatus::where('child_id', $id)
            ->whereNotNull('entry_weight') 
            ->whereNotNull('entry_height') 
            ->whereNotNull('entry_actual_date_of_weighing')
            ->first();

        $hasUponEntryData = $entryData ? true : false;

        $exitData = NutritionalStatus::where('child_id', $id)
            ->whereNotNull('exit_weight')
            ->whereNotNull('exit_height')
            ->whereNotNull('exit_actual_date_of_weighing')
            ->first();

        $hasUponExitData = $exitData ? true : false;

        $results = NutritionalStatus::where('child_id', $id)->get();

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

        return view('nutritionalstatus.index', compact('child', 'results', 'entryAgeInYears', 'entryAgeInMonths', 'exitAgeInYears', 'exitAgeInMonths','hasUponEntryData','hasUponExitData'));
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

        $entryRecord = NutritionalStatus::where('child_id', $request->child_id)
                        ->first();

        if ($entryRecord) {
            return redirect()->back()->with(['error' => 'Upon entry details already exists.']);
        
        } else{

            $entryWeightForAge = null;
            $entryHeightForAge = null;
            $entryWeightForHeight = null;
            $entryIsMalnourished = false;
            $entryIsUndernourished = false;

            $child = Child::with('nutritionalStatus', 'sex')
                ->where('id', $request->child_id)
                ->first();

            $childSex = $child->sex->id;
            $childBirthDate = Carbon::parse($child->date_of_birth);

            $entryWeighingDate = Carbon::parse($request->entry_actual_date_of_weighing);
            $entryAgeInMonths = $entryWeighingDate->diffInMonths($childBirthDate);

            //weight for age
            if ($childSex == '1'){
                $getAge = cgs_wfa_boys::where('age_month', $entryAgeInMonths)->first();
            
                if ((float) $getAge->severly_underweight >= (float)$request->entry_weight) {
                    $entryWeightForAge = 'Severely Underweight';
                } elseif ((float) $getAge->underweight_from <= (float)$request->entry_weight && (float) $getAge->underweight_to >= (float)$request->entry_weight) {
                    $entryWeightForAge = 'Underweight';
                } elseif ((float) $getAge->normal_from <= (float)$request->entry_weight && (float) $getAge->normal_to >= (float)$request->entry_weight) {
                    $entryWeightForAge = 'Normal';
                } elseif ((float)$request->entry_weight > (float) $getAge->normal_to) {
                    $entryWeightForAge = 'Overweight';
                }
            } else{
                $getAge = cgs_wfa_girls::where('age_month', $entryAgeInMonths)->first();
            
                if ((float) $getAge->severly_underweight >= (float)$request->entry_weight) {
                    $entryWeightForAge = 'Severely Underweight';
                } elseif ((float) $getAge->underweight_from <= (float)$request->entry_weight && (float) $getAge->underweight_to >= (float)$request->entry_weight) {
                    $entryWeightForAge = 'Underweight';
                } elseif ((float) $getAge->normal_from <= (float)$request->entry_weight && (float) $getAge->normal_to >= (float)$request->entry_weight) {
                    $entryWeightForAge = 'Normal';
                } elseif ((float)$request->entry_weight > (float) $getAge->normal_to) {
                    $entryWeightForAge = 'Overweight';
                }
            }

            //height for age
            if ($childSex == '1'){
                $getAge = cgs_hfa_boys::where('age_month', $entryAgeInMonths)->first();
            
                if ((float) $getAge->severly_stunted >= (float)$request->entry_height) {
                    $entryHeightForAge = 'Severely Stunted';
                } elseif ((float) $getAge->stunted_from <= (float)$request->entry_height && (float) $getAge->stunted_to >= (float)$request->entry_height) {
                    $entryHeightForAge = 'Stunted';
                } elseif ((float) $getAge->normal_from <= (float)$request->entry_height && (float) $getAge->normal_to >= (float)$request->entry_height) {
                    $entryWeightForAge = 'Normal';
                } elseif ((float) $getAge->tall <= (float)$request->entry_height){
                    $entryHeightForAge = 'Tall';
                }
            } else{
                $getAge = cgs_hfa_girls::where('age_month', $entryAgeInMonths)->first();
            
                if ((float) $getAge->severly_stunted >= (float)$request->entry_height) {
                    $entryHeightForAge = 'Severely Stunted';
                } elseif ((float) $getAge->stunted_from <= (float)$request->entry_height && (float) $getAge->stunted_to >= (float)$request->entry_height) {
                    $entryHeightForAge = 'Stunted';
                } elseif ((float) $getAge->normal_from <= (float)$request->entry_height && (float) $getAge->normal_to >= (float)$request->entry_height) {
                    $entryWeightForAge = 'Normal';
                } elseif ((float) $getAge->tall <= (float)$request->entry_height) {
                    $entryHeightForAge = 'Tall';
                }
            }

            //weight for height
            if ($childSex == '1'){
                $getHeight = cgs_wfh_boys::where('length_in_cm', $request->entry_height)->first();
            
                if ((float) $getHeight->severly_wasted >= (float)$request->entry_weight) {
                    $entryWeightForHeight = 'Severely Wasted';
                } elseif ((float) $getHeight->wasted_from <= (float)$request->entry_weight && (float) $getHeight->wasted_to >= (float)$request->entry_weight) {
                    $entryWeightForHeight = 'Wasted';
                } elseif ((float) $getHeight->normal_from <= (float)$request->entry_weight && (float) $getHeight->normal_to >= (float)$request->entry_weight) {
                    $entryWeightForHeight = 'Normal';
                } elseif ((float) $getHeight->overweight_from <= (float)$request->entry_weight && (float) $getHeight->overweight_to >= (float)$request->entry_weight) {
                    $entryWeightForHeight = 'Overweight';
                } elseif ((float) $getHeight->obese <= (float)$request->entry_weight){
                    $entryWeightForHeight = 'Obese';
                }
            } else{
                $getHeight = cgs_wfh_girls::where('length_in_cm', $request->entry_height)->first();
            
                if ((float) $getHeight->severly_wasted >= (float)$request->entry_weight) {
                    $entryWeightForHeight = 'Severely Wasted';
                } elseif ((float) $getHeight->wasted_from <= (float)$request->entry_weight && (float) $getHeight->wasted_to >= (float)$request->entry_weight) {
                    $entryWeightForHeight = 'Wasted';
                } elseif ((float) $getHeight->normal_from <= (float)$request->entry_weight && (float) $getHeight->normal_to >= (float)$request->entry_weight) {
                    $entryWeightForHeight = 'Normal';
                } elseif ((float) $getHeight->overweight_from <= (float)$request->entry_weight && (float) $getHeight->overweight_to >= (float)$request->entry_weight) {
                    $entryWeightForHeight = 'Overweight';
                } elseif ((float) $getHeight->obese <= (float)$request->entry_weight){
                    $entryWeightForHeight = 'Obese';
                }

            }

            if ($entryWeightForAge != 'Normal' || $entryHeightForAge != 'Normal' || $entryHeightForAge == 'Tall' || $entryWeightForHeight != 'Normal') {
                $entryIsMalnourished = true;
            } else {
                $entryIsMalnourished = false;
            }
            
            if ($entryWeightForAge != 'Normal' || $entryWeightForAge != 'Overweight' || $entryHeightForAge != 'Tall' || $entryHeightForAge != 'Normal' || $entryWeightForHeight != 'Normal' || $entryWeightForHeight != 'Overweight' || $entryWeightForHeight != 'Obese') {
                $entryIsUndernourished = true;
            } else {
                $entryIsUndernourished = false;
            }
            
        }
        
        $entryNutritionalStatus = NutritionalStatus::create([
            'child_id' => $request->child_id,
            'entry_weight' => $request->entry_weight,
            'entry_height' => $request->entry_height,
            'entry_actual_date_of_weighing' => $request->entry_actual_date_of_weighing,
            'entry_weight_for_age' => $entryWeightForAge,
            'entry_height_for_age' => $entryHeightForAge,
            'entry_weight_for_height' => $entryWeightForHeight,
            'entry_is_malnourish' => $entryIsMalnourished,
            'entry_is_undernourish' => $entryIsUndernourished,
            'created_by_user_id' => auth()->id(),
            'updated_by_user_id' => auth()->id(),   
        ]);

        return redirect()->route('nutritionalstatus.index', ['id' => $request->child_id])->with('success', 'Upon entry details saved successfully.');
    }

    

    public function storeExitDetails(UpdateNutritionalStatusRequest $request)
    {
    $validatedData = $request->validated();

    $exitRecord = NutritionalStatus::where('child_id', $request->child_id)->first();

    if ($exitRecord && !is_null($exitRecord->exit_weight) && !is_null($exitRecord->exit_height) && !is_null($exitRecord->exit_actual_date_of_weighing)) {
        return redirect()->back()->with(['error' => 'Exit details already exist for this child.']);
    }

    if ($exitRecord) {
        
            $exitWeightForAge = null;
            $exitHeightForAge = null;
            $exitWeightForHeight = null;
            $exitIsMalnourished = false;
            $exitIsUndernourished = false;

            $child = Child::with('nutritionalStatus', 'sex')
                ->where('id', $request->child_id)
                ->first();

            $childSex = $child->sex->id;
            $childBirthDate = Carbon::parse($child->date_of_birth);

            $exitWeighingDate = Carbon::parse($request->exit_actual_date_of_weighing);
            $exitAgeInMonths = $exitWeighingDate->diffInMonths($childBirthDate);

            //weight for age
            if ($childSex == '1'){
                $getAge = cgs_wfa_boys::where('age_month', $exitAgeInMonths)->first();
            
                if ((float) $getAge->severly_underweight >= (float)$request->exit_weight) {
                    $exitWeightForAge = 'Severely Underweight';
                } elseif ((float) $getAge->underweight_from <= (float)$request->exit_weight && (float) $getAge->underweight_to >= (float)$request->exit_weight) {
                    $exitWeightForAge = 'Underweight';
                } elseif ((float) $getAge->normal_from <= (float)$request->exit_weight && (float) $getAge->normal_to >= (float)$request->exit_weight) {
                    $exitWeightForAge = 'Normal';
                } else {
                    $exitWeightForAge = 'Overweight';
                }
            } else{
                $getAge = cgs_wfa_girls::where('age_month', $exitAgeInMonths)->first();
            
                if ((float) $getAge->severly_underweight >= (float)$request->exit_weight) {
                    $exitWeightForAge = 'Severely Underweight';
                } elseif ((float) $getAge->underweight_from <= (float)$request->exit_weight && (float) $getAge->underweight_to >= (float)$request->exit_weight) {
                    $exitWeightForAge = 'Underweight';
                } elseif ((float) $getAge->normal_from <= (float)$request->exit_weight && (float) $getAge->normal_to >= (float)$request->exit_weight) {
                    $exitWeightForAge = 'Normal';
                } else {
                    $exitWeightForAge = 'Overweight';
                }
            }

            //height for age
            if ($childSex == '1'){
                $getAge = cgs_hfa_boys::where('age_month', $exitAgeInMonths)->first();
            
                if ((float) $getAge->severly_stunted >= (float)$request->exit_height) {
                    $exitHeightForAge = 'Severely Stunted';
                } elseif ((float) $getAge->stunted_from <= (float)$request->exit_height && (float) $getAge->stunted_to >= (float)$request->exit_height) {
                    $exitHeightForAge = 'Stunted';
                } elseif ((float) $getAge->normal_from <= (float)$request->exit_height && (float) $getAge->normal_to >= (float)$request->exit_height) {
                    $exitHeightForAge = 'Normal';
                } elseif ((float) $getAge->severly_stunted <= (float)$request->exit_height) {
                    $exitWeightForAge = 'Tall';
                }
            } else{
                $getAge = cgs_hfa_girls::where('age_month', $exitAgeInMonths)->first();
            
                if ((float) $getAge->severly_stunted >= (float)$request->exit_height) {
                    $exitHeightForAge = 'Severely Stunted';
                } elseif ((float) $getAge->stunted_from <= (float)$request->exit_height && (float) $getAge->stunted_to >= (float)$request->exit_height) {
                    $exitHeightForAge = 'Stunted';
                } elseif ((float) $getAge->normal_from <= (float)$request->exit_height && (float) $getAge->normal_to >= (float)$request->exit_height) {
                    $exitHeightForAge = 'Normal';
                } elseif ((float) $getAge->severly_stunted <= (float)$request->exit_height) {
                    $exitHeightForAge = 'Tall';
                }
            }

            //weight for height
            if ($childSex == '1'){
                $getHeight = cgs_wfh_boys::where('length_in_cm', $request->exit_height)->first();
            
                if ((float) $getHeight->severly_wasted >= (float)$request->exit_weight) {
                    $exitWeightForHeight = 'Severely Stunted';
                } elseif ((float) $getHeight->wasted_from <= (float)$request->exit_weight && (float) $getHeight->wasted_to >= (float)$request->exit_weight) {
                    $exitWeightForHeight = 'Stunted';
                } elseif ((float) $getHeight->normal_from <= (float)$request->exit_weight && (float) $getHeight->normal_to >= (float)$request->exit_weight) {
                    $exitWeightForHeight = 'Normal';
                } elseif ((float) $getHeight->overweight_from <= (float)$request->exit_weight && (float) $getHeight->overweight_to >= (float)$request->exit_weight) {
                    $exitWeightForHeight = 'Stunted';
                } elseif ((float) $getHeight->obese <= (float)$request->exit_weight) {
                    $exitWeightForHeight = 'Obese';
                }
            } else{
                $getHeight = cgs_wfh_girls::where('length_in_cm', $request->exit_height)->first();
            
                if ((float) $getHeight->severly_stunted >= (float)$request->exit_weight) {
                    $exitWeightForHeight = 'Severely Stunted';
                } elseif ((float) $getHeight->stunted_from <= (float)$request->exit_weight && (float) $getHeight->stunted_to >= (float)$request->exit_weight) {
                    $exitWeightForHeight = 'Stunted';
                } elseif ((float) $getHeight->normal_from <= (float)$request->exit_weight && (float) $getHeight->normal_to >= (float)$request->exit_weight) {
                    $exitWeightForAge = 'Normal';
                } elseif ((float) $getHeight->overweight_from <= (float)$request->exit_weight && (float) $getHeight->overweight_to >= (float)$request->exit_weight) {
                    $exitWeightForHeight = 'Stunted';
                } elseif ((float) $getHeight->obese <= (float)$request->exit_weight) {
                    $exitWeightForHeight = 'Obese';
                }
            }

            if ($exitWeightForAge != 'Normal' || $exitHeightForAge != 'Normal' || $exitHeightForAge == 'Tall' || $exitWeightForHeight != 'Normal') {
                $exitIsMalnourished = true;
            } else {
                $exitIsMalnourished = false;
            }
            
            if ($exitWeightForAge != 'Normal' || $exitWeightForAge != 'Overweight' || $exitHeightForAge != 'Tall' || $exitHeightForAge != 'Normal' || $exitWeightForHeight != 'Normal' || $exitWeightForHeight != 'Overweight' || $exitWeightForHeight != 'Obese') {
                $exitIsUndernourished = true;
            } else {
                $exitIsUndernourished = false;
            }
        
        $exitRecord->update([
            'exit_weight' => $request->exit_weight,
            'exit_height' => $request->exit_height,
            'exit_actual_date_of_weighing' => $request->exit_actual_date_of_weighing,
            'exit_weight_for_age' => $exitWeightForAge,
            'exit_height_for_age' => $exitHeightForAge,
            'exit_weight_for_height' => $exitWeightForHeight,
            'exit_is_malnourish' => $exitIsMalnourished,
            'exit_is_undernourish' => $exitIsUndernourished,
            'updated_by_user_id' => auth()->id(),
        ]);

    } else {
        return redirect()->back()->with(['error' => 'No existing record found for this child.']);
    }

    return redirect()->route('nutritionalstatus.index', ['id' => $request->child_id])
                 ->with([
                     'success' => 'Exit details updated successfully.',
                     'exitDataSaved' => true
                 ]);
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
