<?php

namespace App\Http\Controllers;

use App\Models\ChildDevelopmentCenter;
use App\Models\Psgc;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Child;
use App\Models\ChildCenter;
use App\Models\UserCenter;
use App\Models\Implementation;
use Carbon\Carbon;
use Clegginabox\PDFMerger\PDFMerger;
use Illuminate\Support\Facades\DB;

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
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        // add filter to session
        session(['filter_cdc_id' => $request->center_name]);

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;
        $childCount = null;

        $fundedChildren = Child::with('records', 'sex')
            ->orderByRaw("CASE WHEN sex_id = 1 THEN 0 ELSE 1 END")
            ->orderBy('lastname', 'asc');

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('status', 'active')
                        ->orderBy('child_development_center_id', 'asc');
                })
                    ->paginate(10);

                $childCount = $isFunded->count();

            } else {

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($request, $cycle) {
                    $query->where('child_development_center_id', $request->center_name)
                        ->where('implementation_id', $cycle->id)
                        ->where('status', 'active')
                        ->orderBy('child_development_center_id', 'asc');
                })
                    ->paginate(10);

                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
                $childCount = $isFunded->count();
            }

        } else {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($centerIDs, $cycle) {
                    if ($cycle) {
                        $query->whereIn('child_development_center_id', $centerIDs)
                            ->where('implementation_id', $cycle->id)
                            ->where('status', 'active')
                            ->orderBy('child_development_center_id', 'asc');
                    }
                })
                    ->paginate(10);

                $childCount = $isFunded->count();
            } else {

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
                    $query->where('child_development_center_id', $cdcId)
                        ->where('implementation_id', $cycle->id)
                        ->where('status', 'active')
                        ->orderBy('child_development_center_id', 'asc');
                })
                    ->paginate(10);

                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
                $childCount = $isFunded->count();
            }

        }

        return view('reports.index', compact('isFunded', 'centers', 'cdcId', 'selectedCenter', 'cycle', 'centerNames', 'childCount'));
    }

    // public function index2($cycleId, Request $request)
    // {
    //     $cycle = Implementation::where('id', $cycleId)->first();

    //     if (!$cycle) {
    //         return back()->with('error', 'No active regular cycle found.');
    //     }

    //     $cdcId = $request->input('center_name', 'all_center');
    //     $selectedCenter = null;

    //     $fundedChildren = Child::with('records','nutritionalStatus', 'sex');

    //     if (auth()->user()->hasRole('admin')) {
    //         $centers = ChildDevelopmentCenter::all()->keyBy('id');
    //         $centerIDs = $centers->pluck('id');
    //         $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

    //         if ($cdcId == 'all_center') {
    //             $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
    //                 $query->where('implementation_id', $cycle->id)
    //                     ->where('funded', 1);
    //             })
    //                 ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
    //                     $query->where('implementation_id', $cycle->id);
    //                 })
    //                 ->paginate(5);

    //             $childCount = $isFunded->count();

    //         } else {

    //             $isFunded = $fundedChildren->whereHas('records', function ($query) use ($request, $cycle) {
    //                 $query->where('child_development_center_id', $request->center_name)
    //                     ->where('implementation_id', $cycle->id)
    //                     ->where('funded', 1);
    //             })
    //                 ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
    //                     $query->where('implementation_id', $cycle->id);
    //                 })
    //                 ->paginate(5);
    //             $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
    //             $childCount = $isFunded->count();
    //         }

    //     } else {
    //         $userID = auth()->id();
    //         $centers = UserCenter::where('user_id', $userID)->get();
    //         $centerIDs = $centers->pluck('child_development_center_id');

    //         $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

    //         if ($cdcId == 'all_center') {
    //             $isFunded = $fundedChildren->whereHas('records', function ($query) use ($centerIDs, $cycle) {
    //                 if ($cycle) {
    //                     $query->whereIn('child_development_center_id', $centerIDs)
    //                         ->where('implementation_id', $cycle->id)
    //                         ->where('funded', 1);
    //                 }
    //             })
    //                 ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
    //                     $query->where('implementation_id', $cycle->id);
    //                 })
    //                 ->paginate(5);

    //             $childCount = $isFunded->count();
    //         } else {

    //             $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
    //                 $query->where('child_development_center_id', $cdcId)
    //                     ->where('implementation_id', $cycle->id)
    //                     ->where('funded', 1);

    //             })
    //                 ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
    //                     $query->where('implementation_id', $cycle->id);
    //                 })
    //                 ->paginate(5);
    //             $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
    //             $childCount = $isFunded->count();
    //         }

    //     }

    //     return view('reports.index2', compact('isFunded', 'centers', 'cdcId', 'selectedCenter', 'cycle', 'centerNames', 'childCount'));
    // }

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
    public function show(Request $request)
    {
        session(['report_cycle_id' => $request->input('cycle_id')]);

        return redirect()->route('reports.index');
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
    public function nutritionalStatusWFA(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::find($cycleID);

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all();
        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = ChildDevelopmentCenter::whereIn('id', function ($query) {
                $query->select('child_development_center_id')
                    ->from('user_centers')
                    ->where('user_id', auth()->id());
            })->with('users.roles')->get();
            $centerIDs = $centers->pluck('id');

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();
        }

        $oldestNutritionalIds = DB::table('nutritional_statuses')
            ->select(DB::raw('MIN(id) as id'))
            ->groupBy('child_id');

        $results = DB::table('nutritional_statuses')
            ->joinSub($oldestNutritionalIds, 'oldest_nutritionals', function ($join) {
                $join->on('nutritional_statuses.id', '=', 'oldest_nutritionals.id');
            })
            ->join('children', 'children.id', '=', 'nutritional_statuses.child_id')
            ->join('child_centers', function ($join) {
                $join->on('children.id', '=', 'child_centers.child_id')
                    ->where('child_centers.status', '=', 'active')
                    ->where('child_centers.funded', '=', 1);
            })
            ->join('child_development_centers', 'child_development_centers.id', '=', 'child_centers.child_development_center_id')
            ->select([
                'child_development_centers.id as center_id',
                'child_development_centers.center_name as center_name',
                'children.sex_id',
                'nutritional_statuses.age_in_years',
                'nutritional_statuses.weight_for_age',
                DB::raw('COUNT(*) as total')
            ])
            ->groupBy(
                'child_development_centers.id',
                'child_development_centers.center_name',
                'children.sex_id',
                'nutritional_statuses.age_in_years',
                'nutritional_statuses.weight_for_age'
            )
            ->get();

        $ages = [2, 3, 4, 5];
        $sexMap = [1 => 'M', 2 => 'F'];
        $sexLabels = ['M', 'F'];
        $categories = ['Normal', 'Underweight', 'Severely Underweight', 'Overweight'];

        $wfaCounts = [];

        $overallTotals = [
            'total_children' => 0,
            'total_male' => 0,
            'total_female' => 0,
        ];

        $maleAgeTotals = [
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
        ];

        $femaleAgeTotals = [
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
        ];

        foreach ($results as $row) {
            $centerId = $row->center_id;
            $centerName = $row->center_name;

            if (!isset($wfaCounts[$centerId])) {
                $wfaCounts[$centerId] = [
                    'center_name' => $centerName,
                    'data' => [],
                    'total_children' => 0,
                    'total_male' => 0,
                    'total_female' => 0,
                ];

                foreach ($categories as $category) {
                    foreach ($sexLabels as $sex) {
                        foreach ($ages as $age) {
                            $wfaCounts[$centerId]['data'][$category][$sex][$age] = 0;
                        }
                    }
                }
            }

            $category = $row->weight_for_age;
            $sex = $sexMap[$row->sex_id] ?? null;
            $age = $row->age_in_years;

            if (isset($wfaCounts[$centerId]['data'][$category][$sex][$age])){
                $wfaCounts[$centerId]['data'][$category][$sex][$age] += $row->total;
            }

            $wfaCounts[$centerId]['total_children'] += $row->total;
            $overallTotals['total_children'] += $row->total;

            if ($sex === 'M') {
                $wfaCounts[$centerId]['total_male'] += $row->total;
                $overallTotals['total_male'] += $row->total;
            } elseif ($sex === 'F') {
                $wfaCounts[$centerId]['total_female'] += $row->total;
                $overallTotals['total_female'] += $row->total;
            }

            if ($sex === 'M' && isset($maleAgeTotals[$age])) {
                $maleAgeTotals[$age] += $row->total;
            } elseif($sex === 'F' && isset($femaleAgeTotals[$age])){
                $femaleAgeTotals[$age] += $row->total;
            }

        }

        $agetotals = [];
        foreach ($categories as $category) {
            foreach ($sexLabels as $sex) {
                foreach ($ages as $age) {
                    $agetotals[$category][$sex][$age] = 0;
                }
            }
        }

        foreach ($wfaCounts as $center) {
            foreach ($categories as $category) {
                foreach ($sexLabels as $sex) {
                    foreach ($ages as $age) {
                        $agetotals[$category][$sex][$age] += $center['data'][$category][$sex][$age] ?? 0;
                    }
                }
            }
        }

        $ageTotalsPerCategory = [];
        foreach ($categories as $category) {
            foreach ($sexLabels as $sex) {
                $ageTotalsPerCategory[$category][$sex] = 0;
                foreach ($ages as $age) {
                    $ageTotalsPerCategory[$category][$sex] += $agetotals[$category][$sex][$age] ?? 0;
                }
            }
        }

        $totalsPerCategory = [];
        foreach ($categories as $category) {
            $totalsPerCategory[$category] = 0;
            foreach ($sexLabels as $sex) {
                $totalsPerCategory[$category] += $ageTotalsPerCategory[$category][$sex] ?? 0;
            }
        }

        $pdf = PDF::loadView('reports.print.weight-for-age-upon-entry', compact('cycle', 'province', 'city', 'results', 'centers', 'wfaCounts', 'ages', 'sexLabels', 'categories', 'agetotals', 'ageTotalsPerCategory', 'totalsPerCategory', 'overallTotals', 'maleAgeTotals', 'femaleAgeTotals'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 1,
                'margin-left' => 1,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->stream();
    }
    public function nutritionalStatusHFA(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::find($cycleID);

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all();
        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = ChildDevelopmentCenter::whereIn('id', function ($query) {
                $query->select('child_development_center_id')
                    ->from('user_centers')
                    ->where('user_id', auth()->id());
            })->with('users.roles')->get();
            $centerIDs = $centers->pluck('id');

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();
        }

        $oldestNutritionalIds = DB::table('nutritional_statuses')
            ->select(DB::raw('MIN(id) as id'))
            ->groupBy('child_id');

        $results = DB::table('nutritional_statuses')
            ->joinSub($oldestNutritionalIds, 'oldest_nutritionals', function ($join) {
                $join->on('nutritional_statuses.id', '=', 'oldest_nutritionals.id');
            })
            ->join('children', 'children.id', '=', 'nutritional_statuses.child_id')
            ->join('child_centers', function ($join) {
                $join->on('children.id', '=', 'child_centers.child_id')
                    ->where('child_centers.status', '=', 'active')
                    ->where('child_centers.funded', '=', 1);
            })
            ->join('child_development_centers', 'child_development_centers.id', '=', 'child_centers.child_development_center_id')
            ->select([
                'child_development_centers.id as center_id',
                'child_development_centers.center_name as center_name',
                'children.sex_id',
                'nutritional_statuses.age_in_years',
                'nutritional_statuses.height_for_age',
                DB::raw('COUNT(*) as total')
            ])
            ->groupBy(
                'child_development_centers.id',
                'child_development_centers.center_name',
                'children.sex_id',
                'nutritional_statuses.age_in_years',
                'nutritional_statuses.height_for_age'
            )
            ->get();

        $ages = [2, 3, 4, 5];
        $sexMap = [1 => 'M', 2 => 'F'];
        $sexLabels = ['M', 'F'];
        $categories = ['Normal', 'Stunted', 'Severely Stunted', 'Tall'];

        $hfaCounts = [];

        $overallTotals = [
            'total_children' => 0,
            'total_male' => 0,
            'total_female' => 0,
        ];

        $maleAgeTotals = [
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
        ];

        $femaleAgeTotals = [
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
        ];

        foreach ($results as $row) {
            $centerId = $row->center_id;
            $centerName = $row->center_name;

            if (!isset($hfaCounts[$centerId])) {
                $hfaCounts[$centerId] = [
                    'center_name' => $centerName,
                    'data' => [],
                    'total_children' => 0,
                    'total_male' => 0,
                    'total_female' => 0,
                ];

                foreach ($categories as $category) {
                    foreach ($sexLabels as $sex) {
                        foreach ($ages as $age) {
                            $hfaCounts[$centerId]['data'][$category][$sex][$age] = 0;
                        }
                    }
                }
            }

            $category = $row->height_for_age;
            $sex = $sexMap[$row->sex_id] ?? null;
            $age = $row->age_in_years;

            if (isset($hfaCounts[$centerId]['data'][$category][$sex][$age])){
                $hfaCounts[$centerId]['data'][$category][$sex][$age] += $row->total;
            }

            $hfaCounts[$centerId]['total_children'] += $row->total;
            $overallTotals['total_children'] += $row->total;

            if ($sex === 'M') {
                $hfaCounts[$centerId]['total_male'] += $row->total;
                $overallTotals['total_male'] += $row->total;
            } elseif ($sex === 'F') {
                $hfaCounts[$centerId]['total_female'] += $row->total;
                $overallTotals['total_female'] += $row->total;
            }

            if ($sex === 'M' && isset($maleAgeTotals[$age])) {
                $maleAgeTotals[$age] += $row->total;
            } elseif($sex === 'F' && isset($femaleAgeTotals[$age])){
                $femaleAgeTotals[$age] += $row->total;
            }

        }

        $agetotals = [];
        foreach ($categories as $category) {
            foreach ($sexLabels as $sex) {
                foreach ($ages as $age) {
                    $agetotals[$category][$sex][$age] = 0;
                }
            }
        }

        foreach ($hfaCounts as $center) {
            foreach ($categories as $category) {
                foreach ($sexLabels as $sex) {
                    foreach ($ages as $age) {
                        $agetotals[$category][$sex][$age] += $center['data'][$category][$sex][$age] ?? 0;
                    }
                }
            }
        }

        $ageTotalsPerCategory = [];
        foreach ($categories as $category) {
            foreach ($sexLabels as $sex) {
                $ageTotalsPerCategory[$category][$sex] = 0;
                foreach ($ages as $age) {
                    $ageTotalsPerCategory[$category][$sex] += $agetotals[$category][$sex][$age] ?? 0;
                }
            }
        }

        $totalsPerCategory = [];
        foreach ($categories as $category) {
            $totalsPerCategory[$category] = 0;
            foreach ($sexLabels as $sex) {
                $totalsPerCategory[$category] += $ageTotalsPerCategory[$category][$sex] ?? 0;
            }
        }

        $pdf = PDF::loadView('reports.print.height-for-age-upon-entry', compact('cycle', 'province', 'city', 'results', 'centers', 'hfaCounts', 'ages', 'sexLabels', 'categories', 'agetotals', 'ageTotalsPerCategory', 'totalsPerCategory', 'overallTotals', 'maleAgeTotals', 'femaleAgeTotals'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 1,
                'margin-left' => 1,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->stream();
    }
    public function nutritionalStatusWFH(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::find($cycleID);

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all();
        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = ChildDevelopmentCenter::whereIn('id', function ($query) {
                $query->select('child_development_center_id')
                    ->from('user_centers')
                    ->where('user_id', auth()->id());
            })->with('users.roles')->get();
            $centerIDs = $centers->pluck('id');

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();
        }

        $oldestNutritionalIds = DB::table('nutritional_statuses')
            ->select(DB::raw('MIN(id) as id'))
            ->groupBy('child_id');

        $results = DB::table('nutritional_statuses')
            ->joinSub($oldestNutritionalIds, 'oldest_nutritionals', function ($join) {
                $join->on('nutritional_statuses.id', '=', 'oldest_nutritionals.id');
            })
            ->join('children', 'children.id', '=', 'nutritional_statuses.child_id')
            ->join('child_centers', function ($join) {
                $join->on('children.id', '=', 'child_centers.child_id')
                    ->where('child_centers.status', '=', 'active')
                    ->where('child_centers.funded', '=', 1);
            })
            ->join('child_development_centers', 'child_development_centers.id', '=', 'child_centers.child_development_center_id')
            ->select([
                'child_development_centers.id as center_id',
                'child_development_centers.center_name as center_name',
                'children.sex_id',
                'nutritional_statuses.age_in_years',
                'nutritional_statuses.weight_for_height',
                DB::raw('COUNT(*) as total')
            ])
            ->groupBy(
                'child_development_centers.id',
                'child_development_centers.center_name',
                'children.sex_id',
                'nutritional_statuses.age_in_years',
                'nutritional_statuses.weight_for_height'
            )
            ->get();

        $ages = [2, 3, 4, 5];
        $sexMap = [1 => 'M', 2 => 'F'];
        $sexLabels = ['M', 'F'];
        $categories = ['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese'];

        $wfhCounts = [];

        $overallTotals = [
            'total_children' => 0,
            'total_male' => 0,
            'total_female' => 0,
        ];

        $maleAgeTotals = [
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
        ];

        $femaleAgeTotals = [
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
        ];

        foreach ($results as $row) {
            $centerId = $row->center_id;
            $centerName = $row->center_name;

            if (!isset($wfhCounts[$centerId])) {
                $wfhCounts[$centerId] = [
                    'center_name' => $centerName,
                    'data' => [],
                    'total_children' => 0,
                    'total_male' => 0,
                    'total_female' => 0,
                ];

                foreach ($categories as $category) {
                    foreach ($sexLabels as $sex) {
                        foreach ($ages as $age) {
                            $wfhCounts[$centerId]['data'][$category][$sex][$age] = 0;
                        }
                    }
                }
            }

            $category = $row->weight_for_height;
            $sex = $sexMap[$row->sex_id] ?? null;
            $age = $row->age_in_years;

            if (isset($wfhCounts[$centerId]['data'][$category][$sex][$age])){
                $wfhCounts[$centerId]['data'][$category][$sex][$age] += $row->total;
            }

            $wfhCounts[$centerId]['total_children'] += $row->total;
            $overallTotals['total_children'] += $row->total;

            if ($sex === 'M') {
                $wfhCounts[$centerId]['total_male'] += $row->total;
                $overallTotals['total_male'] += $row->total;
            } elseif ($sex === 'F') {
                $wfhCounts[$centerId]['total_female'] += $row->total;
                $overallTotals['total_female'] += $row->total;
            }

            if ($sex === 'M' && isset($maleAgeTotals[$age])) {
                $maleAgeTotals[$age] += $row->total;
            } elseif($sex === 'F' && isset($femaleAgeTotals[$age])){
                $femaleAgeTotals[$age] += $row->total;
            }

        }

        $agetotals = [];
        foreach ($categories as $category) {
            foreach ($sexLabels as $sex) {
                foreach ($ages as $age) {
                    $agetotals[$category][$sex][$age] = 0;
                }
            }
        }

        foreach ($wfhCounts as $center) {
            foreach ($categories as $category) {
                foreach ($sexLabels as $sex) {
                    foreach ($ages as $age) {
                        $agetotals[$category][$sex][$age] += $center['data'][$category][$sex][$age] ?? 0;
                    }
                }
            }
        }

        $ageTotalsPerCategory = [];
        foreach ($categories as $category) {
            foreach ($sexLabels as $sex) {
                $ageTotalsPerCategory[$category][$sex] = 0;
                foreach ($ages as $age) {
                    $ageTotalsPerCategory[$category][$sex] += $agetotals[$category][$sex][$age] ?? 0;
                }
            }
        }

        $totalsPerCategory = [];
        foreach ($categories as $category) {
            $totalsPerCategory[$category] = 0;
            foreach ($sexLabels as $sex) {
                $totalsPerCategory[$category] += $ageTotalsPerCategory[$category][$sex] ?? 0;
            }
        }

        $pdf = PDF::loadView('reports.print.weight-for-height-upon-entry', compact('cycle', 'province', 'city', 'results', 'centers', 'wfhCounts', 'ages', 'sexLabels', 'categories', 'agetotals', 'ageTotalsPerCategory', 'totalsPerCategory', 'overallTotals', 'maleAgeTotals', 'femaleAgeTotals'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 1,
                'margin-left' => 1,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->stream();
    }
}
