<?php

namespace App\Http\Controllers;

use App\Models\ChildDevelopmentCenter;
use App\Models\Psgc;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Child;
use App\Models\CycleImplementation;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-cycle-implementation', ['only' => ['view']]);
        $this->middleware('permission:create-cycle-implementation', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-cycle-implementation', ['only' => ['edit', 'update']]);
    }

    public function index(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', null);

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        $fundedChildren = Child::with('nutritionalStatus')
            ->where('is_funded', true)
            ->where('cycle_implementation_id', $cycleImplementation->id);

        $notFundedChildren = Child::with('nutritionalStatus')
            ->where('is_funded', false)
            ->where('cycle_implementation_id', $cycleImplementation->id);

        $childrenWithDisabilities = Child::with('center')
            ->where('is_funded', true)
            ->where('is_person_with_disability', true)
            ->where('cycle_implementation_id', $cycleImplementation->id);

        if (auth()->user()->hasRole('admin')) {

            $isFunded = $fundedChildren->paginate(10);
            $isNotFunded = $notFundedChildren->paginate(10);
            $isPwdChildren = $childrenWithDisabilities->paginate(10);


            $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                ->where('is_funded', true)
                ->selectRaw('count(*) as total')
                ->groupBy('child_development_center_id', 'sex_id')
                ->get()
                ->mapToGroups(function ($item) {
                    return [$item->child_development_center_id => [$item->sex_id => $item->total]];
                })
                ->map(function ($items) {
                    return [
                        'male' => $items->get(1, 0),
                        'female' => $items->get(2, 0),
                    ];
                });


            $countsPerCenter = Child::with('center')
                ->where('is_funded', true)
                ->get()
                ->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) {
                    return [
                        'indigenous_people' => [
                            'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                        ],
                        'pantawid' => [
                            'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'pwd' => [
                            'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'lactose_intolerant' => [
                            'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                        ],
                        'child_of_solo_parent' => [
                            'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                        ],
                        'dewormed' => [
                            'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'vitamin_a' => [
                            'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                        ],
                    ];
                });

                $countsPerNutritionalStatus = Child::with('nutritionalStatus')
                    ->where('is_funded', true)
                    ->get()
                    ->filter(function ($child) {
                        $nutritionalStatus = $child->nutritionalStatus; 
                        if ($nutritionalStatus) {
                            $entryWeighingDate = Carbon::parse($nutritionalStatus->entry_weighing_date);
                            $birthdate = Carbon::parse($child->birthdate);
                            $ageInYears = $birthdate->diffInYears($entryWeighingDate); // Calculate age based on entry weighing date
                            return in_array($ageInYears, [2, 3, 4, 5]);
                        }
                        return false; 
                    })
                    ->groupBy(function ($child) {
                        $nutritionalStatus = $child->nutritionalStatus; 
                        return $nutritionalStatus ? Carbon::parse($child->birthdate)->diffInYears(Carbon::parse($nutritionalStatus->entry_weighing_date)) : null;
                    })
                    ->map(function ($childrenByAge) {
                        return [
                            'weight_for_age_normal' => [
                                'male' => $childrenByAge->where('entry_weight_for_age', 'Normal')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_weight_for_age', 'Normal')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_age_underweight' => [
                                'male' => $childrenByAge->where('entry_weight_for_age', 'Underweight')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_weight_for_age', 'Underweight')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_age_severely_underweight' => [
                                'male' => $childrenByAge->where('entry_weight_for_age', 'Severely Underweight')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_weight_for_age', 'Severely Underweight')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_age_overweight' => [
                                'male' => $childrenByAge->where('entry_weight_for_age', 'Overweight')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_weight_for_age', 'Overweight')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_normal' => [
                                'male' => $childrenByAge->where('entry_weight_for_height', 'Normal')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_weight_for_height', 'Normal')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_wasted' => [
                                'male' => $childrenByAge->where('entry_weight_for_height', 'Wasted')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_weight_for_height', 'Wasted')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_severely_wasted' => [
                                'male' => $childrenByAge->where('entry_weight_for_height', 'Severely Wasted')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_weight_for_height', 'Severely Wasted')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_overweight' => [
                                'male' => $childrenByAge->where('entry_weight_for_height', 'Overweight')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_weight_for_height', 'Overweight')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_obese' => [
                                'male' => $childrenByAge->where('entry_weight_for_height', 'Obese')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_weight_for_height', 'Obese')->where('sex_id', 2)->count(),
                            ],
                            'height_for_age_normal' => [
                                'male' => $childrenByAge->where('entry_height_for_age', 'Normal')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_height_for_age', 'Normal')->where('sex_id', 2)->count(),
                            ],
                            'height_for_age_stunted' => [
                                'male' => $childrenByAge->where('entry_height_for_age', 'Stunted')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_height_for_age', 'Stunted')->where('sex_id', 2)->count(),
                            ],
                            'height_for_age_severely_stunted' => [
                                'male' => $childrenByAge->where('entry_height_for_age', 'Severely Stunted')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_height_for_age', 'Severely Stunted')->where('sex_id', 2)->count(),
                            ],
                            'height_for_age_tall' => [
                                'male' => $childrenByAge->where('entry_height_for_age', 'Tall')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('entry_height_for_age', 'Tall')->where('sex_id', 2)->count(),
                            ],
                        ];
                    });

            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            return view('reports.index', compact(
                'isFunded',
                'isNotFunded',
                'isPwdChildren',
                'countsPerCenter',
                'countsPerCenterAndGender',
                'countsPerNutritionalStatus',
                'centers',
                'cdcId'
            ));

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userCityPsgc = auth()->user()->city_name_psgc;

            $matchingPsgcIds = Psgc::where('city_name_psgc', $userCityPsgc)
                ->pluck('psgc_id');

            $isFunded = $fundedChildren = Child::with('nutritionalStatus')
                ->where('is_funded', true)
                ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                    $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                })
                ->where('cycle_implementation_id', $cycleImplementation->id)
                ->paginate(10);

            $isNotFunded = $notFundedChildren = Child::with('nutritionalStatus')
                ->where('is_funded', false)
                ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                    $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                })
                ->where('cycle_implementation_id', $cycleImplementation->id)
                ->paginate(10);

            $isPwdChildren = $childrenWithDisabilities = Child::with('center')
                ->where('is_funded', true)
                ->where('is_person_with_disability', true)
                ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                    $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                })
                ->where('cycle_implementation_id', $cycleImplementation->id)
                ->paginate(10);

            $centers = ChildDevelopmentCenter::whereIn('psgc_id', $matchingPsgcIds)->get();

            // $countsPerCenterAndGender = $this->getCountsPerCenterAndGender($matchingPsgcIds ?? null);

            $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                ->where('is_funded', true)
                ->selectRaw('count(*) as total')
                ->groupBy('child_development_center_id', 'sex_id')
                ->get()
                ->mapToGroups(function ($item) {
                    return [$item->child_development_center_id => [$item->sex_id => $item->total]];
                })
                ->map(function ($items) {
                    return [
                        'male' => $items->get(1, 0),
                        'female' => $items->get(2, 0),
                    ];
                });


            $countsPerCenter = Child::with('center')
                ->where('is_funded', true)
                ->get()
                ->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) {
                    return [
                        'indigenous_people' => [
                            'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                        ],
                        'pantawid' => [
                            'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'pwd' => [
                            'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'lactose_intolerant' => [
                            'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                        ],
                        'child_of_solo_parent' => [
                            'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                        ],
                        'dewormed' => [
                            'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'vitamin_a' => [
                            'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                        ],
                    ];
                });

            return view('reports.index', compact(
                'isFunded',
                'isNotFunded',
                'isPwdChildren',
                'countsPerCenter',
                'countsPerCenterAndGender',
                'centers',
                'cdcId'
            ));

        } else {

            $cdc = ChildDevelopmentCenter::where('assigned_user_id', auth()->id())->first();

            if (!$cdc) {
                return view('reports.index', [
                    'fundedChildren' => collect(),
                    'notFundedChildren' => collect(),
                    'childrenWithDisabilities' => collect(),
                    'countsPerCenter' => collect(),
                    'countsPerCenterAndGender' => collect(),
                    'centers' => ChildDevelopmentCenter::all()->keyBy('id'),
                ]);
            }

            $isFunded = $fundedChildren->where('child_development_center_id', $cdc->id)->paginate(10);
            $isNotFunded = $notFundedChildren->where('child_development_center_id', $cdc->id)->paginate(10);
            $isPwdChildren = $childrenWithDisabilities->paginate(10);

            $countsPerCenterAndGender = Child::where('child_development_center_id', $cdc->id)
                ->where('is_funded', true)
                ->select('sex_id')
                ->selectRaw('count(*) as total')
                ->groupBy('sex_id')
                ->get()
                ->mapToGroups(function ($item) {
                    return [$item->sex_id => $item->total];
                })
                ->map(function ($items) {
                    return [
                        'male' => $items->get(1, 0),
                        'female' => $items->get(2, 0),
                    ];
                });

            $countsPerCenter = Child::where('child_development_center_id', $cdc->id)
                ->where('is_funded', true)
                ->get()
                ->filter(function ($child) {
                    $nutritionalStatus = $child->nutritionalStatus; 
                    if ($nutritionalStatus) {
                        $entryWeighingDate = Carbon::parse($nutritionalStatus->entry_weighing_date);
                        $birthdate = Carbon::parse($child->date_of_birth);
                        $ageInYears = $birthdate->diffInYears($entryWeighingDate); 
                        return in_array($ageInYears, [2, 3, 4, 5]);
                    }
                    return false; 
                })
                ->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) {
                    return [
                        'indigenous_people' => [
                            'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                        ],
                        'pantawid' => [
                            'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'pwd' => [
                            'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'lactose_intolerant' => [
                            'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                        ],
                        'child_of_solo_parent' => [
                            'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                        ],
                        'dewormed' => [
                            'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'vitamin_a' => [
                            'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                        ],
                    ];
                });

                $countsPerNutritionalStatus = Child::with('nutritionalStatus')
                    ->where('is_funded', true)
                    ->get()
                    ->filter(function ($child) {
                        $nutritionalStatus = $child->nutritionalStatus; 
                        if ($nutritionalStatus) {
                            $entryWeighingDate = Carbon::parse($nutritionalStatus->entry_weighing_date);
                            $birthdate = Carbon::parse($child->date_of_birth);
                            $ageInYears = $birthdate->diffInYears($entryWeighingDate); 
                            return in_array($ageInYears, [2, 3, 4, 5]);
                        }
                        return false; 
                    })
                    ->groupBy(function ($child) {
                        $nutritionalStatus = $child->nutritionalStatus;
                        return $nutritionalStatus ? Carbon::parse($child->date_of_birth)->diffInYears(Carbon::parse($nutritionalStatus->entry_weighing_date)) : null;
                    })
                    ->map(function ($childrenByAge) {
                        return [
                            'weight_for_age_normal' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_weight_for_age', 'Normal')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_weight_for_age', 'Normal')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_age_underweight' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_weight_for_age', 'Underweight')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_weight_for_age', 'Underweight')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_age_severely_underweight' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_weight_for_age', 'Severely Underweight')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_weight_for_age', 'Severely Underweight')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_age_overweight' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_weight_for_age', 'Overweight')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_weight_for_age', 'Overweight')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_normal' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_weight_for_height', 'Normal')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_weight_for_height', 'Normal')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_wasted' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_weight_for_height', 'Wasted')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_weight_for_height', 'Wasted')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_severely_wasted' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_weight_for_height', 'Severely Wasted')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_weight_for_height', 'Severely Wasted')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_overweight' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_weight_for_height', 'Overweight')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_weight_for_height', 'Overweight')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_obese' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_weight_for_height', 'Obese')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_weight_for_height', 'Obese')->where('sex_id', 2)->count(),
                            ],
                            'height_for_age_normal' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_height_for_age', 'Normal')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_height_for_age', 'Normal')->where('sex_id', 2)->count(),
                            ],
                            'height_for_age_stunted' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_height_for_age', 'Stunted')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_height_for_age', 'Stunted')->where('sex_id', 2)->count(),
                            ],
                            'height_for_age_severely_stunted' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_height_for_age', 'Severely Stunted')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_height_for_age', 'Severely Stunted')->where('sex_id', 2)->count(),
                            ],
                            'height_for_age_tall' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_height_for_age', 'Tall')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_height_for_age', 'Tall')->where('sex_id', 2)->count(),
                            ],
                            'entry_is_undernourish' => [
                                'male' => $childrenByAge->where('nutritionalStatus.entry_is_undernourish', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.entry_is_undernourish', true)->where('sex_id', 2)->count(),
                            ],

                        ];
                    });

            $exitCountsPerCenter = Child::where('child_development_center_id', $cdc->id)
                ->where('is_funded', true)
                ->get()
                ->filter(function ($child) {
                    $nutritionalStatus = $child->nutritionalStatus; 
                    if ($nutritionalStatus) {
                        $exitWeighingDate = Carbon::parse($nutritionalStatus->exit_weighing_date);
                        $birthdate = Carbon::parse($child->date_of_birth);
                        $ageInYears = $birthdate->diffInYears($exitWeighingDate); 
                        return in_array($ageInYears, [2, 3, 4, 5]);
                    }
                    return false; 
                })
                ->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) {
                    return [
                        'indigenous_people' => [
                            'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                        ],
                        'pantawid' => [
                            'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'pwd' => [
                            'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'lactose_intolerant' => [
                            'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                        ],
                        'child_of_solo_parent' => [
                            'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                        ],
                        'dewormed' => [
                            'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'vitamin_a' => [
                            'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                        ],
                    ];
                });

                $exitCountsPerNutritionalStatus = Child::with('nutritionalStatus')
                    ->where('is_funded', true)
                    ->get()
                    ->filter(function ($child) {
                        $nutritionalStatus = $child->nutritionalStatus; 
                        if ($nutritionalStatus) {
                            $exitWeighingDate = Carbon::parse($nutritionalStatus->exit_weighing_date);
                            $birthdate = Carbon::parse($child->date_of_birth);
                            $ageInYears = $birthdate->diffInYears($exitWeighingDate); 
                            return in_array($ageInYears, [2, 3, 4, 5]);
                        }
                        return false; 
                    })
                    ->groupBy(function ($child) {
                        $nutritionalStatus = $child->nutritionalStatus;
                        return $nutritionalStatus ? Carbon::parse($child->date_of_birth)->diffInYears(Carbon::parse($nutritionalStatus->exit_weighing_date)) : null;
                    })
                    ->map(function ($childrenByAge) {
                        return [
                            'weight_for_age_normal' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_weight_for_age', 'Normal')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_weight_for_age', 'Normal')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_age_underweight' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_weight_for_age', 'Underweight')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_weight_for_age', 'Underweight')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_age_severely_underweight' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_weight_for_age', 'Severely Underweight')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_weight_for_age', 'Severely Underweight')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_age_overweight' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_weight_for_age', 'Overweight')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_weight_for_age', 'Overweight')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_normal' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_weight_for_height', 'Normal')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_weight_for_height', 'Normal')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_wasted' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_weight_for_height', 'Wasted')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_weight_for_height', 'Wasted')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_severely_wasted' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_weight_for_height', 'Severely Wasted')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_weight_for_height', 'Severely Wasted')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_overweight' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_weight_for_height', 'Overweight')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_weight_for_height', 'Overweight')->where('sex_id', 2)->count(),
                            ],
                            'weight_for_height_obese' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_weight_for_height', 'Obese')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_weight_for_height', 'Obese')->where('sex_id', 2)->count(),
                            ],
                            'height_for_age_normal' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_height_for_age', 'Normal')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_height_for_age', 'Normal')->where('sex_id', 2)->count(),
                            ],
                            'height_for_age_stunted' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_height_for_age', 'Stunted')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_height_for_age', 'Stunted')->where('sex_id', 2)->count(),
                            ],
                            'height_for_age_severely_stunted' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_height_for_age', 'Severely Stunted')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_height_for_age', 'Severely Stunted')->where('sex_id', 2)->count(),
                            ],
                            'height_for_age_tall' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_height_for_age', 'Tall')->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_height_for_age', 'Tall')->where('sex_id', 2)->count(),
                            ],
                            'exit_is_undernourish' => [
                                'male' => $childrenByAge->where('nutritionalStatus.exit_is_undernourish', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByAge->where('nutritionalStatus.exit_is_undernourish', true)->where('sex_id', 2)->count(),
                            ],

                        ];
                    });

            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            return view('reports.index', compact(
                'isFunded',
                'isNotFunded',
                'isPwdChildren',
                'countsPerCenter',
                'countsPerCenterAndGender',
                'countsPerNutritionalStatus',
                'exitCountsPerCenter',
                'exitCountsPerNutritionalStatus',
                'centers',
                'cdcId'
            ));
        }
    }

    public function filterFundedByCdc(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('funded_center_name', null);

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        $fundedChildren = Child::with('nutritionalStatus')
            ->where('is_funded', true)
            ->where('cycle_implementation_id', $cycleImplementation->id);

        $notFundedChildren = Child::with('nutritionalStatus')
            ->where('is_funded', false)
            ->where('cycle_implementation_id', $cycleImplementation->id);

        $childrenWithDisabilities = Child::with('center')
            ->where('is_funded', true)
            ->where('is_person_with_disability', true)
            ->where('cycle_implementation_id', $cycleImplementation->id);

        if (auth()->user()->hasRole('admin')) {
            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->paginate(10);
                $isNotFunded = $notFundedChildren->paginate(10);
                $isPwdChildren = $childrenWithDisabilities->paginate(10);


                $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                    ->where('is_funded', true)
                    ->selectRaw('count(*) as total')
                    ->groupBy('child_development_center_id', 'sex_id')
                    ->get()
                    ->mapToGroups(function ($item) {
                        return [$item->child_development_center_id => [$item->sex_id => $item->total]];
                    })
                    ->map(function ($items) {
                        return [
                            'male' => $items->get(1, 0),
                            'female' => $items->get(2, 0),
                        ];
                    });


                $countsPerCenter = Child::with('center')
                    ->where('is_funded', true)
                    ->get()
                    ->groupBy('child_development_center_id')
                    ->map(function ($childrenByCenter) {
                        return [
                            'indigenous_people' => [
                                'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                            ],
                            'pantawid' => [
                                'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'pwd' => [
                                'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'lactose_intolerant' => [
                                'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                            ],
                            'child_of_solo_parent' => [
                                'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                            ],
                            'dewormed' => [
                                'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'vitamin_a' => [
                                'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                        ];
                    });

            } else {
                $isFunded = $fundedChildren->where('child_development_center_id', $cdcId)->paginate(10);
                $isNotFunded = $notFundedChildren->paginate(10);
                $isPwdChildren = $childrenWithDisabilities->paginate(10);

                $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                    ->where('is_funded', true)
                    ->selectRaw('count(*) as total')
                    ->groupBy('child_development_center_id', 'sex_id')
                    ->get()
                    ->mapToGroups(function ($item) {
                        return [$item->child_development_center_id => [$item->sex_id => $item->total]];
                    })
                    ->map(function ($items) {
                        return [
                            'male' => $items->get(1, 0),
                            'female' => $items->get(2, 0),
                        ];
                    });


                $countsPerCenter = Child::with('center')
                    ->where('is_funded', true)
                    ->get()
                    ->groupBy('child_development_center_id')
                    ->map(function ($childrenByCenter) {
                        return [
                            'indigenous_people' => [
                                'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                            ],
                            'pantawid' => [
                                'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'pwd' => [
                                'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'lactose_intolerant' => [
                                'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                            ],
                            'child_of_solo_parent' => [
                                'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                            ],
                            'dewormed' => [
                                'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'vitamin_a' => [
                                'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                        ];
                    });
            }

            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            return view('reports.index', compact(
                'isFunded',
                'isNotFunded',
                'isPwdChildren',
                'countsPerCenter',
                'countsPerCenterAndGender',
                'centers',
                'cdcId'
            ));

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userCityPsgc = auth()->user()->city_name_psgc;

            $matchingPsgcIds = Psgc::where('city_name_psgc', $userCityPsgc)
                ->pluck('psgc_id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren = Child::with('nutritionalStatus')
                    ->where('is_funded', true)
                    ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                        $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                    })
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);

                $isNotFunded = $notFundedChildren = Child::with('nutritionalStatus')
                    ->where('is_funded', false)
                    ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                        $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                    })
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);

                $isPwdChildren = $childrenWithDisabilities = Child::with('center')
                    ->where('is_funded', true)
                    ->where('is_person_with_disability', true)
                    ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                        $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                    })
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);

                $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                    ->where('is_funded', true)
                    ->selectRaw('count(*) as total')
                    ->groupBy('child_development_center_id', 'sex_id')
                    ->get()
                    ->mapToGroups(function ($item) {
                        return [$item->child_development_center_id => [$item->sex_id => $item->total]];
                    })
                    ->map(function ($items) {
                        return [
                            'male' => $items->get(1, 0),
                            'female' => $items->get(2, 0),
                        ];
                    });


                $countsPerCenter = Child::with('center')
                    ->where('is_funded', true)
                    ->get()
                    ->groupBy('child_development_center_id')
                    ->map(function ($childrenByCenter) {
                        return [
                            'indigenous_people' => [
                                'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                            ],
                            'pantawid' => [
                                'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'pwd' => [
                                'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'lactose_intolerant' => [
                                'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                            ],
                            'child_of_solo_parent' => [
                                'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                            ],
                            'dewormed' => [
                                'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'vitamin_a' => [
                                'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                        ];
                    });
            } else {
                $isFunded = $fundedChildren = Child::with('nutritionalStatus')
                    ->where('is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);

                $isNotFunded = $notFundedChildren = Child::with('nutritionalStatus')
                    ->where('is_funded', false)
                    ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                        $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                    })
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);

                $isPwdChildren = $childrenWithDisabilities = Child::with('center')
                    ->where('is_funded', true)
                    ->where('is_person_with_disability', true)
                    ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                        $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                    })
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);

                $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                    ->where('is_funded', true)
                    ->selectRaw('count(*) as total')
                    ->groupBy('child_development_center_id', 'sex_id')
                    ->get()
                    ->mapToGroups(function ($item) {
                        return [$item->child_development_center_id => [$item->sex_id => $item->total]];
                    })
                    ->map(function ($items) {
                        return [
                            'male' => $items->get(1, 0),
                            'female' => $items->get(2, 0),
                        ];
                    });


                $countsPerCenter = Child::with('center')
                    ->where('is_funded', true)
                    ->get()
                    ->groupBy('child_development_center_id')
                    ->map(function ($childrenByCenter) {
                        return [
                            'indigenous_people' => [
                                'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                            ],
                            'pantawid' => [
                                'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'pwd' => [
                                'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'lactose_intolerant' => [
                                'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                            ],
                            'child_of_solo_parent' => [
                                'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                            ],
                            'dewormed' => [
                                'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'vitamin_a' => [
                                'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                        ];
                    });
            }

            $centers = ChildDevelopmentCenter::whereIn('psgc_id', $matchingPsgcIds)->get();

            return view('reports.index', compact(
                'isFunded',
                'isNotFunded',
                'isPwdChildren',
                'countsPerCenter',
                'countsPerCenterAndGender',
                'centers',
                'cdcId'
            ));

        } else {

            $cdc = ChildDevelopmentCenter::where('assigned_user_id', auth()->id())->first();

            if (!$cdc) {
                return view('reports.index', [
                    'fundedChildren' => collect(),
                    'notFundedChildren' => collect(),
                    'childrenWithDisabilities' => collect(),
                    'countsPerCenter' => collect(),
                    'countsPerCenterAndGender' => collect(),
                    'centers' => ChildDevelopmentCenter::all()->keyBy('id'),
                ]);
            }

            $isFunded = $fundedChildren->where('child_development_center_id', $cdc->id)->paginate(10);
            $isNotFunded = $notFundedChildren->where('child_development_center_id', $cdc->id)->paginate(10);
            $isPwdChildren = $childrenWithDisabilities->paginate(10);

            $countsPerCenterAndGender = Child::where('child_development_center_id', $cdc->id)
                ->where('is_funded', true)
                ->select('sex_id')
                ->selectRaw('count(*) as total')
                ->groupBy('sex_id')
                ->get()
                ->mapToGroups(function ($item) {
                    return [$item->sex_id => $item->total];
                })
                ->map(function ($items) {
                    return [
                        'male' => $items->get(1, 0),
                        'female' => $items->get(2, 0),
                    ];
                });

            $countsPerCenter = Child::where('child_development_center_id', $cdc->id)
                ->where('is_funded', true)
                ->get()
                ->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) {
                    return [
                        'indigenous_people' => [
                            'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                        ],
                        'pantawid' => [
                            'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'pwd' => [
                            'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'lactose_intolerant' => [
                            'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                        ],
                        'child_of_solo_parent' => [
                            'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                        ],
                        'dewormed' => [
                            'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'vitamin_a' => [
                            'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                        ],
                    ];
                });
        }

        $centers = ChildDevelopmentCenter::all()->keyBy('id');

        return view('reports.index', compact(
            'isFunded',
            'isNotFunded',
            'isPwdChildren',
            'countsPerCenter',
            'countsPerCenterAndGender',
            'centers',
            'cdcId'
        ));
    }

    public function filterUnfundedByCdc(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('funded_center_name', null);

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        $fundedChildren = Child::with('nutritionalStatus')
            ->where('is_funded', true)
            ->where('cycle_implementation_id', $cycleImplementation->id);

        $notFundedChildren = Child::with('nutritionalStatus')
            ->where('is_funded', false)
            ->where('cycle_implementation_id', $cycleImplementation->id);

        $childrenWithDisabilities = Child::with('center')
            ->where('is_funded', true)
            ->where('is_person_with_disability', true)
            ->where('cycle_implementation_id', $cycleImplementation->id);

        if (auth()->user()->hasRole('admin')) {
            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->paginate(10);
                $isNotFunded = $notFundedChildren->paginate(10);
                $isPwdChildren = $childrenWithDisabilities->paginate(10);


                $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                    ->where('is_funded', true)
                    ->selectRaw('count(*) as total')
                    ->groupBy('child_development_center_id', 'sex_id')
                    ->get()
                    ->mapToGroups(function ($item) {
                        return [$item->child_development_center_id => [$item->sex_id => $item->total]];
                    })
                    ->map(function ($items) {
                        return [
                            'male' => $items->get(1, 0),
                            'female' => $items->get(2, 0),
                        ];
                    });


                $countsPerCenter = Child::with('center')
                    ->where('is_funded', true)
                    ->get()
                    ->groupBy('child_development_center_id')
                    ->map(function ($childrenByCenter) {
                        return [
                            'indigenous_people' => [
                                'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                            ],
                            'pantawid' => [
                                'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'pwd' => [
                                'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'lactose_intolerant' => [
                                'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                            ],
                            'child_of_solo_parent' => [
                                'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                            ],
                            'dewormed' => [
                                'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'vitamin_a' => [
                                'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                        ];
                    });

            } else {
                $isFunded = $fundedChildren->paginate(10);
                $isNotFunded = $notFundedChildren->where('child_development_center_id', $cdcId)->paginate(10);
                $isPwdChildren = $childrenWithDisabilities->paginate(10);

                $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                    ->where('is_funded', true)
                    ->selectRaw('count(*) as total')
                    ->groupBy('child_development_center_id', 'sex_id')
                    ->get()
                    ->mapToGroups(function ($item) {
                        return [$item->child_development_center_id => [$item->sex_id => $item->total]];
                    })
                    ->map(function ($items) {
                        return [
                            'male' => $items->get(1, 0),
                            'female' => $items->get(2, 0),
                        ];
                    });


                $countsPerCenter = Child::with('center')
                    ->where('is_funded', true)
                    ->get()
                    ->groupBy('child_development_center_id')
                    ->map(function ($childrenByCenter) {
                        return [
                            'indigenous_people' => [
                                'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                            ],
                            'pantawid' => [
                                'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'pwd' => [
                                'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'lactose_intolerant' => [
                                'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                            ],
                            'child_of_solo_parent' => [
                                'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                            ],
                            'dewormed' => [
                                'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'vitamin_a' => [
                                'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                        ];
                    });
            }

            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            return view('reports.index', compact(
                'isFunded',
                'isNotFunded',
                'isPwdChildren',
                'countsPerCenter',
                'countsPerCenterAndGender',
                'centers',
                'cdcId'
            ));

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userCityPsgc = auth()->user()->city_name_psgc;

            $matchingPsgcIds = Psgc::where('city_name_psgc', $userCityPsgc)
                ->pluck('psgc_id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren = Child::with('nutritionalStatus')
                    ->where('is_funded', true)
                    ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                        $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                    })
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);


                $isNotFunded = $notFundedChildren = Child::with('nutritionalStatus')
                    ->where('is_funded', false)
                    ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                        $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                    })
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);

                $isPwdChildren = $childrenWithDisabilities = Child::with('center')
                    ->where('is_funded', true)
                    ->where('is_person_with_disability', true)
                    ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                        $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                    })
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);

                $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                    ->where('is_funded', true)
                    ->selectRaw('count(*) as total')
                    ->groupBy('child_development_center_id', 'sex_id')
                    ->get()
                    ->mapToGroups(function ($item) {
                        return [$item->child_development_center_id => [$item->sex_id => $item->total]];
                    })
                    ->map(function ($items) {
                        return [
                            'male' => $items->get(1, 0),
                            'female' => $items->get(2, 0),
                        ];
                    });


                $countsPerCenter = Child::with('center')
                    ->where('is_funded', true)
                    ->get()
                    ->groupBy('child_development_center_id')
                    ->map(function ($childrenByCenter) {
                        return [
                            'indigenous_people' => [
                                'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                            ],
                            'pantawid' => [
                                'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'pwd' => [
                                'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'lactose_intolerant' => [
                                'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                            ],
                            'child_of_solo_parent' => [
                                'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                            ],
                            'dewormed' => [
                                'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'vitamin_a' => [
                                'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                        ];
                    });
            } else {
                $isFunded = $fundedChildren = Child::with('nutritionalStatus')
                    ->where('is_funded', true)
                    ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                        $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                    })
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);

                $isNotFunded = $notFundedChildren = Child::with('nutritionalStatus')
                    ->where('is_funded', false)
                    ->where('child_development_center_id', $cdcId)
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);

                $isPwdChildren = $childrenWithDisabilities = Child::with('center')
                    ->where('is_funded', true)
                    ->where('is_person_with_disability', true)
                    ->whereIn('child_development_center_id', function ($query) use ($matchingPsgcIds) {
                        $query->select('id')->from('child_development_centers')->whereIn('psgc_id', $matchingPsgcIds);
                    })
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);

                $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                    ->where('is_funded', true)
                    ->selectRaw('count(*) as total')
                    ->groupBy('child_development_center_id', 'sex_id')
                    ->get()
                    ->mapToGroups(function ($item) {
                        return [$item->child_development_center_id => [$item->sex_id => $item->total]];
                    })
                    ->map(function ($items) {
                        return [
                            'male' => $items->get(1, 0),
                            'female' => $items->get(2, 0),
                        ];
                    });


                $countsPerCenter = Child::with('center')
                    ->where('is_funded', true)
                    ->get()
                    ->groupBy('child_development_center_id')
                    ->map(function ($childrenByCenter) {
                        return [
                            'indigenous_people' => [
                                'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                            ],
                            'pantawid' => [
                                'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'pwd' => [
                                'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'lactose_intolerant' => [
                                'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                            ],
                            'child_of_solo_parent' => [
                                'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                            ],
                            'dewormed' => [
                                'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                            'vitamin_a' => [
                                'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                                'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                            ],
                        ];
                    });
            }

            $centers = ChildDevelopmentCenter::whereIn('psgc_id', $matchingPsgcIds)->get();

            return view('reports.index', compact(
                'isFunded',
                'isNotFunded',
                'isPwdChildren',
                'countsPerCenter',
                'countsPerCenterAndGender',
                'centers',
                'cdcId'
            ));

        } else {

            $cdc = ChildDevelopmentCenter::where('assigned_user_id', auth()->id())->first();

            if (!$cdc) {
                return view('reports.index', [
                    'fundedChildren' => collect(),
                    'notFundedChildren' => collect(),
                    'childrenWithDisabilities' => collect(),
                    'countsPerCenter' => collect(),
                    'countsPerCenterAndGender' => collect(),
                    'centers' => ChildDevelopmentCenter::all()->keyBy('id'),
                ]);
            }

            $isFunded = $fundedChildren->where('child_development_center_id', $cdc->id)->paginate(10);
            $isNotFunded = $notFundedChildren->where('child_development_center_id', $cdc->id)->paginate(10);
            $isPwdChildren = $childrenWithDisabilities->paginate(10);

            $countsPerCenterAndGender = Child::where('child_development_center_id', $cdc->id)
                ->where('is_funded', true)
                ->select('sex_id')
                ->selectRaw('count(*) as total')
                ->groupBy('sex_id')
                ->get()
                ->mapToGroups(function ($item) {
                    return [$item->sex_id => $item->total];
                })
                ->map(function ($items) {
                    return [
                        'male' => $items->get(1, 0),
                        'female' => $items->get(2, 0),
                    ];
                });

            $countsPerCenter = Child::where('child_development_center_id', $cdc->id)
                ->where('is_funded', true)
                ->get()
                ->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) {
                    return [
                        'indigenous_people' => [
                            'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                        ],
                        'pantawid' => [
                            'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'pwd' => [
                            'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'lactose_intolerant' => [
                            'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex_id', 2)->count(),
                        ],
                        'child_of_solo_parent' => [
                            'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex_id', 2)->count(),
                        ],
                        'dewormed' => [
                            'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex_id', 2)->count(),
                        ],
                        'vitamin_a' => [
                            'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex_id', 2)->count(),
                        ],
                    ];
                });
        }

        $centers = ChildDevelopmentCenter::all()->keyBy('id');

        return view('reports.index', compact(
            'isFunded',
            'isNotFunded',
            'isPwdChildren',
            'countsPerCenter',
            'countsPerCenterAndGender',
            'centers',
            'cdcId'
        ));
    }

    public function printFunded(Request $request)
    {
        // Get the active cycle
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        if (!$cycleImplementation) {
            return back()->with('error', 'No active cycle found');
        }

        // Get the center id from request or default to 'all_center'
        $cdcId = $request->input('funded_center_name', 'all_center');

        // Base query to fetch funded children
        $fundedChildrenQuery = Child::with('nutritionalStatus')
            ->where('is_funded', true)
            ->where('cycle_implementation_id', $cycleImplementation->id);

        // Filter by specific center if not 'all_center'
        if ($cdcId !== 'all_center') {
            $fundedChildrenQuery->where('child_development_center_id', $cdcId);
        }

        // Get the results
        $isFunded = $fundedChildrenQuery->get();

        // Get the centers if the user has the 'admin' role
        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
        } else {
            $centers = [];
        }

        // Debug the data fetched
        // dd($isFunded);

        // Load the PDF view
        $pdf = PDF::loadView('reports.sample', compact('cycleImplementation', 'isFunded', 'centers', 'cdcId'))
            ->setPaper('folio', 'landscape');

        // Stream the PDF
        return $pdf->stream('funded_children_report.pdf');
    }



    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
