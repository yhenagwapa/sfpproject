<?php

namespace App\Http\Controllers;


use App\Models\CycleImplementation;
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
            ->whereNotNull('weight')
            ->whereNotNull('height')
            ->whereNotNull('weighing_date')
            ->get();

        $hasUponEntryData = false;
        $hasUponExitData = false;
        $entryDetails = null;
        $exitDetails = null;

        $count = $entryData->count();

        if ($count === 1) {
            $hasUponEntryData = true;
            $entryDetails = $entryData[0];
            
        } elseif ($count === 2) {
            $hasUponEntryData = true;
            $hasUponExitData = true;
            $entryDetails = $entryData[0];
            $exitDetails = $entryData[1];
        }


        return view('nutritionalstatus.index', compact('child', 'entryDetails', 'exitDetails', 'hasUponEntryData', 'hasUponExitData'));
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

        $entryWeightForAge = null;
        $entryHeightForAge = null;
        $entryWeightForHeight = null;
        $entryIsMalnourished = false;
        $entryIsUndernourished = false;

        $child = Child::with( 'sex')
            ->where('id', $request->child_id)
            ->first();

        $childSex = $child->sex->id;

        $cycleID = $child->cycle_implementation_id;
        $childMilkFeeding = $child->milk_feeding_id;
        $childBirthDate = Carbon::parse($child->date_of_birth);

        $entryWeighingDate = Carbon::parse($request->weighing_date);
        $entryAgeInMonths = $entryWeighingDate->diffInMonths($childBirthDate);
        $entryAgeInYears = floor($entryAgeInMonths / 12);

        //weight for age
        if ($childSex == '1') {
            $getAge = cgs_wfa_boys::where('age_month', $entryAgeInMonths)->first();

            if ((float) $getAge->severly_underweight >= (float) $request->weight) {
                $entryWeightForAge = 'Severely Underweight';
            } elseif ((float) $getAge->underweight_from <= (float) $request->weight && (float) $getAge->underweight_to >= (float) $request->weight) {
                $entryWeightForAge = 'Underweight';
            } elseif ((float) $getAge->normal_from <= (float) $request->weight && (float) $getAge->normal_to >= (float) $request->weight) {
                $entryWeightForAge = 'Normal';
            } elseif ((float) $request->weight > (float) $getAge->normal_to) {
                $entryWeightForAge = 'Overweight';
            }
        } else {
            $getAge = cgs_wfa_girls::where('age_month', $entryAgeInMonths)->first();

            if ((float) $getAge->severly_underweight >= (float) $request->weight) {
                $entryWeightForAge = 'Severely Underweight';
            } elseif ((float) $getAge->underweight_from <= (float) $request->weight && (float) $getAge->underweight_to >= (float) $request->weight) {
                $entryWeightForAge = 'Underweight';
            } elseif ((float) $getAge->normal_from <= (float) $request->weight && (float) $getAge->normal_to >= (float) $request->weight) {
                $entryWeightForAge = 'Normal';
            } elseif ((float) $request->weight > (float) $getAge->normal_to) {
                $entryWeightForAge = 'Overweight';
            }
        }

        //height for age
        if ($childSex == '1') {
            $getAge = cgs_hfa_boys::where('age_month', $entryAgeInMonths)->first();

            if ((float) $getAge->severly_stunted >= (float) $request->height) {
                $entryHeightForAge = 'Severely Stunted';
            } elseif ((float) $getAge->stunted_from <= (float) $request->height && (float) $getAge->stunted_to >= (float) $request->height) {
                $entryHeightForAge = 'Stunted';
            } elseif ((float) $getAge->normal_from <= (float) $request->height && (float) $getAge->normal_to >= (float) $request->height) {
                $entryHeightForAge = 'Normal';
            } elseif ((float) $getAge->tall <= (float) $request->height) {
                $entryHeightForAge = 'Tall';
            }
        } else {
            $getAge = cgs_hfa_girls::where('age_month', $entryAgeInMonths)->first();

            if ((float) $getAge->severly_stunted >= (float) $request->height) {
                $entryHeightForAge = 'Severely Stunted';
            } elseif ((float) $getAge->stunted_from <= (float) $request->height && (float) $getAge->stunted_to >= (float) $request->height) {
                $entryHeightForAge = 'Stunted';
            } elseif ((float) $getAge->normal_from <= (float) $request->height && (float) $getAge->normal_to >= (float) $request->height) {
                $entryHeightForAge = 'Normal';
            } elseif ((float) $getAge->tall <= (float) $request->height) {
                $entryHeightForAge = 'Tall';
            }
        }

        //weight for height
        if ($childSex == '1') {
            $getHeight = cgs_wfh_boys::where('length_in_cm', $request->height)->first();

            if ((float) $getHeight->severly_wasted >= (float) $request->weight) {
                $entryWeightForHeight = 'Severely Wasted';
            } elseif ((float) $getHeight->wasted_from <= (float) $request->weight && (float) $getHeight->wasted_to >= (float) $request->weight) {
                $entryWeightForHeight = 'Wasted';
            } elseif ((float) $getHeight->normal_from <= (float) $request->weight && (float) $getHeight->normal_to >= (float) $request->weight) {
                $entryWeightForHeight = 'Normal';
            } elseif ((float) $getHeight->overweight_from <= (float) $request->weight && (float) $getHeight->overweight_to >= (float) $request->weight) {
                $entryWeightForHeight = 'Overweight';
            } elseif ((float) $getHeight->obese <= (float) $request->weight) {
                $entryWeightForHeight = 'Obese';
            }
        } else {
            $getHeight = cgs_wfh_girls::where('length_in_cm', $request->height)->first();

            if ((float) $getHeight->severly_wasted >= (float) $request->weight) {
                $entryWeightForHeight = 'Severely Wasted';
            } elseif ((float) $getHeight->wasted_from <= (float) $request->weight && (float) $getHeight->wasted_to >= (float) $request->weight) {
                $entryWeightForHeight = 'Wasted';
            } elseif ((float) $getHeight->normal_from <= (float) $request->weight && (float) $getHeight->normal_to >= (float) $request->weight) {
                $entryWeightForHeight = 'Normal';
            } elseif ((float) $getHeight->overweight_from <= (float) $request->weight && (float) $getHeight->overweight_to >= (float) $request->weight) {
                $entryWeightForHeight = 'Overweight';
            } elseif ((float) $getHeight->obese <= (float) $request->weight) {
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



        $entryNutritionalStatus = NutritionalStatus::create([
            'cycle_implementation_id' => $cycleID,
            'milk_feeding_id' => $childMilkFeeding,
            'child_id' => $request->child_id,
            'weight' => $request->weight,
            'height' => $request->height,
            'weighing_date' => $request->weighing_date,
            'age_in_months' => $entryAgeInMonths,
            'age_in_years' => $entryAgeInYears,
            'weight_for_age' => $entryWeightForAge,
            'height_for_age' => $entryHeightForAge,
            'weight_for_height' => $entryWeightForHeight,
            'is_malnourish' => $entryIsMalnourished,
            'is_undernourish' => $entryIsUndernourished,
            'created_by_user_id' => auth()->id(),
            'updated_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('nutritionalstatus.index', ['id' => $request->child_id])->with('success', 'Upon entry details saved successfully.');
    }



    public function storeExitDetails(UpdateNutritionalStatusRequest $request)
    {
        $validatedData = $request->validated();

        $exitRecord = NutritionalStatus::where('child_id', $request->child_id)->count();

        if ($exitRecord >= 2 ) {
            return redirect()->back()->with(['error' => 'Exit details already exist for this child.']);
        }

        if ($exitRecord) { $validatedData = $request->validated();

            $entryWeightForAge = null;
            $entryHeightForAge = null;
            $entryWeightForHeight = null;
            $entryIsMalnourished = false;
            $entryIsUndernourished = false;

            $child = Child::with( 'sex')
                ->where('id', $request->child_id)
                ->first();

            $childSex = $child->sex->id;

            $cycleID = $child->cycle_implementation_id;
            $childMilkFeeding = $child->milk_feeding_id;
            $childBirthDate = Carbon::parse($child->date_of_birth);

            $entryWeighingDate = Carbon::parse($request->weighing_date);
            $entryAgeInMonths = $entryWeighingDate->diffInMonths($childBirthDate);
            $entryAgeInYears = $entryAgeInMonths / 12;

            //weight for age
            if ($childSex == '1') {
                $getAge = cgs_wfa_boys::where('age_month', $entryAgeInMonths)->first();

                if ((float) $getAge->severly_underweight >= (float) $request->weight) {
                    $entryWeightForAge = 'Severely Underweight';
                } elseif ((float) $getAge->underweight_from <= (float) $request->weight && (float) $getAge->underweight_to >= (float) $request->weight) {
                    $entryWeightForAge = 'Underweight';
                } elseif ((float) $getAge->normal_from <= (float) $request->weight && (float) $getAge->normal_to >= (float) $request->weight) {
                    $entryWeightForAge = 'Normal';
                } elseif ((float) $request->weight > (float) $getAge->normal_to) {
                    $entryWeightForAge = 'Overweight';
                }
            } else {
                $getAge = cgs_wfa_girls::where('age_month', $entryAgeInMonths)->first();

                if ((float) $getAge->severly_underweight >= (float) $request->weight) {
                    $entryWeightForAge = 'Severely Underweight';
                } elseif ((float) $getAge->underweight_from <= (float) $request->weight && (float) $getAge->underweight_to >= (float) $request->weight) {
                    $entryWeightForAge = 'Underweight';
                } elseif ((float) $getAge->normal_from <= (float) $request->weight && (float) $getAge->normal_to >= (float) $request->weight) {
                    $entryWeightForAge = 'Normal';
                } elseif ((float) $request->weight > (float) $getAge->normal_to) {
                    $entryWeightForAge = 'Overweight';
                }
            }

            //height for age
            if ($childSex == '1') {
                $getAge = cgs_hfa_boys::where('age_month', $entryAgeInMonths)->first();

                if ((float) $getAge->severly_stunted >= (float) $request->height) {
                    $entryHeightForAge = 'Severely Stunted';
                } elseif ((float) $getAge->stunted_from <= (float) $request->height && (float) $getAge->stunted_to >= (float) $request->height) {
                    $entryHeightForAge = 'Stunted';
                } elseif ((float) $getAge->normal_from <= (float) $request->height && (float) $getAge->normal_to >= (float) $request->height) {
                    $entryHeightForAge = 'Normal';
                } elseif ((float) $getAge->tall <= (float) $request->height) {
                    $entryHeightForAge = 'Tall';
                }
            } else {
                $getAge = cgs_hfa_girls::where('age_month', $entryAgeInMonths)->first();

                if ((float) $getAge->severly_stunted >= (float) $request->height) {
                    $entryHeightForAge = 'Severely Stunted';
                } elseif ((float) $getAge->stunted_from <= (float) $request->height && (float) $getAge->stunted_to >= (float) $request->height) {
                    $entryHeightForAge = 'Stunted';
                } elseif ((float) $getAge->normal_from <= (float) $request->height && (float) $getAge->normal_to >= (float) $request->height) {
                    $entryHeightForAge = 'Normal';
                } elseif ((float) $getAge->tall <= (float) $request->height) {
                    $entryHeightForAge = 'Tall';
                }
            }

            //weight for height
            if ($childSex == '1') {
                $getHeight = cgs_wfh_boys::where('length_in_cm', $request->height)->first();

                if ((float) $getHeight->severly_wasted >= (float) $request->weight) {
                    $entryWeightForHeight = 'Severely Wasted';
                } elseif ((float) $getHeight->wasted_from <= (float) $request->weight && (float) $getHeight->wasted_to >= (float) $request->weight) {
                    $entryWeightForHeight = 'Wasted';
                } elseif ((float) $getHeight->normal_from <= (float) $request->weight && (float) $getHeight->normal_to >= (float) $request->weight) {
                    $entryWeightForHeight = 'Normal';
                } elseif ((float) $getHeight->overweight_from <= (float) $request->weight && (float) $getHeight->overweight_to >= (float) $request->weight) {
                    $entryWeightForHeight = 'Overweight';
                } elseif ((float) $getHeight->obese <= (float) $request->weight) {
                    $entryWeightForHeight = 'Obese';
                }
            } else {
                $getHeight = cgs_wfh_girls::where('length_in_cm', $request->height)->first();

                if ((float) $getHeight->severly_wasted >= (float) $request->weight) {
                    $entryWeightForHeight = 'Severely Wasted';
                } elseif ((float) $getHeight->wasted_from <= (float) $request->weight && (float) $getHeight->wasted_to >= (float) $request->weight) {
                    $entryWeightForHeight = 'Wasted';
                } elseif ((float) $getHeight->normal_from <= (float) $request->weight && (float) $getHeight->normal_to >= (float) $request->weight) {
                    $entryWeightForHeight = 'Normal';
                } elseif ((float) $getHeight->overweight_from <= (float) $request->weight && (float) $getHeight->overweight_to >= (float) $request->weight) {
                    $entryWeightForHeight = 'Overweight';
                } elseif ((float) $getHeight->obese <= (float) $request->weight) {
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



            $exitNutritionalStatus = NutritionalStatus::create([
                'cycle_implementation_id' => $cycleID,
                'milk_feeding_id' => $childMilkFeeding,
                'child_id' => $request->child_id,
                'weight' => $request->weight,
                'height' => $request->height,
                'weighing_date' => $request->weighing_date,
                'age_in_months' => $entryAgeInMonths,
                'age_in_years' => $entryAgeInYears,
                'weight_for_age' => $entryWeightForAge,
                'height_for_age' => $entryHeightForAge,
                'weight_for_height' => $entryWeightForHeight,
                'is_malnourish' => $entryIsMalnourished,
                'is_undernourish' => $entryIsUndernourished,
                'created_by_user_id' => auth()->id(),
                'updated_by_user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('nutritionalstatus.index', ['id' => $request->child_id])->with('success', 'After 120 feeding days details saved successfully.');
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
