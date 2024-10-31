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
        $this->middleware('permission:create-nutritional-status', ['only' => ['index', 'storeUponEntryDetails', 'storeExitDetails']]);
        $this->middleware('permission:edit-nutritional-status', ['only' => ['index', 'edit', 'updateUponEntryDetails', 'updateAfter120Details' ]]);
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
    public function edit($id)
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
            
        return view('nutritionalstatus.edit', compact('child', 'entryDetails', 'exitDetails', 'hasUponEntryData', 'hasUponExitData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateUponEntryDetails(UpdateNutritionalStatusRequest $request, NutritionalStatus $nutritionalStatus)
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

        $nutritionalStatus = NutritionalStatus::where('child_id', $request->child_id)->first();

        if ($nutritionalStatus) {
            $nutritionalStatus->update([
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
                'updated_by_user_id' => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', 'Upon entry details updated successfully.');

    }

    public function updateAfter120Details(UpdateNutritionalStatusRequest $request)
    {
        $validatedData = $request->validated();

            $exitWeightForAge = null;
            $exitHeightForAge = null;
            $exitWeightForHeight = null;
            $exitIsMalnourished = false;
            $exitIsUndernourished = false;

            $child = Child::with( 'sex')
                ->orderBy('created_at')
                ->skip(1)
                ->take(1)
                ->first();

            $childSex = $child->sex->id;
            $childBirthDate = Carbon::parse($child->date_of_birth);

            $exitWeighingDate = Carbon::parse($request->weighing_date);
            $exitAgeInMonths = $exitWeighingDate->diffInMonths($childBirthDate);
            $exitAgeInYears = $exitAgeInMonths / 12;

            //weight for age
            if ($childSex == '1') {
                $getAge = cgs_wfa_boys::where('age_month', $exitAgeInMonths)->first();

                if ((float) $getAge->severly_underweight >= (float) $request->weight) {
                    $exitWeightForAge = 'Severely Underweight';
                } elseif ((float) $getAge->underweight_from <= (float) $request->weight && (float) $getAge->underweight_to >= (float) $request->weight) {
                    $exitWeightForAge = 'Underweight';
                } elseif ((float) $getAge->normal_from <= (float) $request->weight && (float) $getAge->normal_to >= (float) $request->weight) {
                    $exitWeightForAge = 'Normal';
                } elseif ((float) $request->weight > (float) $getAge->normal_to) {
                    $exitWeightForAge = 'Overweight';
                }
            } else {
                $getAge = cgs_wfa_girls::where('age_month', $exitAgeInMonths)->first();

                if ((float) $getAge->severly_underweight >= (float) $request->weight) {
                    $exitWeightForAge = 'Severely Underweight';
                } elseif ((float) $getAge->underweight_from <= (float) $request->weight && (float) $getAge->underweight_to >= (float) $request->weight) {
                    $exitWeightForAge = 'Underweight';
                } elseif ((float) $getAge->normal_from <= (float) $request->weight && (float) $getAge->normal_to >= (float) $request->weight) {
                    $exitWeightForAge = 'Normal';
                } elseif ((float) $request->weight > (float) $getAge->normal_to) {
                    $exitWeightForAge = 'Overweight';
                }
            }

            //height for age
            if ($childSex == '1') {
                $getAge = cgs_hfa_boys::where('age_month', $exitAgeInMonths)->first();

                if ((float) $getAge->severly_stunted >= (float) $request->height) {
                    $exitHeightForAge = 'Severely Stunted';
                } elseif ((float) $getAge->stunted_from <= (float) $request->height && (float) $getAge->stunted_to >= (float) $request->height) {
                    $exitHeightForAge = 'Stunted';
                } elseif ((float) $getAge->normal_from <= (float) $request->height && (float) $getAge->normal_to >= (float) $request->height) {
                    $exitHeightForAge = 'Normal';
                } elseif ((float) $getAge->tall <= (float) $request->height) {
                    $exitHeightForAge = 'Tall';
                }
            } else {
                $getAge = cgs_hfa_girls::where('age_month', $exitAgeInMonths)->first();

                if ((float) $getAge->severly_stunted >= (float) $request->height) {
                    $exitHeightForAge = 'Severely Stunted';
                } elseif ((float) $getAge->stunted_from <= (float) $request->height && (float) $getAge->stunted_to >= (float) $request->height) {
                    $exitHeightForAge = 'Stunted';
                } elseif ((float) $getAge->normal_from <= (float) $request->height && (float) $getAge->normal_to >= (float) $request->height) {
                    $exitHeightForAge = 'Normal';
                } elseif ((float) $getAge->tall <= (float) $request->height) {
                    $exitHeightForAge = 'Tall';
                }
            }

            //weight for height
            if ($childSex == '1') {
                $getHeight = cgs_wfh_boys::where('length_in_cm', $request->height)->first();

                if ((float) $getHeight->severly_wasted >= (float) $request->weight) {
                    $exitWeightForHeight = 'Severely Wasted';
                } elseif ((float) $getHeight->wasted_from <= (float) $request->weight && (float) $getHeight->wasted_to >= (float) $request->weight) {
                    $exitWeightForHeight = 'Wasted';
                } elseif ((float) $getHeight->normal_from <= (float) $request->weight && (float) $getHeight->normal_to >= (float) $request->weight) {
                    $exitWeightForHeight = 'Normal';
                } elseif ((float) $getHeight->overweight_from <= (float) $request->weight && (float) $getHeight->overweight_to >= (float) $request->weight) {
                    $exitWeightForHeight = 'Overweight';
                } elseif ((float) $getHeight->obese <= (float) $request->weight) {
                    $exitWeightForHeight = 'Obese';
                }
            } else {
                $getHeight = cgs_wfh_girls::where('length_in_cm', $request->height)->first();

                if ((float) $getHeight->severly_wasted >= (float) $request->weight) {
                    $exitWeightForHeight = 'Severely Wasted';
                } elseif ((float) $getHeight->wasted_from <= (float) $request->weight && (float) $getHeight->wasted_to >= (float) $request->weight) {
                    $exitWeightForHeight = 'Wasted';
                } elseif ((float) $getHeight->normal_from <= (float) $request->weight && (float) $getHeight->normal_to >= (float) $request->weight) {
                    $exitWeightForHeight = 'Normal';
                } elseif ((float) $getHeight->overweight_from <= (float) $request->weight && (float) $getHeight->overweight_to >= (float) $request->weight) {
                    $exitWeightForHeight = 'Overweight';
                } elseif ((float) $getHeight->obese <= (float) $request->weight) {
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

            $nutritionalStatus = NutritionalStatus::where('child_id', $request->child_id)
                ->orderBy('created_at')
                ->skip(1)
                ->take(1)
                ->first();

                $nutritionalStatus->update([
                    'child_id' => $request->child_id,
                    'weight' => $request->weight,
                    'height' => $request->height,
                    'weighing_date' => $request->weighing_date,
                    'age_in_months' => $exitAgeInMonths,
                    'age_in_years' => $exitAgeInYears,
                    'weight_for_age' => $exitWeightForAge,
                    'height_for_age' => $exitHeightForAge,
                    'weight_for_height' => $exitWeightForHeight,
                    'is_malnourish' => $exitIsMalnourished,
                    'is_undernourish' => $exitIsUndernourished,
                    'updated_by_user_id' => auth()->id(),
                ]);
           

                return redirect()->back()->with('success', 'After 120 details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NutritionalStatus $nutritionalStatus)
    {
        //
    }
}
