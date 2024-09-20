<?php

namespace App\Http\Controllers;

use App\Models\ChildDevelopmentCenter;
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
        $this->middleware('permission:create-cycle-implementation', ['only' => ['create','store']]);
        $this->middleware('permission:edit-cycle-implementation', ['only' => ['edit','update']]);
    }

    public function index()
    {
        // Check if the user is an admin
        $isAdminOrFocal = auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal');
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

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
        
        if ($isAdminOrFocal) {
            
            $isFunded = $fundedChildren->paginate(10);
            $isNotFunded = $notFundedChildren->paginate(10);
            $isPwdChidlren = $childrenWithDisabilities->paginate(10);

            
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
            $isPwdChidlren = $childrenWithDisabilities->paginate(10);

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
            'isPwdChidlren',
            'countsPerCenter',
            'countsPerCenterAndGender',
            'centers'
        ));
    }


    // public function viewMasterlist()
    // {
    //     $isAdmin = auth()->user()->hasRole('admin');

    //     // Retrieve the active cycle_implementation
    //     $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

    //     if (!$cycleImplementation) {
    //         return view('reports.index', [
    //             'fundedChildren' => collect(), 
    //         ]);
    //     }

    //     // Common query: Filter funded children and the active cycle_implementation_id
    //     $childrenQuery = Child::with('nutritionalStatus')
    //         ->where('is_funded', true) // Only funded children
    //         ->where('cycle_implementation_id', $cycleImplementation->id); // Only for active cycle

    //     if ($isAdmin) {
    //         // Admin sees all funded children for the active cycle
    //         $fundedChildren = $childrenQuery->paginate(10);
    //     } else {
    //         // Non-admin users are restricted to their assigned CDC
    //         $cdc = ChildDevelopmentCenter::where('assigned_user_id', auth()->id())->first();

    //         if (!$cdc) {
    //             return view('reports.index', [
    //                 'fundedChildren' => collect(), // Return an empty collection
    //             ]);
    //         }

    //         // Restrict children to those in the user's CDC
    //         $fundedChildren = $childrenQuery
    //             ->where('child_development_center_id', $cdc->id)
    //             ->paginate(10);
    //     }

    //     // Return the view with the funded children
    //     return view('reports.index', compact('fundedChildren'));
    // }

    // public function viewChildrenWithDisabilities()
    // {
    //     $isAdmin = auth()->user()->hasRole('admin');

    //     $childrenWithDisabilities = Child::where('is_person_with_disability', true)
    //             ->with('center')
    //             ->paginate(10);

    //     if ($isAdmin) {
            
    //         $fundedChildrenwithDisabilities = $childrenWithDisabilities->paginate(10);
    //     } else {
            
    //         $cdc = ChildDevelopmentCenter::where('assigned_user_id', auth()->id())->first();

    //         if (!$cdc) {
    //             return view('reports.index', [
    //                 'fundedChildren' => collect(), // Return an empty collection
    //             ]);
    //         }

            
    //         $fundedChildrenwithDisabilities = $childrenWithDisabilities
    //             ->where('is_person_with_disability', 'true')
    //             ->paginate(10);
    //     }

    //     // Return the view with the funded children
    //     return view('reports.index', compact('fundedChildrenwithDisabilities'));
    // }



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
