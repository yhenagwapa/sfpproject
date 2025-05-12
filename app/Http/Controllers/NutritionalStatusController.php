<?php

namespace App\Http\Controllers;


use App\Models\ChildCenter;
use App\Models\Implementation;
use App\Models\NutritionalStatus;
use App\Models\cgs_wfa_girls;
use App\Models\cgs_wfa_boys;
use App\Models\cgs_hfa_girls;
use App\Models\cgs_hfa_boys;
use App\Models\cgs_wfh_girls;
use App\Models\cgs_wfh_boys;
use App\Http\Requests\StoreNutritionalStatusRequest;
use App\Http\Requests\UpdateNutritionalStatusRequest;
use Illuminate\Http\Request;
use App\Models\Child;
use App\Models\Sex;
use Carbon\Carbon;

class NutritionalStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-nutritional-status', ['only' => ['storeUponEntryDetails', 'storeExitDetails']]);
        $this->middleware('permission:edit-nutritional-status', ['only' => ['edit', 'updateUponEntryDetails', 'updateAfter120Details' ]]);
    }

    public function index(Request $request)
    {
        $childID = session('child_id');

        $implementation = Implementation::where('status', 'active')->first();

        $child = Child::findOrFail($childID);

        $entryData = NutritionalStatus::where('child_id', $childID)
            ->whereNotNull('weight')
            ->whereNotNull('height')
            ->whereNotNull('actual_weighing_date')
            ->get();

        $hasUponEntryData = false;
        $hasUponExitData = false;
        $entryDetails = null;
        $exitDetails = null;
        $entryWeighingDate = null;

        $count = $entryData->count();

        if ($count === 1) {
            $hasUponEntryData = true;
            $entryWeighingDate = $entryData[0]->actual_weighing_date;
            $entryDetails = $entryData[0];

        } elseif ($count === 2) {
            $hasUponEntryData = true;
            $hasUponExitData = true;
            $entryDetails = $entryData[0];
            $exitDetails = $entryData[1];
        }


        return view('nutritionalstatus.index', compact('child', 'implementation', 'entryWeighingDate', 'entryDetails', 'exitDetails', 'hasUponEntryData', 'hasUponExitData'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        session(['child_id' => $request->input('child_id')]);

        return redirect()->route('nutritionalstatus.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeUponEntryDetails(StoreNutritionalStatusRequest $request)
    {
        $validatedData = $request->validated();

        $childID = session('child_id');

        $entryWeightForAge = null;
        $entryHeightForAge = null;
        $entryWeightForHeight = null;
        $entryIsMalnourished = false;
        $entryIsUndernourished = false;

        $child = Child::with( 'sex')
            ->where('id', $childID)
            ->first();

        $childcenter = ChildCenter::with('implementation')
            ->where('child_id', $child->id)
            ->where('status', 'active')
            ->first();

        $childSex = $child->sex->id;

        $cycleID = $childcenter->implementation_id;

        $childMilkFeeding = $child->milk_feeding_id ? $child->milk_feeding_id : null;
        $childBirthDate = Carbon::parse($child->date_of_birth);

        $entryWeighingDate = Carbon::parse($request->weighing_date);
        $entryAgeInMonths = $entryWeighingDate->diffInMonths($childBirthDate);
        $entryAgeInYears = floor($entryAgeInMonths / 12);

        //weight for age
        if ($childSex == '1') {
            $getAge = cgs_wfa_boys::where('age_month', $entryAgeInMonths)->first();

            if(!$getAge){
                return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
            }

            if ((float) $request->weight <=(float) $getAge->severly_underweight) {
                $entryWeightForAge = 'Severely Underweight';
            } elseif ((float) $request->weight >= (float) $getAge->underweight_from && (float) $request->weight <= (float) $getAge->underweight_to) {
                $entryWeightForAge = 'Underweight';
            } elseif ((float) $request->weight >= (float) $getAge->normal_from && (float) $request->weight <= (float) $getAge->normal_to) {
                $entryWeightForAge = 'Normal';
            } elseif ((float) $request->weight >= (float) $request->weight) {
                $entryWeightForAge = 'Overweight';
            } else {
                $entryWeightForAge = 'Not Applicable';
            }

        } else {
            $getAge = cgs_wfa_girls::where('age_month', $entryAgeInMonths)->first();

            if(!$getAge){
                return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
            }

            if ((float) $request->weight <=(float) $getAge->severly_underweight) {
                $entryWeightForAge = 'Severely Underweight';
            } elseif ((float) $request->weight >= (float) $getAge->underweight_from && (float) $request->weight <= (float) $getAge->underweight_to) {
                $entryWeightForAge = 'Underweight';
            } elseif ((float) $request->weight >= (float) $getAge->normal_from && (float) $request->weight <= (float) $getAge->normal_to) {
                $entryWeightForAge = 'Normal';
            } elseif ((float) $request->weight >= (float) $request->weight) {
                $entryWeightForAge = 'Overweight';
            } else {
                $entryWeightForAge = 'Not Applicable';
            }
        }

        //height for age
        if ($childSex == '1') {
            $getAge = cgs_hfa_boys::where('age_month', $entryAgeInMonths)->first();

            if(!$getAge){
                return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
            }

            if ((float) $request->height <= (float) $getAge->severly_stunted) {
                $entryHeightForAge = 'Severely Stunted';
            } elseif ((float) $request->height >= (float) $getAge->stunted_from && (float) $request->height <= (float) $getAge->stunted_to) {
                $entryHeightForAge = 'Stunted';
            } elseif ((float) $request->height >= (float) $getAge->normal_from && (float) $request->height <= (float) $getAge->normal_to) {
                $entryHeightForAge = 'Normal';
            } elseif ((float) $request->height >= (float) $getAge->tall) {
                $entryHeightForAge = 'Tall';
            } else {
                $entryHeightForAge = 'Not Applicable';
            }

        } else {
            $getAge = cgs_hfa_girls::where('age_month', $entryAgeInMonths)->first();

            if(!$getAge){
                return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
            }

            if ((float) $request->height <= (float) $getAge->severly_stunted) {
                $entryHeightForAge = 'Severely Stunted';
            } elseif ((float) $request->height >= (float) $getAge->stunted_from && (float) $request->height <= (float) $getAge->stunted_to) {
                $entryHeightForAge = 'Stunted';
            } elseif ((float) $request->height >= (float) $getAge->normal_from && (float) $request->height <= (float) $getAge->normal_to) {
                $entryHeightForAge = 'Normal';
            } elseif ((float) $request->height >= (float) $getAge->tall) {
                $entryHeightForAge = 'Tall';
            } else {
                $entryHeightForAge = 'Not Applicable';
            }
        }

        //weight for height
        if ($childSex == '1') {
            $getHeight = cgs_wfh_boys::where('length_in_cm', $request->height)->first();

            if(!$getHeight){
                return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
            }

            if ((float) $request->weight <= (float) $getHeight->severly_wasted) {
                $entryWeightForHeight = 'Severely Wasted';
            } elseif ((float) $request->weight >= (float) $getHeight->wasted_from && (float) $request->weight <= (float) $getHeight->wasted_to) {
                $entryWeightForHeight = 'Wasted';
            } elseif ((float) $request->weight >= (float) $getHeight->normal_from && (float) $request->weight <= (float) $getHeight->normal_to) {
                $entryWeightForHeight = 'Normal';
            } elseif ((float) $request->weight >= (float) $getHeight->overweight_from && (float) (float) $request->weight <= $getHeight->overweight_to) {
                $entryWeightForHeight = 'Overweight';
            } elseif ((float) $request->weight >= (float) $getHeight->obese) {
                $entryWeightForHeight = 'Obese';
            } else {
                $entryWeightForHeight = 'Not Applicable';
            }

        } else {
            $getHeight = cgs_wfh_girls::where('length_in_cm', $request->height)->first();

            if(!$getHeight){
                return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
            }

            if ((float) $request->weight <= (float) $getHeight->severly_wasted) {
                $entryWeightForHeight = 'Severely Wasted';
            } elseif ((float) $request->weight >= (float) $getHeight->wasted_from && (float) $request->weight <= (float) $getHeight->wasted_to) {
                $entryWeightForHeight = 'Wasted';
            } elseif ((float) $request->weight >= (float) $getHeight->normal_from && (float) $request->weight <= (float) $getHeight->normal_to) {
                $entryWeightForHeight = 'Normal';
            } elseif ((float) $request->weight >= (float) $getHeight->overweight_from && (float) (float) $request->weight <= $getHeight->overweight_to) {
                $entryWeightForHeight = 'Overweight';
            } elseif ((float) $request->weight >= (float) $getHeight->obese) {
                $entryWeightForHeight = 'Obese';
            } else {
                $entryWeightForHeight = 'Not Applicable';
            }

        }

        $entryIsMalnourished = !(
            $entryWeightForAge === 'Normal' &&
            ($entryHeightForAge === 'Normal' || $entryHeightForAge === 'Tall') &&
            $entryWeightForHeight === 'Normal'
        );
        
        $entryIsUndernourished = !(
            in_array($entryWeightForAge, ['Normal', 'Overweight']) &&
            $entryHeightForAge === 'Normal' &&
            in_array($entryWeightForHeight, ['Normal', 'Overweight', 'Obese'])
        );
        

        $entryNutritionalStatus = NutritionalStatus::create([
            'implementation_id' => $cycleID,
            'child_id' => $request->child_id,
            'weight' => $request->weight,
            'height' => $request->height,
            'actual_weighing_date' => $request->actual_weighing_date,
            'age_in_months' => $entryAgeInMonths,
            'age_in_years' => $entryAgeInYears,
            'weight_for_age' => $entryWeightForAge,
            'height_for_age' => $entryHeightForAge,
            'weight_for_height' => $entryWeightForHeight,
            'is_malnourish' => $entryIsMalnourished,
            'is_undernourish' => $entryIsUndernourished,
            'deworming_date' => $request->deworming_date,
            'vitamin_a_date' => $request->vitamin_a_date,
            'created_by_user_id' => auth()->id(),
            'updated_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('nutritionalstatus.index')->with('success', 'Child nutritional status saved successfully.')->with('child_id', $request->input('child_id'));
    }

    public function storeExitDetails(Request $request)
    {

        $exitRecord = NutritionalStatus::where('child_id', $request->exitchild_id)->count();

        if ($exitRecord >= 2 ) {
            return redirect()->back()->with(['error' => 'Exit details already exist for this child.']);
        }

        if ($exitRecord) {

            $exitWeightForAge = null;
            $exitHeightForAge = null;
            $exitWeightForHeight = null;
            $exitIsMalnourished = false;
            $exitIsUndernourished = false;

            $child = Child::with( 'sex')
                ->where('id', $request->exitchild_id)
                ->first();

            $childcenter = ChildCenter::with('implementation')
                ->where('child_id', $child->id)
                ->where('status', 'active')
                ->first();

            $childSex = $child->sex->id;

            $cycleID = $childcenter->implementation_id;

            // $childMilkFeeding = $child->milk_feeding_id;
            $childBirthDate = Carbon::parse($child->date_of_birth);

            $exitWeighingDate = Carbon::parse($request->exitweighing_date);
            $exitAgeInMonths = $exitWeighingDate->diffInMonths($childBirthDate);
            $exitAgeInYears = floor($exitAgeInMonths / 12);

            //weight for age
            if ($childSex == '1') {
                $getAge = cgs_wfa_boys::where('age_month', $exitAgeInMonths)->first();

                if(!$getAge){
                    return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                }

                if ((float) $request->exitweight <= (float) $getAge->severly_underweight) {
                    $exitWeightForAge = 'Severely Underweight';
                } elseif ((float) $request->exitweight >= (float) $getAge->underweight_from && (float) $request->exitweight <= (float) $getAge->underweight_to) {
                    $exitWeightForAge = 'Underweight';
                } elseif ((float) $request->exitweight >= (float) $getAge->normal_from && (float) $request->exitweight <= (float) $getAge->normal_to) {
                    $exitWeightForAge = 'Normal';
                } elseif ((float) $request->exitweight >= (float) $request->exitweight) {
                    $exitWeightForAge = 'Overweight';
                } else {
                    $exitWeightForAge = 'Not Applicable';
                }

            } else {
                $getAge = cgs_wfa_girls::where('age_month', $exitAgeInMonths)->first();

                if(!$getAge){
                    return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                }

                if ((float) $request->exitweight <= (float) $getAge->severly_underweight) {
                    $exitWeightForAge = 'Severely Underweight';
                } elseif ((float) $request->exitweight >= (float) $getAge->underweight_from && (float) $request->exitweight <= (float) $getAge->underweight_to) {
                    $exitWeightForAge = 'Underweight';
                } elseif ((float) $request->exitweight >= (float) $getAge->normal_from && (float) $request->exitweight <= (float) $getAge->normal_to) {
                    $exitWeightForAge = 'Normal';
                } elseif ((float) $request->exitweight >= (float) $request->exitweight) {
                    $exitWeightForAge = 'Overweight';
                } else {
                    $exitWeightForAge = 'Not Applicable';
                }
            }

            //height for age
            if ($childSex == '1') {
                $getAge = cgs_hfa_boys::where('age_month', $exitAgeInMonths)->first();

                if(!$getAge){
                    return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                }

                if ((float) $request->exitheight <= (float) $getAge->severly_stunted) {
                    $exitHeightForAge = 'Severely Stunted';
                } elseif ((float) $request->exitheight >= (float) $getAge->stunted_from && (float) $request->exitheight <= (float) $getAge->stunted_to) {
                    $exitHeightForAge = 'Stunted';
                } elseif ((float) $request->exitheight >= (float) $getAge->normal_from && (float) $request->exitheight <= (float) $getAge->normal_to) {
                    $exitHeightForAge = 'Normal';
                } elseif ((float) $request->exitheight >= (float) $getAge->tall) {
                    $exitHeightForAge = 'Tall';
                } else {
                    $exitHeightForAge = 'Not Applicable';
                }
            } else {
                $getAge = cgs_hfa_girls::where('age_month', $exitAgeInMonths)->first();

                if(!$getAge){
                    return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                }

                if ((float) $request->exitheight <= (float) $getAge->severly_stunted) {
                    $exitHeightForAge = 'Severely Stunted';
                } elseif ((float) $request->exitheight >= (float) $getAge->stunted_from && (float) $request->exitheight <= (float) $getAge->stunted_to) {
                    $exitHeightForAge = 'Stunted';
                } elseif ((float) $request->exitheight >= (float) $getAge->normal_from && (float) $request->exitheight <= (float) $getAge->normal_to) {
                    $exitHeightForAge = 'Normal';
                } elseif ((float) $request->exitheight >= (float) $getAge->tall) {
                    $exitHeightForAge = 'Tall';
                } else {
                    $exitHeightForAge = 'Not Applicable';
                }
            }

            //weight for height
            if ($childSex == '1') {
                $getHeight = cgs_wfh_boys::where('length_in_cm', $request->exitheight)->first();

                if(!$getHeight){
                    return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                }

                if ((float) $request->exitweight <= (float) $getHeight->severly_wasted) {
                    $exitWeightForHeight = 'Severely Wasted';
                } elseif ((float) $request->exitweight >= (float) $getHeight->wasted_from && (float) $request->exitweight <= (float) $getHeight->wasted_to) {
                    $exitWeightForHeight = 'Wasted';
                } elseif ((float) $request->exitweight >= (float) $getHeight->normal_from && (float) $request->exitweight <= (float) $getHeight->normal_to) {
                    $exitWeightForHeight = 'Normal';
                } elseif ((float) $request->exitweight >= (float) $getHeight->overweight_from && (float) $request->exitweight <= (float) $getHeight->overweight_to) {
                    $exitWeightForHeight = 'Overweight';
                } elseif ((float) $request->exitweight >= (float) $getHeight->obese) {
                    $exitWeightForHeight = 'Obese';
                } else {
                    $exitWeightForHeight = 'Not Applicable';
                }

            } else {
                $getHeight = cgs_wfh_girls::where('length_in_cm', $request->exitheight)->first();

                if(!$getHeight){
                    return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                }

                if ((float) $request->exitweight <= (float) $getHeight->severly_wasted) {
                    $exitWeightForHeight = 'Severely Wasted';
                } elseif ((float) $request->exitweight >= (float) $getHeight->wasted_from && (float) $request->exitweight <= (float) $getHeight->wasted_to) {
                    $exitWeightForHeight = 'Wasted';
                } elseif ((float) $request->exitweight >= (float) $getHeight->normal_from && (float) $request->exitweight <= (float) $getHeight->normal_to) {
                    $exitWeightForHeight = 'Normal';
                } elseif ((float) $request->exitweight >= (float) $getHeight->overweight_from && (float) $request->exitweight <= (float) $getHeight->overweight_to) {
                    $exitWeightForHeight = 'Overweight';
                } elseif ((float) $request->exitweight >= (float) $getHeight->obese) {
                    $exitWeightForHeight = 'Obese';
                } else {
                    $exitWeightForHeight = 'Not Applicable';
                }
            }

            $exitIsMalnourished = !(
                $exitWeightForAge === 'Normal' &&
                ($exitHeightForAge === 'Normal' || $exitHeightForAge === 'Tall') &&
                $exitWeightForHeight === 'Normal'
            );
            
            $exitIsUndernourished = !(
                in_array($exitWeightForAge, ['Normal', 'Overweight']) &&
                $exitHeightForAge === 'Normal' &&
                in_array($exitWeightForHeight, ['Normal', 'Overweight', 'Obese'])
            );
            

            $exitNutritionalStatus = NutritionalStatus::create([
                'implementation_id' => $cycleID,
                'child_id' => $request->exitchild_id,
                'weight' => $request->exitweight,
                'height' => $request->exitheight,
                'actual_weighing_date' => $request->exitweighing_date,
                'age_in_months' => $exitAgeInMonths,
                'age_in_years' => $exitAgeInYears,
                'weight_for_age' => $exitWeightForAge,
                'height_for_age' => $exitHeightForAge,
                'weight_for_height' => $exitWeightForHeight,
                'is_malnourish' => $exitIsMalnourished,
                'is_undernourish' => $exitIsUndernourished,
                'created_by_user_id' => auth()->id(),
                'updated_by_user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('nutritionalstatus.index')->with('success', 'Child nutritional status updated successfully.')->with('child_id', $request->input('exitchild_id'));
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        session(['child_id' => $request->input('child_id')]);
        session(['exitchild_id' => $request->input('child_id')]);

        return redirect()->route('nutritionalstatus.edit');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $childID = session('child_id');

        $implementation = NutritionalStatus::with('implementation')->first();

        $child = Child::findOrFail($childID);

        $entryData = NutritionalStatus::where('child_id', $childID)
            ->whereNotNull('weight')
            ->whereNotNull('height')
            ->whereNotNull('actual_weighing_date')
            ->get();

        $hasUponEntryData = false;
        $entryDetails = null;
        $hasUponExitData = false;
        $exitDetails = null;

        $count = $entryData->count();

        if ($count === 1) {
            $hasUponEntryData = true;
            $entryDetails = $entryData[0];

            $hasUponExitData = false;

        } elseif ($count === 2) {
            $hasUponEntryData = true;
            $entryDetails = $entryData[0];

            $hasUponExitData = true;
            $exitDetails = $entryData[1];
        }

        return view('nutritionalstatus.edit', compact('implementation', 'child', 'entryDetails', 'hasUponEntryData', 'exitDetails', 'hasUponExitData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateUponEntryDetails(UpdateNutritionalStatusRequest $request)
    {
        $validatedData = $request->validated();

        $childID = $validatedData['child_id'];

        $nutritionalStatus = NutritionalStatus::where('child_id', $childID)->first();

        $nutritionalStatus->fill($request->only(['child_id', 'weight', 'height','actual_weighing_date', 'deworming_date', 'vitamin_a_date']));

        if($nutritionalStatus->isClean()){
            return redirect()->route('nutritionalstatus.index')->with('warning', 'No changes were made.');
        }

        $entryWeightForAge = null;
        $entryHeightForAge = null;
        $entryWeightForHeight = null;
        $entryIsMalnourished = false;
        $entryIsUndernourished = false;

        $child = NutritionalStatus::with( 'child')
            ->where('child_id', $childID)
            ->orderBy('created_at')
            ->first();

        $childInfo = Child::findOrFail($child->child_id);
        $childSex = Sex::where('id', $childInfo->sex_id)
            ->first();

        $childBirthDate = Carbon::parse($childInfo->date_of_birth);

        $entryWeighingDate = Carbon::parse($request->actual_weighing_date);
        $entryAgeInMonths = $entryWeighingDate->diffInMonths($childBirthDate);
        $entryAgeInYears = floor($entryAgeInMonths / 12);

        //weight for age
        if ($childSex == '1') {
            $getAge = cgs_wfa_boys::where('age_month', $entryAgeInMonths)->first();

            if(!$getAge){
                return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
            }

            if ((float) $request->weight <=(float) $getAge->severly_underweight) {
                $entryWeightForAge = 'Severely Underweight';
            } elseif ((float) $request->weight >= (float) $getAge->underweight_from && (float) $request->weight <= (float) $getAge->underweight_to) {
                $entryWeightForAge = 'Underweight';
            } elseif ((float) $request->weight >= (float) $getAge->normal_from && (float) $request->weight <= (float) $getAge->normal_to) {
                $entryWeightForAge = 'Normal';
            } elseif ((float) $request->weight >= (float) $request->weight) {
                $entryWeightForAge = 'Overweight';
            } else {
                $entryWeightForAge = 'Not Applicable';
            }

        } else {
            $getAge = cgs_wfa_girls::where('age_month', $entryAgeInMonths)->first();

            if(!$getAge){
                return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
            }

            if ((float) $request->weight <=(float) $getAge->severly_underweight) {
                $entryWeightForAge = 'Severely Underweight';
            } elseif ((float) $request->weight >= (float) $getAge->underweight_from && (float) $request->weight <= (float) $getAge->underweight_to) {
                $entryWeightForAge = 'Underweight';
            } elseif ((float) $request->weight >= (float) $getAge->normal_from && (float) $request->weight <= (float) $getAge->normal_to) {
                $entryWeightForAge = 'Normal';
            } elseif ((float) $request->weight >= (float) $request->weight) {
                $entryWeightForAge = 'Overweight';
            } else {
                $entryWeightForAge = 'Not Applicable';
            }
        }

        //height for age
        if ($childSex == '1') {
            $getAge = cgs_hfa_boys::where('age_month', $entryAgeInMonths)->first();

            if(!$getAge){
                return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
            }

            if ((float) $request->height <= (float) $getAge->severly_stunted) {
                $entryHeightForAge = 'Severely Stunted';
            } elseif ((float) $request->height >= (float) $getAge->stunted_from && (float) $request->height <= (float) $getAge->stunted_to) {
                $entryHeightForAge = 'Stunted';
            } elseif ((float) $request->height >= (float) $getAge->normal_from && (float) $request->height <= (float) $getAge->normal_to) {
                $entryHeightForAge = 'Normal';
            } elseif ((float) $request->height >= (float) $getAge->tall) {
                $entryHeightForAge = 'Tall';
            } else {
                $entryHeightForAge = 'Not Applicable';
            }

        } else {
            $getAge = cgs_hfa_girls::where('age_month', $entryAgeInMonths)->first();

            if(!$getAge){
                return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
            }

            if ((float) $request->height <= (float) $getAge->severly_stunted) {
                $entryHeightForAge = 'Severely Stunted';
            } elseif ((float) $request->height >= (float) $getAge->stunted_from && (float) $request->height <= (float) $getAge->stunted_to) {
                $entryHeightForAge = 'Stunted';
            } elseif ((float) $request->height >= (float) $getAge->normal_from && (float) $request->height <= (float) $getAge->normal_to) {
                $entryHeightForAge = 'Normal';
            } elseif ((float) $request->height >= (float) $getAge->tall) {
                $entryHeightForAge = 'Tall';
            } else {
                $entryHeightForAge = 'Not Applicable';
            }
        }

        //weight for height
        if ($childSex == '1') {
            $getHeight = cgs_wfh_boys::where('length_in_cm', $request->height)->first();

            if(!$getHeight){
                return redirect()->back()->withErrors(['ageError' => 'Height is out of range.']);
            }

            if ((float) $request->weight <= (float) $getHeight->severly_wasted) {
                $entryWeightForHeight = 'Severely Wasted';
            } elseif ((float) $request->weight >= (float) $getHeight->wasted_from && (float) $request->weight <= (float) $getHeight->wasted_to) {
                $entryWeightForHeight = 'Wasted';
            } elseif ((float) $request->weight >= (float) $getHeight->normal_from && (float) $request->weight <= (float) $getHeight->normal_to) {
                $entryWeightForHeight = 'Normal';
            } elseif ((float) $request->weight >= (float) $getHeight->overweight_from && (float) (float) $request->weight <= $getHeight->overweight_to) {
                $entryWeightForHeight = 'Overweight';
            } elseif ((float) $request->weight >= (float) $getHeight->obese) {
                $entryWeightForHeight = 'Obese';
            } else {
                $entryWeightForHeight = 'Not Applicable';
            }

        } else {
            $getHeight = cgs_wfh_girls::where('length_in_cm', $request->height)->first();

            if(!$getHeight){
                return redirect()->back()->withErrors(['ageError' => 'Height is out of range.']);
            }

            if ((float) $request->weight <= (float) $getHeight->severly_wasted) {
                $entryWeightForHeight = 'Severely Wasted';
            } elseif ((float) $request->weight >= (float) $getHeight->wasted_from && (float) $request->weight <= (float) $getHeight->wasted_to) {
                $entryWeightForHeight = 'Wasted';
            } elseif ((float) $request->weight >= (float) $getHeight->normal_from && (float) $request->weight <= (float) $getHeight->normal_to) {
                $entryWeightForHeight = 'Normal';
            } elseif ((float) $request->weight >= (float) $getHeight->overweight_from && (float) (float) $request->weight <= $getHeight->overweight_to) {
                $entryWeightForHeight = 'Overweight';
            } elseif ((float) $request->weight >= (float) $getHeight->obese) {
                $entryWeightForHeight = 'Obese';
            } else {
                $entryWeightForHeight = 'Not Applicable';
            }

        }

        $entryIsMalnourished = !(
            $entryWeightForAge === 'Normal' &&
            ($entryHeightForAge === 'Normal' || $entryHeightForAge === 'Tall') &&
            $entryWeightForHeight === 'Normal'
        );
        
        $entryIsUndernourished = !(
            in_array($entryWeightForAge, ['Normal', 'Overweight']) &&
            $entryHeightForAge === 'Normal' &&
            in_array($entryWeightForHeight, ['Normal', 'Overweight', 'Obese'])
        );        

        $nutritionalStatus->age_in_months = $entryAgeInMonths;
        $nutritionalStatus->age_in_years = $entryAgeInYears;
        $nutritionalStatus->weight_for_age = $entryWeightForAge;
        $nutritionalStatus->height_for_age = $entryHeightForAge;
        $nutritionalStatus->weight_for_height = $entryWeightForHeight;
        $nutritionalStatus->is_malnourish = $entryIsMalnourished;
        $nutritionalStatus->is_undernourish = $entryIsUndernourished;
        $nutritionalStatus->updated_by_user_id = auth()->id();

        $nutritionalStatus->update();

        // re-save the child
        session(['child_id' => $request->input('child_id')]);

        // return redirect()->route('nutritionalstatus.index')->with('success', 'Child nutritional status saved successfully.')->with('child_id', $request->input('child_id'));
        return redirect()->route('nutritionalstatus.index')->with('success', 'Child nutritional status updated successfully.')->with('child_id', $request->input('child_id'));

    }
    public function updateAfter120Details(UpdateNutritionalStatusRequest $request)
    {
        $validatedData = $request->validated();

        $childID = session('exitchild_id');

        $nutritionalStatus = NutritionalStatus::where('child_id', $childID)
                ->orderBy('created_at')
                ->skip(1)
                ->take(1)
                ->first();

        $nutritionalStatus->fill([
            'child_id' => $request->input('exitchild_id'),
            'weight' => $request->input('exitweight'),
            'height' => $request->input('exitheight'),
            'actual_weighing_date' =>$request->input('exitweighing_date')
        ]);

        if($nutritionalStatus->isClean()){
            return redirect()->route('nutritionalstatus.index')->with('warning', 'No changes were made.');
        }

            $exitWeightForAge = null;
            $exitHeightForAge = null;
            $exitWeightForHeight = null;
            $exitIsMalnourished = false;
            $exitIsUndernourished = false;

            $child = NutritionalStatus::with( 'child')
                ->where('child_id', $childID)
                ->orderBy('created_at')
                ->skip(1)
                ->take(1)
                ->first();

                // dd('child id',$child->child_id);

            $childInfo = Child::findOrFail($child->child_id);
            $childSex = Sex::where('id', $childInfo->sex_id)
                ->first();

            $childBirthDate = Carbon::parse($childInfo->date_of_birth);

            $exitWeighingDate = Carbon::parse($request->exitweighing_date);

            $exitAgeInMonths = $exitWeighingDate->diffInMonths($childBirthDate);
            $exitAgeInYears = floor($exitAgeInMonths / 12);

            //weight for age
            if ($childSex == '1') {
                $getAge = cgs_wfa_boys::where('age_month', $exitAgeInMonths)->first();

                if(!$getAge){
                    return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                }

                if ((float) $request->exitweight <= (float) $getAge->severly_underweight) {
                    $exitWeightForAge = 'Severely Underweight';
                } elseif ((float) $request->exitweight >= (float) $getAge->underweight_from && (float) $request->exitweight <= (float) $getAge->underweight_to) {
                    $exitWeightForAge = 'Underweight';
                } elseif ((float) $request->exitweight >= (float) $getAge->normal_from && (float) $request->exitweight <= (float) $getAge->normal_to) {
                    $exitWeightForAge = 'Normal';
                } elseif ((float) $request->exitweight >= (float) $request->exitweight) {
                    $exitWeightForAge = 'Overweight';
                } else {
                    $exitWeightForAge = 'Not Applicable';
                }

            } else {
                $getAge = cgs_wfa_girls::where('age_month', $exitAgeInMonths)->first();

                if(!$getAge){
                    return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                }

                if ((float) $request->exitweight <= (float) $getAge->severly_underweight) {
                    $exitWeightForAge = 'Severely Underweight';
                } elseif ((float) $request->exitweight >= (float) $getAge->underweight_from && (float) $request->exitweight <= (float) $getAge->underweight_to) {
                    $exitWeightForAge = 'Underweight';
                } elseif ((float) $request->exitweight >= (float) $getAge->normal_from && (float) $request->exitweight <= (float) $getAge->normal_to) {
                    $exitWeightForAge = 'Normal';
                } elseif ((float) $request->exitweight >= (float) $request->exitweight) {
                    $exitWeightForAge = 'Overweight';
                } else {
                    $exitWeightForAge = 'Not Applicable';
                }
            }

            //height for age
            if ($childSex == '1') {
                $getAge = cgs_hfa_boys::where('age_month', $exitAgeInMonths)->first();

                if(!$getAge){
                    return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                }

                if ((float) $request->exitheight <= (float) $getAge->severly_stunted) {
                    $exitHeightForAge = 'Severely Stunted';
                } elseif ((float) $request->exitheight >= (float) $getAge->stunted_from && (float) $request->exitheight <= (float) $getAge->stunted_to) {
                    $exitHeightForAge = 'Stunted';
                } elseif ((float) $request->exitheight >= (float) $getAge->normal_from && (float) $request->exitheight <= (float) $getAge->normal_to) {
                    $exitHeightForAge = 'Normal';
                } elseif ((float) $request->exitheight >= (float) $getAge->tall) {
                    $exitHeightForAge = 'Tall';
                } else {
                    $exitHeightForAge = 'Not Applicable';
                }
            } else {
                $getAge = cgs_hfa_girls::where('age_month', $exitAgeInMonths)->first();

                if(!$getAge){
                    return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                }

                if ((float) $request->exitheight <= (float) $getAge->severly_stunted) {
                    $exitHeightForAge = 'Severely Stunted';
                } elseif ((float) $request->exitheight >= (float) $getAge->stunted_from && (float) $request->exitheight <= (float) $getAge->stunted_to) {
                    $exitHeightForAge = 'Stunted';
                } elseif ((float) $request->exitheight >= (float) $getAge->normal_from && (float) $request->exitheight <= (float) $getAge->normal_to) {
                    $exitHeightForAge = 'Normal';
                } elseif ((float) $request->exitheight >= (float) $getAge->tall) {
                    $exitHeightForAge = 'Tall';
                } else {
                    $exitHeightForAge = 'Not Applicable';
                }
            }

            //weight for height
            if ($childSex == '1') {
                $getHeight = cgs_wfh_boys::where('length_in_cm', $request->exitheight)->first();

                if(!$getHeight){
                    return redirect()->back()->withErrors(['ageError' => 'Height is out of range.']);
                }

                if ((float) $request->exitweight <= (float) $getHeight->severly_wasted) {
                    $exitWeightForHeight = 'Severely Wasted';
                } elseif ((float) $request->exitweight >= (float) $getHeight->wasted_from && (float) $request->exitweight <= (float) $getHeight->wasted_to) {
                    $exitWeightForHeight = 'Wasted';
                } elseif ((float) $request->exitweight >= (float) $getHeight->normal_from && (float) $request->exitweight <= (float) $getHeight->normal_to) {
                    $exitWeightForHeight = 'Normal';
                } elseif ((float) $request->exitweight >= (float) $getHeight->overweight_from && (float) $request->exitweight <= (float) $getHeight->overweight_to) {
                    $exitWeightForHeight = 'Overweight';
                } elseif ((float) $request->exitweight >= (float) $getHeight->obese) {
                    $exitWeightForHeight = 'Obese';
                } else {
                    $exitWeightForHeight = 'Not Applicable';
                }

            } else {
                $getHeight = cgs_wfh_girls::where('length_in_cm', $request->exitheight)->first();

                if(!$getHeight){
                    return redirect()->back()->withErrors(['ageError' => 'Height is out of range.']);
                }

                if ((float) $request->exitweight <= (float) $getHeight->severly_wasted) {
                    $exitWeightForHeight = 'Severely Wasted';
                } elseif ((float) $request->exitweight >= (float) $getHeight->wasted_from && (float) $request->exitweight <= (float) $getHeight->wasted_to) {
                    $exitWeightForHeight = 'Wasted';
                } elseif ((float) $request->exitweight >= (float) $getHeight->normal_from && (float) $request->exitweight <= (float) $getHeight->normal_to) {
                    $exitWeightForHeight = 'Normal';
                } elseif ((float) $request->exitweight >= (float) $getHeight->overweight_from && (float) $request->exitweight <= (float) $getHeight->overweight_to) {
                    $exitWeightForHeight = 'Overweight';
                } elseif ((float) $request->exitweight >= (float) $getHeight->obese) {
                    $exitWeightForHeight = 'Obese';
                } else {
                    $exitWeightForHeight = 'Not Applicable';
                }
            }

            $exitIsMalnourished = !(
                $exitWeightForAge === 'Normal' &&
                ($exitHeightForAge === 'Normal' || $exitHeightForAge === 'Tall') &&
                $exitWeightForHeight === 'Normal'
            );
            
            $exitIsUndernourished = !(
                in_array($exitWeightForAge, ['Normal', 'Overweight']) &&
                $exitHeightForAge === 'Normal' &&
                in_array($exitWeightForHeight, ['Normal', 'Overweight', 'Obese'])
            );
            

            $nutritionalStatus->age_in_months = $exitAgeInMonths;
            $nutritionalStatus->age_in_years = $exitAgeInYears;
            $nutritionalStatus->weight_for_age = $exitWeightForAge;
            $nutritionalStatus->height_for_age = $exitHeightForAge;
            $nutritionalStatus->weight_for_height = $exitWeightForHeight;
            $nutritionalStatus->is_malnourish = $exitIsMalnourished;
            $nutritionalStatus->is_undernourish = $exitIsUndernourished;
            $nutritionalStatus->updated_by_user_id = auth()->id();

            $nutritionalStatus->update();

            session(['exitchild_id' => $request->input('child_id')]);

        return redirect()->route('nutritionalstatus.index')->with('success', 'Child nutritional status updated successfully.')->with('child_id', $request->input('exitchild_id'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NutritionalStatus $nutritionalStatus)
    {
        //
    }
}
