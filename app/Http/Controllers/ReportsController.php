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

    public function generatePDF(Request $request)
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
                if ($cdcId) {
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
            }

            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            $data = [
                'cycleImplementation' => $cycleImplementation,
                'isFunded' => $isFunded,
                'isNotFunded' => $isNotFunded,
                'isPwdChildren' => $isPwdChildren,
                'countsPerCenter' => $countsPerCenter,
                'countsPerCenterAndGender' => $countsPerCenterAndGender,
                'centers' => $centers,
                'cdcId' => $cdcId
            ];

            $pdf = PDF::loadView('reports.sample', $data)->setPaper('folio','landscape');
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
