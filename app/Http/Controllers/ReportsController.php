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
        $cdcId = $request->input('center_name', 'all_center');

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        $fundedChildren = Child::with('nutritionalStatus', 'sex')
            ->where('is_funded', true)
            ->where('cycle_implementation_id', $cycleImplementation->id);

        if (auth()->user()->hasRole('admin')) {
            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->paginate(10);
            } else {
                $isFunded = $fundedChildren->where('child_development_center_id', $cdcId)->paginate(10);
            }
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            
        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereIn('child_development_center_id', $centerIds)->paginate(10);
            } else {
                $isFunded = $fundedChildren->whereIn('child_development_center_id', $cdcId)->paginate(10);
            }

        } else {

            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereIn('child_development_center_id', $centerIds)->paginate(10);
            } else {
                $isFunded = $fundedChildren->where('child_development_center_id', $cdcId)->paginate(10);
            }

        }

        return view('reports.index', compact('isFunded', 'centers', 'cdcId'));
    }
    public function malnourish(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');

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
                ->orderBy('child_development_center_id')
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
                ->orderBy('child_development_center_id')
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
            $isPwdChildren = $childrenWithDisabilities->orderBy('child_development_center_id')->paginate(10);

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
                ->orderBy('child_development_center_id')
                ->paginate(10);

            return view('reports.disabilities', compact('isPwdChildren', 'centers'));
        }
    }
    public function monitoring(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');

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
            $centers = ChildDevelopmentCenter::all()->keyBy('id');

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

        }        

        return view('reports.monitoring', compact('isFunded', 'centers', 'cdcId'));
    }
    public function unfunded(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        $unfundedChildren = Child::with('nutritionalStatus')
            ->where('is_funded', false)
            ->where('cycle_implementation_id', $cycleImplementation->id);


        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            if ($cdcId == 'all_center'){
                $isNotFunded = $unfundedChildren->paginate(20);
            } else{
                $isNotFunded = $unfundedChildren->where('child_development_center_id', $cdcId)
                ->paginate(20);
            }
        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center'){
                $isNotFunded = $unfundedChildren->whereIn('child_development_center_id', $centerIds)
                ->paginate(20);

            } else{
                $isNotFunded = $unfundedChildren->where('child_development_center_id', $cdcId)
                ->paginate(20);
            }

        } else {
            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center'){
                $isNotFunded = $unfundedChildren->whereIn('child_development_center_id', $centerIds)
                ->paginate(20);

            } else{
                $isNotFunded = $unfundedChildren->where('child_development_center_id', $cdcId)
                ->paginate(20);
            }

        }

        return view('reports.unfunded', compact('isNotFunded', 'centers', 'cdcId'));
    }
    public function undernourishedUponEntry()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        if (auth()->user()->hasRole('admin')) {
            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $centers = ChildDevelopmentCenter::paginate(15);
            $centers->getCollection()->keyBy('id');

        } else if (auth()->user()->hasRole('lgu focal')) {

            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->paginate(15);
            $centerIds = $centers->pluck('id');

            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereIn('children.child_development_center_id', $centerIds)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();
        }

        $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
            if ($child->nutritionalStatus->isEmpty()) {
                return [
                    'child_id' => $child->id,
                    'entry' => null,
                ];
            }
            $statuses = $child->nutritionalStatus->where('is_undernourish', true)
                ->whereIn('age_in_years', [2, 3, 4, 5]);
            $entry = $statuses->first();

            return [
                'child_id' => $child->id,
                'entry' => $entry,
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
                'indigenous_people' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->is_indegenous_people == true && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->is_indegenous_people == true && $child->sex_id == 2;
                    })->count(),
                ],
                'pantawid' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->pantawid_details != null && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->pantawid_details != null && $child->sex_id == 2;
                    })->count(),
                ],
                'pwd' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->person_with_disability_details != null && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->person_with_disability_details != null && $child->sex_id == 2;
                    })->count(),
                ],
                'lactose_intolerant' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->is_lactose_intolerant == true && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->is_lactose_intolerant == true && $child->sex_id == 2;
                    })->count(),
                ],
                'child_of_solo_parent' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->is_child_of_soloparent == true && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $child->is_child_of_soloparent == true && $child->sex_id == 2;
                    })->count(),
                ],
                'dewormed' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->deworming_date != null && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->deworming_date != null && $child->sex_id == 2;
                    })->count(),
                ],
                'vitamin_a' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->vitamin_a_date != null && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                        return $firstStatus && $child->vitamin_a_date != null && $child->sex_id == 2;
                    })->count(),
                ],

            ];
        });

        return view('reports.undernourished-upon-entry', compact('centers', 'ageGroupsPerCenter'));
    }
    public function undernourishedAfter120()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        if (auth()->user()->hasRole('admin')) {
            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();
            
            $centers = ChildDevelopmentCenter::paginate(15);
            $centers->getCollection()->keyBy('id');

        } elseif (auth()->user()->hasRole('lgu focal')) {

            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->paginate(15);
            $centerIds = $centers->pluck('id');

            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereIn('children.child_development_center_id', $centerIds)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();
        }
        
        $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
            if ($child->nutritionalStatus->isEmpty()) {
                return [
                    'child_id' => $child->id,
                    'exit' => null,
                ];
            }
            $statuses = $child->nutritionalStatus->where('is_undernourish', true)
                ->whereIn('age_in_years', [2, 3, 4, 5]);
            $exit = $statuses->skip(1)->first();

            return [
                'child_id' => $child->id,
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
                'indigenous_people' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->is_indegenous_people == true && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->is_indegenous_people == true && $child->sex_id == 2;
                    })->count(),
                ],
                'pantawid' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->pantawid_details != null && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->pantawid_details != null && $child->sex_id == 2;
                    })->count(),
                ],
                'pwd' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->person_with_disability_details != null && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->person_with_disability_details != null && $child->sex_id == 2;
                    })->count(),
                ],
                'lactose_intolerant' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->is_lactose_intolerant == true && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->is_lactose_intolerant == true && $child->sex_id == 2;
                    })->count(),
                ],
                'child_of_solo_parent' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->is_child_of_soloparent == true && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $child->is_child_of_soloparent == true && $child->sex_id == 2;
                    })->count(),
                ],
                'dewormed' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->deworming_date != null && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->deworming_date != null && $child->sex_id == 2;
                    })->count(),
                ],
                'vitamin_a' => [
                    'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->vitamin_a_date != null && $child->sex_id == 1;
                    })->count(),
                    'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                        $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                        return $firstStatus && $child->vitamin_a_date != null && $child->sex_id == 2;
                    })->count(),
                ],

            ];
        });

        return view('reports.undernourished-after-120', compact('centers', 'exitAgeGroupsPerCenter'));
    }
    public function entryAgeBracket(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        if (auth()->user()->hasRole('admin')) {
            if($cdcId == 'all_center'){
                $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
                    ->where('children.is_funded', true)
                    ->where('children.cycle_implementation_id', $cycleImplementation->id)
                    ->whereHas('nutritionalStatus', function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->get()
                    ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                });

            } else {
                $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
                    ->where('children.is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->where('children.cycle_implementation_id', $cycleImplementation->id)
                    ->whereHas('nutritionalStatus', function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->get()
                    ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                });
            }

            $centers = ChildDevelopmentCenter::all();
            $centerIds = $centers->pluck('id');

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            if($cdcId == 'all_center'){
                $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
                    ->where('children.is_funded', true)
                    ->whereIn('children.child_development_center_id', $centerIds)
                    ->where('children.cycle_implementation_id', $cycleImplementation->id)
                    ->whereHas('nutritionalStatus', function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->whereIn('child_development_center_id', $centerIds)
                    ->get()
                    ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                });

            } else {
                $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
                    ->where('children.is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->where('children.cycle_implementation_id', $cycleImplementation->id)
                    ->whereHas('nutritionalStatus', function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->get()
                    ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                });
            }

        } else {
            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            if($cdcId == 'all_center'){
                $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
                    ->where('children.is_funded', true)
                    ->whereIn('children.child_development_center_id', $centerIds)
                    ->where('children.cycle_implementation_id', $cycleImplementation->id)
                    ->whereHas('nutritionalStatus', function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->whereIn('child_development_center_id', $centerIds)
                    ->get()
                    ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                });

            } else {
                $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
                    ->where('children.is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->where('children.cycle_implementation_id', $cycleImplementation->id)
                    ->whereHas('nutritionalStatus', function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->get()
                    ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                });
            }
        }

        $countsPerNutritionalStatus = $allCountsPerNutritionalStatus->groupBy(function ($child) {
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

        
        return view('reports.age-bracket-upon-entry', compact('fundedChildren','countsPerNutritionalStatus', 'centers', 'cdcId'));
    }
    public function after120AgeBracket(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        if (auth()->user()->hasRole('admin')) {
            if($cdcId == 'all_center'){
                $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
                    ->where('children.is_funded', true)
                    ->where('children.cycle_implementation_id', $cycleImplementation->id)
                    ->whereHas('nutritionalStatus', function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->get()
                    ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                });

            } else {
                $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
                    ->where('children.is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->where('children.cycle_implementation_id', $cycleImplementation->id)
                    ->whereHas('nutritionalStatus', function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->get()
                    ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                });
            }

            $centers = ChildDevelopmentCenter::all();
            $centerIds = $centers->pluck('id');

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            if($cdcId == 'all_center'){
                $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
                    ->where('children.is_funded', true)
                    ->whereIn('children.child_development_center_id', $centerIds)
                    ->where('children.cycle_implementation_id', $cycleImplementation->id)
                    ->whereHas('nutritionalStatus', function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->whereIn('child_development_center_id', $centerIds)
                    ->get()
                    ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                });

            } else {
                $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
                    ->where('children.is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->where('children.cycle_implementation_id', $cycleImplementation->id)
                    ->whereHas('nutritionalStatus', function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->get()
                    ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                });
            }

        } else {
            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            if($cdcId == 'all_center'){
                $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
                    ->where('children.is_funded', true)
                    ->whereIn('children.child_development_center_id', $centerIds)
                    ->where('children.cycle_implementation_id', $cycleImplementation->id)
                    ->whereHas('nutritionalStatus', function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->whereIn('child_development_center_id', $centerIds)
                    ->get()
                    ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                });

            } else {
                $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
                    ->where('children.is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->where('children.cycle_implementation_id', $cycleImplementation->id)
                    ->whereHas('nutritionalStatus', function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with(['nutritionalStatus' => function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }])
                    ->where('is_funded', true)
                    ->where('child_development_center_id', $cdcId)
                    ->get()
                    ->filter(function ($child) {
                    return $child->nutritionalStatus->isNotEmpty();
                });
            }
        }

        $countsPerNutritionalStatus = $allCountsPerNutritionalStatus->map(function ($child) {
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

        return view('reports.age-bracket-after-120', compact('fundedChildren', 'countsPerNutritionalStatus', 'centers', 'cdcId'));
    }
    public function weightForAgeUponEntry()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        if(auth()->user()->hasRole('admin')){
            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $centers = ChildDevelopmentCenter::paginate(20);
            $centers->getCollection()->keyBy('id');

        } elseif(auth()->user()->hasRole('lgu focal')){
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->paginate(20);
            $centerIds = $centers->pluck('id');

            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereIn('children.child_development_center_id', $centerIds)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

        }

        $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
            $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5])
                ->whereIn('weight_for_age', ['Normal', 'Underweight', 'Severely Underweight', 'Overweight']);
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

        return view('reports.weight-for-age-upon-entry', compact('centers', 'ageGroupsPerCenter', 'totals'));
    }
    public function weightForAgeAfter120()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        if(auth()->user()->hasRole('admin')){
            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->where('is_undernourish', true)
                    ->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $centers = ChildDevelopmentCenter::with('user')->paginate(20);
            $centers->getCollection()->keyBy('id');

        } elseif(auth()->user()->hasRole('lgu focal')){
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->paginate(20);
            $centerIds = $centers->pluck('id');

            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereIn('children.child_development_center_id', $centerIds)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();
        }
        $nutritionalStatusOccurrences = $fundedChildren->map(function ($child) {
            $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5])
                ->whereIn('weight_for_age', ['Normal', 'Underweight', 'Severely Underweight', 'Overweight']);
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

        return view('reports.weight-for-age-after-120', compact('centers', 'ageGroupsPerCenter', 'totals'));
    }
    public function weightForHeightUponEntry()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }
        
        if(auth()->user()->hasRole('admin')){
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

            return view('reports.weight-for-height-upon-entry', compact('centers', 'ageGroupsPerCenter', 'totals'));

        } elseif(auth()->user()->hasRole('lgu focal')){
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->paginate(20);
            $centerIds = $centers->pluck('id');

            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereIn('children.child_development_center_id', $centerIds)
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

            return view('reports.weight-for-height-upon-entry', compact('centers', 'ageGroupsPerCenter', 'totals'));
        }
    }
    public function weightForHeightAfter120()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }
        
        if(auth()->user()->hasRole('admin')){
            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $centers = ChildDevelopmentCenter::with('user')->paginate(15);
            $centers->getCollection()->keyBy('id');

        } elseif(auth()->user()->hasRole('lgu focal')){
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->paginate(20);
            $centerIds = $centers->pluck('id');

            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereIn('children.child_development_center_id', $centerIds)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();
        }

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

        return view('reports.weight-for-height-after-120', compact('centers', 'ageGroupsPerCenter', 'totals'));
    }
    public function heightForAgeUponEntry()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }
        if(auth()->user()->hasRole('admin')){
            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $centers = ChildDevelopmentCenter::with('user')->paginate(15);
            $centers->getCollection()->keyBy('id');

        } elseif(auth()->user()->hasRole('lgu focal')){
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->paginate(20);
            $centerIds = $centers->pluck('id');

            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereIn('children.child_development_center_id', $centerIds)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();
        }

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

        return view('reports.height-for-age-upon-entry', compact('centers', 'ageGroupsPerCenter', 'totals'));
    }
    public function heightForAgeAfter120()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        if(auth()->user()->hasRole('admin')){
            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

            $centers = ChildDevelopmentCenter::with('user')->paginate(15);
            $centers->getCollection()->keyBy('id');
        } elseif(auth()->user()->hasRole('lgu focal')){
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->paginate(20);
            $centerIds = $centers->pluck('id');

            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereIn('children.child_development_center_id', $centerIds)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();
        }
        

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

        

        return view('reports.height-for-age-after-120', compact('centers', 'ageGroupsPerCenter', 'totals'));
    }
    
    public function printMasterlist(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');

        if (!$cycleImplementation) {
            return view('reports.index', [
                'fundedChildren' => collect(),
            ]);
        }

        $fundedChildren = Child::with('nutritionalStatus', 'sex')
            ->where('is_funded', true)
            ->where('cycle_implementation_id', $cycleImplementation->id);

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->get();
            } else {
                $isFunded = $fundedChildren->where('child_development_center_id', $cdcId)->get();
            }
            
        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereIn('child_development_center_id', $centerIds)->get();
            } else {
                $isFunded = $fundedChildren->whereIn('child_development_center_id', $cdcId)->get();
            }

        } else {

            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereIn('child_development_center_id', $centerIds)->get();
            } else {
                $isFunded = $fundedChildren->where('child_development_center_id', $cdcId)->get();
            }

        }

        $pdf = PDF::loadView('reports.print.masterlist', compact('cycleImplementation', 'isFunded', 'centers', 'cdcId'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycleImplementation->cycle_name . ' Masterlist.pdf');
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
