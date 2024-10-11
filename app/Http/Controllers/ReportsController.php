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

        $fundedChildren = Child::with('nutritionalStatus', 'sex')
            ->where('is_funded', true)
            ->where('cycle_implementation_id', $cycleImplementation->id);


        if (auth()->user()->hasRole('admin')) {
            $isFunded = $fundedChildren->paginate(10);

            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            // dd($isFunded);

            return view('reports.index', compact('isFunded', 'centers', 'cdcId'));

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            $isFunded = $fundedChildren->whereIn('child_development_center_id', $centerIds)->paginate(10);

            return view('reports.index', compact('isFunded', 'centers', 'cdcId'));

        } else {

            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            $isFunded = $fundedChildren->whereIn('child_development_center_id', $centerIds)->paginate(10);

            return view('reports.index', compact('isFunded', 'centers', 'cdcId'));
        }
    }

    public function malnourish(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', null);

        if (!$cycleImplementation) {
            return view('reports.malnourish', [
                'fundedChildren' => collect(),
            ]);
        }

        $fundedChildren = Child::with('nutritionalStatus', 'sex', 'center')
            ->where('is_funded', true)
            ->where('cycle_implementation_id', $cycleImplementation->id)
            ->get();

        $childrenWithNutritionalStatus = [];

        foreach ($fundedChildren as $child) {
            $nutritionalStatuses = $child->nutritionalStatus;
            
            if ($nutritionalStatuses && $nutritionalStatuses->count() > 0) {
                $entry = $nutritionalStatuses->first();
                $exit = $nutritionalStatuses->count() > 1 ? $nutritionalStatuses[1] : null;
                
            } else {
                $entry = null;
                $exit = null;
            }

            $childrenWithNutritionalStatus[] = [
                'child' => $child,
                'entry' => $entry,
                'exit' => $exit,
            ];
        }


        if (auth()->user()->hasRole('admin')) {
            $isFunded = Child::with('nutritionalStatus', 'sex')
                ->where('is_funded', true)
                ->where('cycle_implementation_id', $cycleImplementation->id)
                ->paginate(10);

            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            return view('reports.malnourish', compact('isFunded', 'centers', 'cdcId'));

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            $isFunded = Child::with('nutritionalStatus', 'sex')
                ->where('is_funded', true)
                ->where('cycle_implementation_id', $cycleImplementation->id)
                ->whereIn('child_development_center_id', $centerIds)
                ->paginate(10);

            return view('reports.malnourish', compact('isFunded', 'centers', 'cdcId'));

        } else {

            $cdc = ChildDevelopmentCenter::where('assigned_worker_user_id', auth()->id())->first();

            $isFunded = Child::with('nutritionalStatus')
                ->where('is_funded', true)
                ->where('cycle_implementation_id', $cycleImplementation->id)
                ->where('child_development_center_id', $cdc->id)
                ->paginate(10);

            return view('reports.malnourish', compact('isFunded', 'centers', 'cdcId'));
        }
    }

    public function disabilities()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.disabilities', [
                'childrenWithDisabilities' => collect(),
            ]);
        }

        $childrenWithDisabilities = Child::with('center')
            ->where('is_funded', true)
            ->where('is_person_with_disability', true)
            ->where('person_with_disability_details', '!=', '')
            ->where('cycle_implementation_id', $cycleImplementation->id);


        if (auth()->user()->hasRole('admin')) {
            $isPwdChildren = $childrenWithDisabilities->paginate(10);

            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            return view('reports.disabilities', compact('isPwdChildren', 'centers'));

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            $isPwdChildren = $childrenWithDisabilities = Child::with('center')
                ->where('is_funded', true)
                ->where('is_person_with_disability', true)
                ->whereIn('child_development_center_id', $centerIds)
                ->where('cycle_implementation_id', $cycleImplementation->id)
                ->paginate(10);

            return view('reports.disabilities', compact('isPwdChildren', 'centers'));

        }
    }

    public function monitoring(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', null);

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }
        $fundedChildren = Child::with('nutritionalStatus', 'sex')
            ->where('is_funded', true)
            ->where('cycle_implementation_id', $cycleImplementation->id)
            ->get();

        $childrenWithNutritionalStatus = [];

        foreach ($fundedChildren as $child) {
            $nutritionalStatuses = $child->nutritionalStatus;
            
            if ($nutritionalStatuses && $nutritionalStatuses->count() > 0) {
                $entry = $nutritionalStatuses->first();
                $exit = $nutritionalStatuses->count() > 1 ? $nutritionalStatuses[1] : null;
                
            } else {
                $entry = null;
                $exit = null;
            }

            $childrenWithNutritionalStatus[] = [
                'child' => $child,
                'entry' => $entry,
                'exit' => $exit,
            ];
        }

        if (auth()->user()->hasRole('admin')) {
            
            $isFunded = Child::with('nutritionalStatus', 'sex')
                ->where('is_funded', true)
                ->where('cycle_implementation_id', $cycleImplementation->id)
                ->paginate(10);
        
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
        
            return view('reports.monitoring', compact('isFunded', 'centers', 'cdcId'));
        
        } elseif (auth()->user()->hasRole('lgu focal')) {

            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');
        
            $isFunded = Child::with('nutritionalStatus', 'sex')
                ->where('is_funded', true)
                ->where('cycle_implementation_id', $cycleImplementation->id)
                ->whereIn('child_development_center_id', $centerIds)
                ->paginate(10);
        
            return view('reports.monitoring', compact('isFunded', 'centers', 'cdcId'));
        
        } else {
            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');
        
            $isFunded = Child::with('nutritionalStatus', 'sex')
                ->where('is_funded', true)
                ->where('cycle_implementation_id', $cycleImplementation->id)
                ->whereIn('child_development_center_id', $centerIds)
                ->paginate(10);
        
            return view('reports.monitoring', compact('isFunded', 'centers', 'cdcId'));
        }        

    }

    public function unfunded(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', null);

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        $unfundedChildren = Child::with('nutritionalStatus')
            ->where('is_funded', false)
            ->where('cycle_implementation_id', $cycleImplementation->id);


        if (auth()->user()->hasRole('admin')) {
            $isNotFunded = $unfundedChildren->paginate(10);

            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            return view('reports.unfunded', compact('isNotFunded', 'centers', 'cdcId'));

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            $isNotFunded = $unfundedChildren->whereIn('child_development_center_id', $centerIds)->paginate(10);

            return view('reports.unfunded', compact('isNotFunded', 'centers', 'cdcId'));

        } else {

            $cdc = ChildDevelopmentCenter::where('assigned_worker_user_id', auth()->id())->first();

            $isNotFunded = $unfundedChildren->where('child_development_center_id', $cdc->id)->paginate(10);

            return view('reports.unfunded', compact('isNotFunded', 'cdcId'));
        }
    }

    public function undernourishedUponEntry()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            

        if (auth()->user()->hasRole('admin')) {

            $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
                $statuses = $child->nutritionalStatus->where('is_undernourish', true)
                                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            
                $entry = $statuses->first();
                $exit = $statuses->skip(1)->first();
            
                return [
                    'child_id' => $child->id,
                    'entry' => $entry,
                    'exit' => $exit,
                ];
            });

            $ageGroupsPerCenter = $fundedChildren->groupBy('child_development_center_id')->map(function ($childrenByCenter) use ($nutritionalStatusOccurrences) {
                return [
                    '2_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                            return $firstStatus && $firstStatus->age_in_years == 2 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                            return $firstStatus && $firstStatus->age_in_years == 2 && $child->sex_id == 2;
                        })->count(),
                    ],
                    '3_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                            return $firstStatus && $firstStatus->age_in_years == 3 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                            return $firstStatus && $firstStatus->age_in_years == 3 && $child->sex_id == 2;
                        })->count(),
                    ],
                    '4_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                            return $firstStatus && $firstStatus->age_in_years == 4 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                            return $firstStatus && $firstStatus->age_in_years == 4 && $child->sex_id == 2;
                        })->count(),
                    ],
                    '5_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                            return $firstStatus && $firstStatus->age_in_years == 5 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                            return $firstStatus && $firstStatus->age_in_years == 5 && $child->sex_id == 2;
                        })->count(),
                    ],
                ];
            });

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
                            'male' => $childrenByCenter->whereNotNull('pantawid_details')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->whereNotNull('pantawid_details')->where('sex_id', 2)->count(),
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

            $centers = ChildDevelopmentCenter::paginate(15);
            $centers->getCollection()->keyBy('id');

            return view('reports.undernourished-upon-entry', compact('centers', 'countsPerCenter', 'countsPerCenterAndGender', 'ageGroupsPerCenter'));
        
        } else if(auth()->user()->hasRole('lgu focal')) {
            
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');
            
            $children = Child::whereHas('center', function ($query) use ($centerIds) {
                $query->whereIn('id', $centerIds);
                })
                ->where('is_funded', true)
                ->get();

            
            $nutritionalStatusOccurrences = $children->map(function ($child) {
                if ($child->nutritionalStatus->isEmpty()) {
                    return [
                        'child_id' => $child->id,
                        'entry' => null,
                        'exit' => null,
                    ];
                }
                $statuses = $child->nutritionalStatus->where('is_undernourish', true)
                                    ->whereIn('age_in_years', [2, 3, 4, 5]);
                $entry = $statuses->first();
                $exit = $statuses->skip(1)->first();
        
                return [
                    'child_id' => $child->id,
                    'entry' => $entry,
                    'exit' => $exit,
                ];
            });
            

            $ageGroupsPerCenter = $children->groupBy('child_development_center_id')->map(function ($childrenByCenter) use ($nutritionalStatusOccurrences) {
                return [
                    '2_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 2 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 2 && $child->sex_id == 2;
                        })->count(),
                    ],
                    '3_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 3 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 3 && $child->sex_id == 2;
                        })->count(),
                    ],
                    '4_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 4 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 4 && $child->sex_id == 2;
                        })->count(),
                    ],
                    '5_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 5 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 5 && $child->sex_id == 2;
                        })->count(),
                    ],
                ];
            });
                

            $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                ->where('is_funded', true)
                ->whereIn('child_development_center_id', $centerIds)
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
                ->whereIn('child_development_center_id', $centerIds)
                ->get()
                ->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) {
                    return [
                        'indigenous_people' => [
                            'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                        ],
                        'pantawid' => [
                            'male' => $childrenByCenter->whereNotNull('pantawid_details')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->whereNotNull('pantawid_details')->where('sex_id', 2)->count(),
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

            $centers = ChildDevelopmentCenter::paginate(15);
            $centers->getCollection()->keyBy('id');

            return view('reports.undernourished-upon-entry', compact('centers', 'countsPerCenter', 'countsPerCenterAndGender', 'ageGroupsPerCenter'));
            
        }
    }

    public function undernourishedAfter120()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            

        if (auth()->user()->hasRole('admin')) {

            $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
                $statuses = $child->nutritionalStatus->where('is_undernourish', true)
                                    ->whereIn('age_in_years', [2, 3, 4, 5]);
                $entry = $statuses->first();
                $exit = $statuses->skip(1)->first();
            
                return [
                    'child_id' => $child->id,
                    'entry' => $entry,
                    'exit' => $exit,
                ];
            });

            $exitAgeGroupsPerCenter = $fundedChildren->groupBy('child_development_center_id')->map(function ($childrenByCenter) use ($nutritionalStatusOccurrences) {
                return [
                    '2_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $exitStatus->age_in_years == 2 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $exitStatus->age_in_years == 2 && $child->sex_id == 2;
                        })->count(),
                    ],
                    '3_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $exitStatus->age_in_years == 3 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $exitStatus->age_in_years == 3 && $child->sex_id == 2;
                        })->count(),
                    ],
                    '4_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $exitStatus->age_in_years == 4 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $exitStatus->age_in_years == 4 && $child->sex_id == 2;
                        })->count(),
                    ],
                    '5_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $exitStatus->age_in_years == 5 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $exitStatus->age_in_years == 5 && $child->sex_id == 2;
                        })->count(),
                    ],
                ];
            });

            $exitCountsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
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

            $exitCountsPerCenter = Child::with('center')
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
                            'male' => $childrenByCenter->whereNotNull('pantawid_details')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->whereNotNull('pantawid_details')->where('sex_id', 2)->count(),
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

            $centers = ChildDevelopmentCenter::paginate(15);
            $centers->getCollection()->keyBy('id');

            return view('reports.undernourished-after-120', compact('centers', 'exitCountsPerCenter', 'exitCountsPerCenterAndGender', 'exitAgeGroupsPerCenter'));
        
        } elseif(auth()->user()->hasRole('lgu focal')) {
            
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');
            
            $children = Child::whereHas('center', function ($query) use ($centerIds) {
                $query->whereIn('id', $centerIds);
                })
                ->where('is_funded', true)
                ->get();

            
            $nutritionalStatusOccurrences = $children->map(function ($child) {
                if ($child->nutritionalStatus->isEmpty()) {
                    return [
                        'child_id' => $child->id,
                        'entry' => null,
                        'exit' => null,
                    ];
                }
                $statuses = $child->nutritionalStatus->where('is_undernourish', true)
                                    ->whereIn('age_in_years', [2, 3, 4, 5]);
                $entry = $statuses->first();
                $exit = $statuses->skip(1)->first();
        
                return [
                    'child_id' => $child->id,
                    'entry' => $entry,
                    'exit' => $exit,
                ];
            });
            

            $exitAgeGroupsPerCenter = $children->groupBy('child_development_center_id')->map(function ($childrenByCenter) use ($nutritionalStatusOccurrences) {
                return [
                    '2_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 2 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 2 && $child->sex_id == 2;
                        })->count(),
                    ],
                    '3_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 3 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 3 && $child->sex_id == 2;
                        })->count(),
                    ],
                    '4_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 4 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 4 && $child->sex_id == 2;
                        })->count(),
                    ],
                    '5_years_old' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 5 && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $status = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id);
                            $firstStatus = $status ? $status['entry'] : null;
                            return $firstStatus && $firstStatus->age_in_years == 5 && $child->sex_id == 2;
                        })->count(),
                    ],
                ];
            });
                

            $exitCountsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                ->where('is_funded', true)
                ->whereIn('child_development_center_id', $centerIds)
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

            $exitCountsPerCenter = Child::with('center')
                ->where('is_funded', true)
                ->whereIn('child_development_center_id', $centerIds)
                ->get()
                ->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) {
                    return [
                        'indigenous_people' => [
                            'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex_id', 2)->count(),
                        ],
                        'pantawid' => [
                            'male' => $childrenByCenter->whereNotNull('pantawid_details')->where('sex_id', 1)->count(),
                            'female' => $childrenByCenter->whereNotNull('pantawid_details')->where('sex_id', 2)->count(),
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

            $centers = ChildDevelopmentCenter::paginate(15);
            $centers->getCollection()->keyBy('id');

            return view('reports.undernourished-after-120', compact('centers', 'exitCountsPerCenter', 'exitCountsPerCenterAndGender', 'exitAgeGroupsPerCenter'));
        }
    }

    public function entryAgeBracket(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', null);

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

        if (auth()->user()->hasRole('admin')) {

            $isFunded = Child::with('nutritionalStatus')
                ->where('is_funded', true)
                ->paginate(10);

            $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
                $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5]);

                if ($statuses->isEmpty()) {
                    return [
                        'child_id' => $child->id,
                        'entry' => null,
                    ];
                }
                $entry = $statuses->first();
            
                return [
                    'child_id' => $child->id,
                    'entry' => $entry,
                ];
            });

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

            $countsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
                }])
                ->where('is_funded', true)
                ->get()
                ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                })
                ->groupBy(function ($child) {
                    return $child->nutritionalStatus->first()->age_in_years ?? null;
                })
                ->map(function ($childrenByAge) {
                    return [
                        'weight_for_age_normal' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_age == 'Normal';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_age == 'Normal';
                            })->count(),
                        ],
                        'weight_for_age_underweight' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_age == 'Underweight';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_age == 'Underweight';
                            })->count(),
                        ],
                        'weight_for_age_severely_underweight' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_age == 'Severely Underweight';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_age == 'Severely Underweight';
                            })->count(),
                        ],
                        'weight_for_age_overweight' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_age == 'Overweight';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_age == 'Overweight';
                            })->count(),
                        ],
                        'weight_for_height_normal' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Normal';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Normal';
                            })->count(),
                        ],
                        'weight_for_height_wasted' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Wasted';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Wasted';
                            })->count(),
                        ],
                        'weight_for_height_severely_wasted' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Severely Wasted';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Severely Wasted';
                            })->count(),
                        ],
                        'weight_for_height_overweight' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Overweight';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Overweight';
                            })->count(),
                        ],
                        'weight_for_height_obese' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Obese';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Obese';
                            })->count(),
                        ],
                        'height_for_age_normal' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->height_for_age == 'Normal';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->height_for_age == 'Normal';
                            })->count(),
                        ],
                        'height_for_age_stunted' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->height_for_age == 'Stunted';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->height_for_age == 'Stunted';
                            })->count(),
                        ],
                        'height_for_age_severely_stunted' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->height_for_age == 'Severely Stunted';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->height_for_age == 'Severely Stunted';
                            })->count(),
                        ],
                        'height_for_age_tall' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->height_for_age == 'Tall';
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->height_for_age == 'Tall';
                            })->count(),
                        ],
                        'is_undernourish' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->nutritionalStatus->first()->is_undernourish == true;
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->nutritionalStatus->first()->is_undernourish == true;
                            })->count(),
                        ],
                        'indigenous_people' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->is_indigenous_people == true;
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->is_indigenous_people == true;
                            })->count(),
                        ],
                        'pantawid' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->pantawid_details != null;
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->pantawid_details != null;
                            })->count(),
                        ],
                        'pwd' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->person_with_disability_details != null;
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->person_with_disability_details != null;
                            })->count(),
                        ],
                        'lactose_intolerant' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->is_lactose_intolerant == true;
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->is_lactose_intolerant == true;
                            })->count(),
                        ],
                        'child_of_soloparent' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->is_child_of_soloparent == true;
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->is_child_of_soloparent == true;
                            })->count(),
                        ],
                        'dewormed' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->deworming_date != null;
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->deworming_date != null;
                            })->count(),
                        ],
                        'vitamin_a' => [
                            'male' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 1 && $child->vitamin_a_date != null;
                            })->count(),
                            'female' => $childrenByAge->filter(function ($child) {
                                return $child->sex_id == 2 && $child->is_child_of_soloparent != null;
                            })->count(),
                        ],
                    
                    ];
                });

            $centers = ChildDevelopmentCenter::paginate(15);
            $centers->getCollection()->keyBy('id');
            return view('reports.age-bracket-upon-entry', 
            compact('isFunded',
                'countsPerCenterAndGender',
                            'countsPerNutritionalStatus',
                            'centers', 'cdcId'));
        }
    }

    public function after120AgeBracket()
    {

        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }
        $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            

        if (auth()->user()->hasRole('admin')) {

            $isFunded = Child::with('nutritionalStatus')
                ->where('is_funded', true)
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

            $countsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                }])
                ->where('is_funded', true)
                ->get()
                ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                })
                ->map(function ($child) {
                    $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5]);
                
                    $exit = $statuses->skip(1)->first();
                
                    return [
                        'child_id' => $child->id,
                        'sex_id' => $child->sex_id,
                        'exit' => $exit,
                    ];
                })
                ->filter(function ($child) {
                    return $child['exit'] !== null;
                })
                ->groupBy(function ($child) {
                    return $child['exit']->age_in_years ?? null;
                })
                ->map(function ($childrenByAge) {
                    return [
                    'weight_for_age_normal' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->weight_for_age == 'Normal';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->weight_for_age == 'Normal';
                        })->count(),
                    ],
                    'weight_for_age_underweight' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->weight_for_age == 'Underweight';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->weight_for_age == 'Underweight';
                        })->count(),
                    ],
                    'weight_for_age_severely_underweight' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->weight_for_age == 'Severely Underweight';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->weight_for_age == 'Severely Underweight';
                        })->count(),
                    ],
                    'weight_for_age_overweight' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->weight_for_age == 'Overweight';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->weight_for_age == 'Overweight';
                        })->count(),
                    ],
                    'weight_for_height_normal' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->weight_for_height == 'Normal';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->weight_for_height == 'Normal';
                        })->count(),
                    ],
                    'weight_for_height_wasted' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->weight_for_height == 'Wasted';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->weight_for_height == 'Wasted';
                        })->count(),
                    ],
                    'weight_for_height_severely_wasted' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->weight_for_height == 'Severely Wasted';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->weight_for_height == 'Severely Wasted';
                        })->count(),
                    ],
                    'weight_for_height_overweight' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->weight_for_height == 'Overweight';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->weight_for_height == 'Overweight';
                        })->count(),
                    ],
                    'weight_for_height_obese' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->weight_for_height == 'Obese';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->weight_for_height == 'Obese';
                        })->count(),
                    ],
                    'height_for_age_normal' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->height_for_age == 'Normal';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->height_for_age == 'Normal';
                        })->count(),
                    ],
                    'height_for_age_stunted' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->height_for_age == 'Stunted';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->height_for_age == 'Stunted';
                        })->count(),
                    ],
                    'height_for_age_severely_stunted' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->height_for_age == 'Severely Stunted';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->height_for_age == 'Severely Stunted';
                        })->count(),
                    ],
                    'height_for_age_tall' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->height_for_age == 'Tall';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->height_for_age == 'Tall';
                        })->count(),
                    ],
                    'is_undernourish' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->is_undernourish == true;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->is_undernourish == true;
                        })->count(),
                    ],
                    'indigenous_people' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->is_indigenous_people == true;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->is_indigenous_people == true;
                        })->count(),
                    ],
                    'pantawid' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->pantawid_details != null;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->pantawid_details != null;
                        })->count(),
                    ],
                    'pwd' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 &&$child['exit']->person_with_disability_details != null;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->person_with_disability_details != null;
                        })->count(),
                    ],
                    'lactose_intolerant' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->is_lactose_intolerant == true;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->is_lactose_intolerant == true;
                        })->count(),
                    ],
                    'child_of_soloparent' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child->is_child_of_soloparent == true;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->is_child_of_soloparent == true;
                        })->count(),
                    ],
                    'dewormed' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->deworming_date != null;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->deworming_date != null;
                        })->count(),
                    ],
                    'vitamin_a' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 1 && $child['exit']->vitamin_a_date != null;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child['sex_id'] == 2 && $child['exit']->is_child_of_soloparent != null;
                        })->count(),
                    ],

                ];
            });

            $centers = ChildDevelopmentCenter::paginate(15);
            $centers->getCollection()->keyBy('id');
            return view('reports.age-bracket-after-120', 
            compact('isFunded',
                'countsPerCenterAndGender',
                            'countsPerNutritionalStatus',
                            'centers'));
        }
    }
    
    public function weightForAgeUponEntry()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }
        $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
                $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5])
                    ->whereIn('weight_for_age', ['Normal', 'Underweight', 'Severely Underweight', 'Overweight'])
                    ->whereIn('weight_for_height', ['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese'])
                    ->whereIn('height_for_age', ['Normal', 'Stunted', 'Severely Stunted', 'Tall']);

                if ($statuses->isEmpty()) {
                    return [
                        'child_id' => $child->id,
                        'entry' => null,
                    ];
                }
                $entry = $statuses->first();
            
                return [
                    'child_id' => $child->id,
                    'entry' => $entry,
                ];
            });

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

                $ageGroupsPerCenter = $fundedChildren->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) use ($nutritionalStatusOccurrences) {
                    return [
                        '2' => [
                            'weight_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '3' => [
                            'weight_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '4' => [
                            'weight_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '5' => [
                            'weight_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                    ];
                });

            $totals = [];

            foreach ($ageGroupsPerCenter as $centerId => $ageGroup) {
                
                $totals[$centerId]['2']['male'] = 
                    ($ageGroup['2']['weight_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_age']['underweight']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_age']['severely_underweight']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_age']['overweight']['male'] ?? 0);
                        
                $totals[$centerId]['3']['male'] = 
                    ($ageGroup['3']['weight_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_age']['underweight']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_age']['severely_underweight']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_age']['overweight']['male'] ?? 0);

                $totals[$centerId]['4']['male'] = 
                    ($ageGroup['4']['weight_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_age']['underweight']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_age']['severely_underweight']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_age']['overweight']['male'] ?? 0);

                $totals[$centerId]['5']['male'] = 
                    ($ageGroup['5']['weight_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_age']['underweight']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_age']['severely_underweight']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_age']['overweight']['male'] ?? 0);

                $totals[$centerId]['2']['female'] = 
                    ($ageGroup['2']['weight_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_age']['underweight']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_age']['severely_underweight']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_age']['overweight']['female'] ?? 0);

                $totals[$centerId]['3']['female'] = 
                    ($ageGroup['3']['weight_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_age']['underweight']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_age']['severely_underweight']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_age']['overweight']['female'] ?? 0);

                $totals[$centerId]['4']['female'] = 
                    ($ageGroup['4']['weight_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_age']['underweight']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_age']['severely_underweight']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_age']['overweight']['female'] ?? 0);

                $totals[$centerId]['5']['female'] = 
                    ($ageGroup['5']['weight_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_age']['underweight']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_age']['severely_underweight']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_age']['overweight']['female'] ?? 0);

                $totals[$centerId]['total_male'] = 
                    ($totals[$centerId]['2']['male'] ?? 0) +
                    ($totals[$centerId]['3']['male'] ?? 0) +
                    ($totals[$centerId]['4']['male'] ?? 0) +
                    ($totals[$centerId]['5']['male'] ?? 0);

                $totals[$centerId]['total_female'] = 
                    ($totals[$centerId]['2']['female'] ?? 0) +
                    ($totals[$centerId]['3']['female'] ?? 0) +
                    ($totals[$centerId]['4']['female'] ?? 0) +
                    ($totals[$centerId]['5']['female'] ?? 0);
                
                $totals[$centerId]['total_served'] = 
                    ($totals[$centerId]['total_male']) +
                    ($totals[$centerId]['total_female']);  
                    
            }
            
            $centers = ChildDevelopmentCenter::with('user')->paginate(15);
            $centers->getCollection()->keyBy('id');
            return view('reports.weight-for-age-upon-entry', compact('centers', 'countsPerCenterAndGender', 'ageGroupsPerCenter', 'totals'));
        
    }

    public function weightForAgeAfter120()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }
        $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
                $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5])
                    ->whereIn('weight_for_age', ['Normal', 'Underweight', 'Severely Underweight', 'Overweight'])
                    ->whereIn('weight_for_height', ['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese'])
                    ->whereIn('height_for_age', ['Normal', 'Stunted', 'Severely Stunted', 'Tall']);

                if ($statuses->isEmpty()) {
                    return [
                        'child_id' => $child->id,
                        'exit' => null,
                    ];
                }
                $exit = $statuses->skip(1)->first();
            
                return [
                    'child_id' => $child->id,
                    'exit' => $exit,
                ];
            });

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

                $ageGroupsPerCenter = $fundedChildren->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) use ($nutritionalStatusOccurrences) {
                    return [
                        '2' => [
                            'weight_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '3' => [
                            'weight_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '4' => [
                            'weight_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '5' => [
                            'weight_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_underweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Severely Underweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_age == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                    ];
                });

            $totals = [];

            foreach ($ageGroupsPerCenter as $centerId => $ageGroup) {
                
                $totals[$centerId]['2']['male'] = 
                    ($ageGroup['2']['weight_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_age']['underweight']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_age']['severely_underweight']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_age']['overweight']['male'] ?? 0);
                        
                $totals[$centerId]['3']['male'] = 
                    ($ageGroup['3']['weight_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_age']['underweight']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_age']['severely_underweight']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_age']['overweight']['male'] ?? 0);

                $totals[$centerId]['4']['male'] = 
                    ($ageGroup['4']['weight_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_age']['underweight']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_age']['severely_underweight']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_age']['overweight']['male'] ?? 0);

                $totals[$centerId]['5']['male'] = 
                    ($ageGroup['5']['weight_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_age']['underweight']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_age']['severely_underweight']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_age']['overweight']['male'] ?? 0);

                $totals[$centerId]['2']['female'] = 
                    ($ageGroup['2']['weight_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_age']['underweight']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_age']['severely_underweight']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_age']['overweight']['female'] ?? 0);

                $totals[$centerId]['3']['female'] = 
                    ($ageGroup['3']['weight_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_age']['underweight']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_age']['severely_underweight']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_age']['overweight']['female'] ?? 0);

                $totals[$centerId]['4']['female'] = 
                    ($ageGroup['4']['weight_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_age']['underweight']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_age']['severely_underweight']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_age']['overweight']['female'] ?? 0);

                $totals[$centerId]['5']['female'] = 
                    ($ageGroup['5']['weight_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_age']['underweight']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_age']['severely_underweight']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_age']['overweight']['female'] ?? 0);

                $totals[$centerId]['total_male'] = 
                    ($totals[$centerId]['2']['male'] ?? 0) +
                    ($totals[$centerId]['3']['male'] ?? 0) +
                    ($totals[$centerId]['4']['male'] ?? 0) +
                    ($totals[$centerId]['5']['male'] ?? 0);

                $totals[$centerId]['total_female'] = 
                    ($totals[$centerId]['2']['female'] ?? 0) +
                    ($totals[$centerId]['3']['female'] ?? 0) +
                    ($totals[$centerId]['4']['female'] ?? 0) +
                    ($totals[$centerId]['5']['female'] ?? 0);
                
                $totals[$centerId]['total_served'] = 
                    ($totals[$centerId]['total_male']) +
                    ($totals[$centerId]['total_female']);  
                    
            }
            
            $centers = ChildDevelopmentCenter::with('user')->paginate(15);
            $centers->getCollection()->keyBy('id');

            return view('reports.weight-for-age-after-120', compact('centers', 'countsPerCenterAndGender', 'ageGroupsPerCenter', 'totals'));
    }

    public function weightForHeightUponEntry()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }
        $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
                $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5])
                    ->whereIn('weight_for_height', ['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese']);

                if ($statuses->isEmpty()) {
                    return [
                        'child_id' => $child->id,
                        'entry' => null,
                    ];
                }
                $entry = $statuses->first();
            
                return [
                    'child_id' => $child->id,
                    'entry' => $entry,
                ];
            });

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

                $ageGroupsPerCenter = $fundedChildren->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) use ($nutritionalStatusOccurrences) {
                    return [
                        '2' => [
                            'weight_for_height' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'obese' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '3' => [
                            'weight_for_height' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'obese' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '4' => [
                            'weight_for_height' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'obese' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '5' => [
                            'weight_for_height' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'obese' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                    ];
                });

            $totals = [];

            foreach ($ageGroupsPerCenter as $centerId => $ageGroup) {
                
                $totals[$centerId]['2']['male'] = 
                    ($ageGroup['2']['weight_for_height']['normal']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['wasted']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['severely_wasted']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['overweight']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['obese']['male'] ?? 0);
                        
                $totals[$centerId]['3']['male'] = 
                    ($ageGroup['3']['weight_for_height']['normal']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['wasted']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['severely_wasted']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['overweight']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['obese']['male'] ?? 0);

                $totals[$centerId]['4']['male'] = 
                    ($ageGroup['4']['weight_for_height']['normal']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['wasted']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['severely_wasted']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['overweight']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['obese']['male'] ?? 0);

                $totals[$centerId]['5']['male'] = 
                    ($ageGroup['5']['weight_for_height']['normal']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['wasted']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['severely_wasted']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['overweight']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['obese']['male'] ?? 0);

                $totals[$centerId]['2']['female'] = 
                    ($ageGroup['2']['weight_for_height']['normal']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['wasted']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['severely_wasted']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['overweight']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['obese']['female'] ?? 0);

                $totals[$centerId]['3']['female'] = 
                    ($ageGroup['3']['weight_for_height']['normal']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['wasted']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['severely_wasted']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['overweight']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['obese']['female'] ?? 0);

                $totals[$centerId]['4']['female'] = 
                    ($ageGroup['4']['weight_for_height']['normal']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['wasted']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['severely_wasted']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['overweight']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['obese']['female'] ?? 0);

                $totals[$centerId]['5']['female'] = 
                    ($ageGroup['5']['weight_for_height']['normal']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['wasted']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['severely_wasted']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['overweight']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['obese']['female'] ?? 0);

                $totals[$centerId]['total_male'] = 
                    ($totals[$centerId]['2']['male'] ?? 0) +
                    ($totals[$centerId]['3']['male'] ?? 0) +
                    ($totals[$centerId]['4']['male'] ?? 0) +
                    ($totals[$centerId]['5']['male'] ?? 0);

                $totals[$centerId]['total_female'] = 
                    ($totals[$centerId]['2']['female'] ?? 0) +
                    ($totals[$centerId]['3']['female'] ?? 0) +
                    ($totals[$centerId]['4']['female'] ?? 0) +
                    ($totals[$centerId]['5']['female'] ?? 0);
                
                $totals[$centerId]['total_served'] = 
                    ($totals[$centerId]['total_male']) +
                    ($totals[$centerId]['total_female']);  
                    
            }
            
            $centers = ChildDevelopmentCenter::with('user')->paginate(15);
            $centers->getCollection()->keyBy('id');

            return view('reports.weight-for-height-upon-entry', compact('centers', 'countsPerCenterAndGender', 'ageGroupsPerCenter', 'totals'));
    }

    public function weightForHeightAfter120()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }
        $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
                $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5])
                    ->whereIn('weight_for_height', ['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese']);

                if ($statuses->isEmpty()) {
                    return [
                        'child_id' => $child->id,
                        'exit' => null,
                    ];
                }
                $exit = $statuses->skip(1)->first();
            
                return [
                    'child_id' => $child->id,
                    'exit' => $exit,
                ];
            });

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

                $ageGroupsPerCenter = $fundedChildren->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) use ($nutritionalStatusOccurrences) {
                    return [
                        '2' => [
                            'weight_for_height' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'obese' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '3' => [
                            'weight_for_height' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'obese' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '4' => [
                            'weight_for_height' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'obese' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '5' => [
                            'weight_for_height' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_wasted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Severely Wasted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'overweight' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Overweight' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'obese' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->weight_for_height == 'Obese' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                    ];
                });

            $totals = [];

            foreach ($ageGroupsPerCenter as $centerId => $ageGroup) {
                
                $totals[$centerId]['2']['male'] = 
                    ($ageGroup['2']['weight_for_height']['normal']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['wasted']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['severely_wasted']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['overweight']['male'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['obese']['male'] ?? 0);
                        
                $totals[$centerId]['3']['male'] = 
                    ($ageGroup['3']['weight_for_height']['normal']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['wasted']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['severely_wasted']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['overweight']['male'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['obese']['male'] ?? 0);

                $totals[$centerId]['4']['male'] = 
                    ($ageGroup['4']['weight_for_height']['normal']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['wasted']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['severely_wasted']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['overweight']['male'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['obese']['male'] ?? 0);

                $totals[$centerId]['5']['male'] = 
                    ($ageGroup['5']['weight_for_height']['normal']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['wasted']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['severely_wasted']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['overweight']['male'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['obese']['male'] ?? 0);

                $totals[$centerId]['2']['female'] = 
                    ($ageGroup['2']['weight_for_height']['normal']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['wasted']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['severely_wasted']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['overweight']['female'] ?? 0) +
                    ($ageGroup['2']['weight_for_height']['obese']['female'] ?? 0);

                $totals[$centerId]['3']['female'] = 
                    ($ageGroup['3']['weight_for_height']['normal']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['wasted']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['severely_wasted']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['overweight']['female'] ?? 0) +
                    ($ageGroup['3']['weight_for_height']['obese']['female'] ?? 0);

                $totals[$centerId]['4']['female'] = 
                    ($ageGroup['4']['weight_for_height']['normal']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['wasted']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['severely_wasted']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['overweight']['female'] ?? 0) +
                    ($ageGroup['4']['weight_for_height']['obese']['female'] ?? 0);

                $totals[$centerId]['5']['female'] = 
                    ($ageGroup['5']['weight_for_height']['normal']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['wasted']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['severely_wasted']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['overweight']['female'] ?? 0) +
                    ($ageGroup['5']['weight_for_height']['obese']['female'] ?? 0);

                $totals[$centerId]['total_male'] = 
                    ($totals[$centerId]['2']['male'] ?? 0) +
                    ($totals[$centerId]['3']['male'] ?? 0) +
                    ($totals[$centerId]['4']['male'] ?? 0) +
                    ($totals[$centerId]['5']['male'] ?? 0);

                $totals[$centerId]['total_female'] = 
                    ($totals[$centerId]['2']['female'] ?? 0) +
                    ($totals[$centerId]['3']['female'] ?? 0) +
                    ($totals[$centerId]['4']['female'] ?? 0) +
                    ($totals[$centerId]['5']['female'] ?? 0);
                
                $totals[$centerId]['total_served'] = 
                    ($totals[$centerId]['total_male']) +
                    ($totals[$centerId]['total_female']);  
                    
            }
            
            $centers = ChildDevelopmentCenter::with('user')->paginate(15);
            $centers->getCollection()->keyBy('id');

            return view('reports.weight-for-height-after-120', compact('centers', 'countsPerCenterAndGender', 'ageGroupsPerCenter', 'totals'));
    }

    public function heightForAgeUponEntry()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }
        $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
                $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5])
                    ->whereIn('height_for_age', ['Normal', 'Stunted', 'Severely Stunted', 'Tall']);

                if ($statuses->isEmpty()) {
                    return [
                        'child_id' => $child->id,
                        'entry' => null,
                    ];
                }
                $entry = $statuses->first();
            
                return [
                    'child_id' => $child->id,
                    'entry' => $entry,
                ];
            });

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

                $ageGroupsPerCenter = $fundedChildren->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) use ($nutritionalStatusOccurrences) {
                    return [
                        '2' => [
                            'height_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'tall' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '3' => [
                            'height_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'tall' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '4' => [
                            'height_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'tall' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '5' => [
                            'height_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'tall' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                    ];
                });

            $totals = [];

            foreach ($ageGroupsPerCenter as $centerId => $ageGroup) {
                
                $totals[$centerId]['2']['male'] = 
                    ($ageGroup['2']['height_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['2']['height_for_age']['stunted']['male'] ?? 0) +
                    ($ageGroup['2']['height_for_age']['severely_stunted']['male'] ?? 0) +
                    ($ageGroup['2']['height_for_age']['tall']['male'] ?? 0);
                        
                $totals[$centerId]['3']['male'] = 
                    ($ageGroup['3']['height_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['3']['height_for_age']['stunted']['male'] ?? 0) +
                    ($ageGroup['3']['height_for_age']['severely_stunted']['male'] ?? 0) +
                    ($ageGroup['3']['height_for_age']['tall']['male'] ?? 0);

                $totals[$centerId]['4']['male'] = 
                    ($ageGroup['4']['height_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['4']['height_for_age']['stunted']['male'] ?? 0) +
                    ($ageGroup['4']['height_for_age']['severely_stunted']['male'] ?? 0) +
                    ($ageGroup['4']['height_for_age']['tall']['male'] ?? 0);

                $totals[$centerId]['5']['male'] = 
                    ($ageGroup['5']['height_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['5']['height_for_age']['stunted']['male'] ?? 0) +
                    ($ageGroup['5']['height_for_age']['severely_stunted']['male'] ?? 0) +
                    ($ageGroup['5']['height_for_age']['tall']['male'] ?? 0);

                $totals[$centerId]['2']['female'] = 
                    ($ageGroup['2']['height_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['2']['height_for_age']['stunted']['female'] ?? 0) +
                    ($ageGroup['2']['height_for_age']['severely_stunted']['female'] ?? 0) +
                    ($ageGroup['2']['height_for_age']['tall']['female'] ?? 0);

                $totals[$centerId]['3']['female'] = 
                    ($ageGroup['3']['height_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['3']['height_for_age']['stunted']['female'] ?? 0) +
                    ($ageGroup['3']['height_for_age']['severely_stunted']['female'] ?? 0) +
                    ($ageGroup['3']['height_for_age']['tall']['female'] ?? 0);

                $totals[$centerId]['4']['female'] = 
                    ($ageGroup['4']['height_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['4']['height_for_age']['stunted']['female'] ?? 0) +
                    ($ageGroup['4']['height_for_age']['severely_stunted']['female'] ?? 0) +
                    ($ageGroup['4']['height_for_age']['tall']['female'] ?? 0);

                $totals[$centerId]['5']['female'] = 
                    ($ageGroup['5']['height_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['5']['height_for_age']['stunted']['female'] ?? 0) +
                    ($ageGroup['5']['height_for_age']['severely_stunted']['female'] ?? 0) +
                    ($ageGroup['5']['height_for_age']['tall']['female'] ?? 0);

                $totals[$centerId]['total_male'] = 
                    ($totals[$centerId]['2']['male'] ?? 0) +
                    ($totals[$centerId]['3']['male'] ?? 0) +
                    ($totals[$centerId]['4']['male'] ?? 0) +
                    ($totals[$centerId]['5']['male'] ?? 0);

                $totals[$centerId]['total_female'] = 
                    ($totals[$centerId]['2']['female'] ?? 0) +
                    ($totals[$centerId]['3']['female'] ?? 0) +
                    ($totals[$centerId]['4']['female'] ?? 0) +
                    ($totals[$centerId]['5']['female'] ?? 0);
                
                $totals[$centerId]['total_served'] = 
                    ($totals[$centerId]['total_male']) +
                    ($totals[$centerId]['total_female']);  
                    
            }
            
            $centers = ChildDevelopmentCenter::with('user')->paginate(15);
            $centers->getCollection()->keyBy('id');

            return view('reports.height-for-age-upon-entry', compact('centers', 'countsPerCenterAndGender', 'ageGroupsPerCenter', 'totals'));
    }

    public function heightForAgeAfter120()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }
        $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
                $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5])
                    ->whereIn('height_for_age', ['Normal', 'Stunted', 'Severely Stunted', 'Tall']);

                if ($statuses->isEmpty()) {
                    return [
                        'child_id' => $child->id,
                        'exit' => null,
                    ];
                }
                $exit = $statuses->skip(1)->first();
            
                return [
                    'child_id' => $child->id,
                    'exit' => $exit,
                ];
            });

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

                $ageGroupsPerCenter = $fundedChildren->groupBy('child_development_center_id')
                ->map(function ($childrenByCenter) use ($nutritionalStatusOccurrences) {
                    return [
                        '2' => [
                            'height_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'tall' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 2 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '3' => [
                            'height_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'tall' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 3 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '4' => [
                            'height_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'tall' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 4 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                        '5' => [
                            'height_for_age' => [
                                'normal' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Normal' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'severely_stunted' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Severely Stunted' && $child->sex_id == 2;
                                    })->count(),
                                ],
                                'tall' => [
                                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 1;
                                    })->count(),
                                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                                        return $firstStatus && $firstStatus->age_in_years == 5 && $firstStatus->height_for_age == 'Tall' && $child->sex_id == 2;
                                    })->count(),
                                ],
                            ],
                        ],
                    ];
                });

            $totals = [];

            foreach ($ageGroupsPerCenter as $centerId => $ageGroup) {
                
                $totals[$centerId]['2']['male'] = 
                    ($ageGroup['2']['height_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['2']['height_for_age']['stunted']['male'] ?? 0) +
                    ($ageGroup['2']['height_for_age']['severely_stunted']['male'] ?? 0) +
                    ($ageGroup['2']['height_for_age']['tall']['male'] ?? 0);
                        
                $totals[$centerId]['3']['male'] = 
                    ($ageGroup['3']['height_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['3']['height_for_age']['stunted']['male'] ?? 0) +
                    ($ageGroup['3']['height_for_age']['severely_stunted']['male'] ?? 0) +
                    ($ageGroup['3']['height_for_age']['tall']['male'] ?? 0);

                $totals[$centerId]['4']['male'] = 
                    ($ageGroup['4']['height_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['4']['height_for_age']['stunted']['male'] ?? 0) +
                    ($ageGroup['4']['height_for_age']['severely_stunted']['male'] ?? 0) +
                    ($ageGroup['4']['height_for_age']['tall']['male'] ?? 0);

                $totals[$centerId]['5']['male'] = 
                    ($ageGroup['5']['height_for_age']['normal']['male'] ?? 0) +
                    ($ageGroup['5']['height_for_age']['stunted']['male'] ?? 0) +
                    ($ageGroup['5']['height_for_age']['severely_stunted']['male'] ?? 0) +
                    ($ageGroup['5']['height_for_age']['tall']['male'] ?? 0);

                $totals[$centerId]['2']['female'] = 
                    ($ageGroup['2']['height_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['2']['height_for_age']['stunted']['female'] ?? 0) +
                    ($ageGroup['2']['height_for_age']['severely_stunted']['female'] ?? 0) +
                    ($ageGroup['2']['height_for_age']['tall']['female'] ?? 0);

                $totals[$centerId]['3']['female'] = 
                    ($ageGroup['3']['height_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['3']['height_for_age']['stunted']['female'] ?? 0) +
                    ($ageGroup['3']['height_for_age']['severely_stunted']['female'] ?? 0) +
                    ($ageGroup['3']['height_for_age']['tall']['female'] ?? 0);

                $totals[$centerId]['4']['female'] = 
                    ($ageGroup['4']['height_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['4']['height_for_age']['stunted']['female'] ?? 0) +
                    ($ageGroup['4']['height_for_age']['severely_stunted']['female'] ?? 0) +
                    ($ageGroup['4']['height_for_age']['tall']['female'] ?? 0);

                $totals[$centerId]['5']['female'] = 
                    ($ageGroup['5']['height_for_age']['normal']['female'] ?? 0) +
                    ($ageGroup['5']['height_for_age']['stunted']['female'] ?? 0) +
                    ($ageGroup['5']['height_for_age']['severely_stunted']['female'] ?? 0) +
                    ($ageGroup['5']['height_for_age']['tall']['female'] ?? 0);

                $totals[$centerId]['total_male'] = 
                    ($totals[$centerId]['2']['male'] ?? 0) +
                    ($totals[$centerId]['3']['male'] ?? 0) +
                    ($totals[$centerId]['4']['male'] ?? 0) +
                    ($totals[$centerId]['5']['male'] ?? 0);

                $totals[$centerId]['total_female'] = 
                    ($totals[$centerId]['2']['female'] ?? 0) +
                    ($totals[$centerId]['3']['female'] ?? 0) +
                    ($totals[$centerId]['4']['female'] ?? 0) +
                    ($totals[$centerId]['5']['female'] ?? 0);
                
                $totals[$centerId]['total_served'] = 
                    ($totals[$centerId]['total_male']) +
                    ($totals[$centerId]['total_female']);  
                    
            }
            
            $centers = ChildDevelopmentCenter::with('user')->paginate(15);
            $centers->getCollection()->keyBy('id');

            return view('reports.height-for-age-after-120', compact('centers', 'countsPerCenterAndGender', 'ageGroupsPerCenter', 'totals'));
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

    public function filterMasterlist(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', null);

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        $fundedChildren = Child::with('nutritionalStatus', 'sex')
            ->where('is_funded', true)
            ->where('cycle_implementation_id', $cycleImplementation->id);


        if (auth()->user()->hasRole('admin')) {
            if($cdcId == 'all_center') {
                $isFunded = $fundedChildren->paginate(10);
            } else {
                $isFunded = $fundedChildren->where('child_development_center_id', $cdcId)->paginate(10);
            }

            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            return view('reports.index', compact('isFunded', 'centers', 'cdcId'));

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            if($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereIn('child_development_center_id', $centerIds)->paginate(10);
            } else {
                $isFunded = $fundedChildren->where('child_development_center_id', $cdcId)->paginate(10);
            }
            return view('reports.index', compact('isFunded', 'centers', 'cdcId'));

        } else {

            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            if($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereIn('child_development_center_id', $centerIds)->paginate(10);
            } else {
                $isFunded = $fundedChildren->where('child_development_center_id', $cdcId)->paginate(10);
            }
            
            return view('reports.index', compact('isFunded', 'centers', 'cdcId'));
        }
    }

    public function filterUnfunded(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', null);

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        $unfundedChildren = Child::with('nutritionalStatus', 'sex')
            ->where('is_funded', false)
            ->where('cycle_implementation_id', $cycleImplementation->id);


        if (auth()->user()->hasRole('admin')) {
            if($cdcId == 'all_center') {
                $isNotFunded = $unfundedChildren->paginate(10);
            } else {
                $isNotFunded = $unfundedChildren->where('child_development_center_id', $cdcId)->paginate(10);
            }

            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            return view('reports.unfunded', compact('isNotFunded', 'centers', 'cdcId'));

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            if($cdcId == 'all_center') {
                $isNotFunded = $unfundedChildren->whereIn('child_development_center_id', $centerIds)->paginate(10);
            } else {
                $isNotFunded = $unfundedChildren->where('child_development_center_id', $cdcId)->paginate(10);
            }
            return view('reports.unfunded', compact('isNotFunded', 'centers', 'cdcId'));

        } else {

            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            if($cdcId == 'all_center') {
                $isNotFunded = $unfundedChildren->whereIn('child_development_center_id', $centerIds)->paginate(10);
            } else {
                $isNotFunded = $unfundedChildren->where('child_development_center_id', $cdcId)->paginate(10);
            }
            
            return view('reports.unfunded', compact('isNotFunded', 'centers', 'cdcId'));
        }
    }

    public function filterMonitoring(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', null);

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }
        $fundedChildren = Child::with('nutritionalStatus', 'sex')
            ->where('is_funded', true)
            ->where('cycle_implementation_id', $cycleImplementation->id)
            ->get();

        $childrenWithNutritionalStatus = [];

        foreach ($fundedChildren as $child) {
            $nutritionalStatuses = $child->nutritionalStatus;
            
            if ($nutritionalStatuses && $nutritionalStatuses->count() > 0) {
                $entry = $nutritionalStatuses->first();
                $exit = $nutritionalStatuses->count() > 1 ? $nutritionalStatuses[1] : null;
                
            } else {
                $entry = null;
                $exit = null;
            }

            $childrenWithNutritionalStatus[] = [
                'child' => $child,
                'entry' => $entry,
                'exit' => $exit,
            ];
        }

        if (auth()->user()->hasRole('admin')) {
            
            if($cdcId == 'all_center') {
                $isFunded = Child::with('nutritionalStatus', 'sex')
                    ->where('is_funded', true)
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);

            } else {
                $isFunded = Child::with('nutritionalStatus', 'sex')
                    ->where('is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->paginate(10);
            }
        
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
        
            return view('reports.monitoring', compact('isFunded', 'centers', 'cdcId'));
        
        } elseif (auth()->user()->hasRole('lgu focal')) {

            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            if($cdcId == 'all_center') {
                $isFunded = Child::with('nutritionalStatus', 'sex')
                    ->where('is_funded', true)
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->whereIn('child_development_center_id', $centerIds)
                    ->paginate(10);

            } else {
                $isFunded = Child::with('nutritionalStatus', 'sex')
                    ->where('is_funded', true)
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->where('child_development_center_id', $cdcId)
                    ->paginate(10);
            }

            return view('reports.monitoring', compact('isFunded', 'centers', 'cdcId'));
        
        } else {
            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');
            
            if ($cdcId == 'all_center') {
                $isFunded = Child::with('nutritionalStatus', 'sex')
                    ->where('is_funded', true)
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->whereIn('child_development_center_id', $centerIds)
                    ->paginate(10);
            } else {
                $isFunded = Child::with('nutritionalStatus', 'sex')
                    ->where('is_funded', true)
                    ->where('cycle_implementation_id', $cycleImplementation->id)
                    ->where('child_development_center_id', $cdcId)
                    ->paginate(10);
            }

            return view('reports.monitoring', compact('isFunded', 'centers', 'cdcId'));
        }        

    }

    public function filterEntryAgeBracket(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', null);

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

        if (auth()->user()->hasRole('admin')) {
            if($cdcId == 'all_center'){
                $isFunded = Child::with('nutritionalStatus')
                ->where('is_funded', true)
                ->paginate(10);

                $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
                    $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5]);

                    if ($statuses->isEmpty()) {
                        return [
                            'child_id' => $child->id,
                            'entry' => null,
                        ];
                    }
                    $entry = $statuses->first();
                
                    return [
                        'child_id' => $child->id,
                        'entry' => $entry,
                    ];
                });

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

                $countsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->get()
                    ->filter(function ($child) {
                        return $child->nutritionalStatus->isNotEmpty();
                    })
                    ->groupBy(function ($child) {
                        return $child->nutritionalStatus->first()->age_in_years ?? null;
                    })
                    ->map(function ($childrenByAge) {
                        return [
                            'weight_for_age_normal' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_age == 'Normal';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_age == 'Normal';
                                })->count(),
                            ],
                            'weight_for_age_underweight' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_age == 'Underweight';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_age == 'Underweight';
                                })->count(),
                            ],
                            'weight_for_age_severely_underweight' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_age == 'Severely Underweight';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_age == 'Severely Underweight';
                                })->count(),
                            ],
                            'weight_for_age_overweight' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_age == 'Overweight';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_age == 'Overweight';
                                })->count(),
                            ],
                            'weight_for_height_normal' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Normal';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Normal';
                                })->count(),
                            ],
                            'weight_for_height_wasted' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Wasted';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Wasted';
                                })->count(),
                            ],
                            'weight_for_height_severely_wasted' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Severely Wasted';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Severely Wasted';
                                })->count(),
                            ],
                            'weight_for_height_overweight' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Overweight';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Overweight';
                                })->count(),
                            ],
                            'weight_for_height_obese' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Obese';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Obese';
                                })->count(),
                            ],
                            'height_for_age_normal' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->height_for_age == 'Normal';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->height_for_age == 'Normal';
                                })->count(),
                            ],
                            'height_for_age_stunted' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->height_for_age == 'Stunted';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->height_for_age == 'Stunted';
                                })->count(),
                            ],
                            'height_for_age_severely_stunted' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->height_for_age == 'Severely Stunted';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->height_for_age == 'Severely Stunted';
                                })->count(),
                            ],
                            'height_for_age_tall' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->height_for_age == 'Tall';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->height_for_age == 'Tall';
                                })->count(),
                            ],
                            'is_undernourish' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->is_undernourish == true;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->is_undernourish == true;
                                })->count(),
                            ],
                            'indigenous_people' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->is_indigenous_people == true;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->is_indigenous_people == true;
                                })->count(),
                            ],
                            'pantawid' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->pantawid_details != null;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->pantawid_details != null;
                                })->count(),
                            ],
                            'pwd' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->person_with_disability_details != null;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->person_with_disability_details != null;
                                })->count(),
                            ],
                            'lactose_intolerant' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->is_lactose_intolerant == true;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->is_lactose_intolerant == true;
                                })->count(),
                            ],
                            'child_of_soloparent' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->is_child_of_soloparent == true;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->is_child_of_soloparent == true;
                                })->count(),
                            ],
                            'dewormed' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->deworming_date != null;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->deworming_date != null;
                                })->count(),
                            ],
                            'vitamin_a' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->vitamin_a_date != null;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->is_child_of_soloparent != null;
                                })->count(),
                            ],
                        
                        ];
                    });

            $centers = ChildDevelopmentCenter::paginate(15);
            $centers->getCollection()->keyBy('id');
            return view('reports.age-bracket-upon-entry', 
            compact('isFunded',
                'countsPerCenterAndGender',
                            'countsPerNutritionalStatus',
                            'centers', 'cdcId')); 
        } else {
                $isFunded = Child::with('nutritionalStatus')
                ->where('is_funded', true)
                ->paginate(10);

                $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
                    $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5]);

                    if ($statuses->isEmpty()) {
                        return [
                            'child_id' => $child->id,
                            'entry' => null,
                        ];
                    }
                    $entry = $statuses->first();
                
                    return [
                        'child_id' => $child->id,
                        'entry' => $entry,
                    ];
                });

                $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex_id')
                    ->where('is_funded', true)
                    ->where('child_development_center_id', $cdcId)
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

                $countsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->get()
                    ->filter(function ($child) {
                        return $child->nutritionalStatus->isNotEmpty();
                    })
                    ->groupBy(function ($child) {
                        return $child->nutritionalStatus->first()->age_in_years ?? null;
                    })
                    ->map(function ($childrenByAge) {
                        return [
                            'weight_for_age_normal' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_age == 'Normal';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_age == 'Normal';
                                })->count(),
                            ],
                            'weight_for_age_underweight' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_age == 'Underweight';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_age == 'Underweight';
                                })->count(),
                            ],
                            'weight_for_age_severely_underweight' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_age == 'Severely Underweight';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_age == 'Severely Underweight';
                                })->count(),
                            ],
                            'weight_for_age_overweight' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_age == 'Overweight';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_age == 'Overweight';
                                })->count(),
                            ],
                            'weight_for_height_normal' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Normal';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Normal';
                                })->count(),
                            ],
                            'weight_for_height_wasted' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Wasted';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Wasted';
                                })->count(),
                            ],
                            'weight_for_height_severely_wasted' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Severely Wasted';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Severely Wasted';
                                })->count(),
                            ],
                            'weight_for_height_overweight' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Overweight';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Overweight';
                                })->count(),
                            ],
                            'weight_for_height_obese' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->weight_for_height == 'Obese';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->weight_for_height == 'Obese';
                                })->count(),
                            ],
                            'height_for_age_normal' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->height_for_age == 'Normal';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->height_for_age == 'Normal';
                                })->count(),
                            ],
                            'height_for_age_stunted' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->height_for_age == 'Stunted';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->height_for_age == 'Stunted';
                                })->count(),
                            ],
                            'height_for_age_severely_stunted' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->height_for_age == 'Severely Stunted';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->height_for_age == 'Severely Stunted';
                                })->count(),
                            ],
                            'height_for_age_tall' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->height_for_age == 'Tall';
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->height_for_age == 'Tall';
                                })->count(),
                            ],
                            'is_undernourish' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->nutritionalStatus->first()->is_undernourish == true;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->nutritionalStatus->first()->is_undernourish == true;
                                })->count(),
                            ],
                            'indigenous_people' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->is_indigenous_people == true;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->is_indigenous_people == true;
                                })->count(),
                            ],
                            'pantawid' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->pantawid_details != null;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->pantawid_details != null;
                                })->count(),
                            ],
                            'pwd' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->person_with_disability_details != null;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->person_with_disability_details != null;
                                })->count(),
                            ],
                            'lactose_intolerant' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->is_lactose_intolerant == true;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->is_lactose_intolerant == true;
                                })->count(),
                            ],
                            'child_of_soloparent' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->is_child_of_soloparent == true;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->is_child_of_soloparent == true;
                                })->count(),
                            ],
                            'dewormed' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->deworming_date != null;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->deworming_date != null;
                                })->count(),
                            ],
                            'vitamin_a' => [
                                'male' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 1 && $child->vitamin_a_date != null;
                                })->count(),
                                'female' => $childrenByAge->filter(function ($child) {
                                    return $child->sex_id == 2 && $child->is_child_of_soloparent != null;
                                })->count(),
                            ],
                        
                        ];
                    });

                $centers = ChildDevelopmentCenter::paginate(15);
                $centers->getCollection()->keyBy('id');
                return view('reports.age-bracket-upon-entry', 
                compact('isFunded',
                    'countsPerCenterAndGender',
                                'countsPerNutritionalStatus',
                                'centers', 'cdcId')); 
            }

            
        }
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
