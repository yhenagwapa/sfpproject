<?php

namespace App\Http\Controllers;

use App\Models\ChildDevelopmentCenter;
use App\Models\ChildRecord;
use App\Models\Psgc;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Child;
use App\Models\ChildCenter;
use App\Models\UserCenter;
use App\Models\Implementation;
use Carbon\Carbon;
use Clegginabox\PDFMerger\PDFMerger;
use Illuminate\Support\Facades\DB;
use Str;
use Storage;

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
        $cdcId = session('center_name');

        $cycle = Implementation::find($cycleID);
        $cycleStatus = $cycle->status;
        $childStatus = [];

        // add filter to session
        // session(['filter_cdc_id' => $request->input('center_name')]);

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $selectedCenter = null;
        $childCount = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex')
            ->orderByRaw("CASE WHEN sex_id = 1 THEN 0 ELSE 1 END")
            ->orderByRaw("LOWER(lastname) ASC");

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                        ->where('action_type', 'active')
                        ->where('funded', 1);
                    })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->orderBy('lastname', 'asc')
                    ->get();

                $childCount = $isFunded->count();

            } else {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('action_type', 'active')
                        ->where('funded', 1)
                        ->where('child_development_center_id', $cdcId);
                    })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->orderBy('lastname', 'asc')
                    ->get();

                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
                $childCount = $isFunded->count();
            }

        } else {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            // dd($cdcId);

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($centerIDs, $cycle) {
                    if ($cycle) {
                        $query->whereIn('child_development_center_id', $centerIDs)
                            ->where('implementation_id', $cycle->id)
                            ->where('action_type', 'active')
                            ->where('funded', 1);
                    }
                    })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->orderBy('lastname', 'asc')
                    ->get();

                $childCount = $isFunded->count();
            } else {

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
                    $query->where('child_development_center_id', $cdcId)
                        ->where('implementation_id', $cycle->id)
                        ->where('action_type', 'active')
                        ->where('funded', 1);
                    })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->orderBy('lastname', 'asc')
                    ->get();

                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
                $childCount = $isFunded->count();
            }

        }

        return view('reports.index', compact('isFunded', 'centers', 'cdcId', 'selectedCenter', 'cycle', 'centerNames', 'childCount'));
    }
    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        session(['report_cycle_id' => $request->input(key: 'cycle_id')]);
        session(['center_name' => $request->input('center_name')]);

        return redirect()->route('reports.index');
    }
    public function exportReport(Request $request)
    {
        ini_set('memory_limit', '512M');

        $cycleID = session('report_cycle_id');
        $cycle = Implementation::find($cycleID);

        $fundedChildren = Child::with([
            'records' => function ($query) use ($cycle) {
                $query->where('implementation_id', $cycle->id)
                    ->where('action_type', 'active');
            },
            'sex',
            'records.center',
            'psgc',
            'nutritionalStatus'
        ])->orderByRaw("CASE WHEN sex_id = 1 THEN 0 ELSE 1 END")
            ->get();

        $userID = auth()->id();

        if (auth()->user()->hasRole('admin')) {
            $centers = UserCenter::all();
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::all()->keyBy('id');

            if (!$cycle) {
                $children = null;
                return view('reports.index', compact('children', 'centerNames', 'cdcId'))->with('error', 'No active implementation.');
            }

            $children = $fundedChildren->filter(function ($child) use ($centerIDs, $cycle) {
                return $child->records->contains(function ($record) use ($centerIDs, $cycle) {
                    return $centerIDs->contains($record->child_development_center_id)
                        && $record->implementation_id === $cycle->id
                        && $record->status === 'active';
                });
            });


            $filename = 'Region XI Report.csv';

        } else {
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $users = User::with('psgc')->find($userID);

            if (!$cycle) {
                $children = null;
                return view('child.index', compact('children', 'centerNames', 'cdcId'))->with('error', 'No active implementation.');
            }

            $children = $fundedChildren->filter(function ($child) use ($centerIDs, $cycle) {
                return $child->records->contains(function ($record) use ($centerIDs, $cycle) {
                    return $centerIDs->contains($record->child_development_center_id)
                        && $record->implementation_id === $cycle->id
                        && $record->status === 'active';
                });
            });

            $filename = $users->psgc->city_name . ' Report.csv';

        }

        $groupedChildren = $children->filter(function ($child) {
            return $child->records->first()?->center !== null;
        })->groupBy(function ($child) {
            return optional($child->records->first()->center)->id;
        });

        $groupedChildren = $groupedChildren->map(function ($childrenGroup) {
            return $childrenGroup
                ->sortBy(function ($child) {
                    return [$child->sex_id, Str::lower($child->lastname)];
                });
        });


        $filepath = storage_path("app/public/{$filename}");

        $handle = fopen($filepath, 'w');

        // Write header row
        fputcsv($handle, [
            'A.1 PROVINCE',
            'A.4 DISTICT',
            'A.2 CITY/MUNICIPALITY',
            'A.3 BARANGAY',
            'A.4 PPAN PRIORITY AREA',
            'A.5 IMPLEMENTATION SCHEME',
            'A.6 MODES OF PROCUREMENT',
            'A.7 NAME OF FACILITY',
            'A.9 DATE OF REGISTRATION',
            'A.8 FACILITY CATEGORY',
            'A.9 LAST NAME OF CHILD DEVELOPMENT WORKER',
            'A.9.1 FIRST NAME OF CHILD DEVELOPMENT WORKER',
            'A.9.2 MIDDLE NAME OF CHILD DEVELOPMENT WORKER',
            'A.9.3 EXT NAME OF CHILD DEVELOPMENT WORKER',
            'A.10 WITH WASH FACILITY (According to  ECCDC Standard)(1-YES, 0-NO)',
            'A.11 WITH COMMUNITY GARDEN',
            'B.1 BARANGAY OF CHILD',
            'B.2 LAST NAME OF CHILD',
            'B2.1 FIRST NAME OF CHILD',
            'B.2.2 MIDDLE NAME OF CHILD',
            'B.2.3 EXT NAME OF CHILD',
            'FOR CHECKING OF DUPLICATES',
            'B.3 SEX OF THE CHILD',
            'B.4 WITH DISABILITY OF THE CHILD (1-YES, 0-NO)',
            'B.5 TYPE OF DISABILITY OF THE CHILD',
            'B.6 CHILD OF SOLO PARENT (1-YES, 0-NO)',
            'B.7 TYPE OF BENEFICIARY',
            'B.8 DATE OF BIRTH_CHILD (MM/DD/YY)',
            'B.9 AGE_CHILD',
            'B.13 DATE OF WEIGHING BEFORE FEEDING_BASELINE (MM/DD/YY)',
            'B.14 AGE IN MONTHS BEFORE FEEDING_BASELINE',
            'B.15 HEIGHT BEFORE FEEDING_BASELINE',
            'B.16 WEIGHT BEFORE FEEDING_BASELINE',
            'B.17 NS WEIGHT FOR HEIGHT_BASELINE',
            'B.18 NS HEIGHT FOR AGE_BASELINE',
            'B.19 NS WEIGHT FOR AGE_BASELINE',
            'B.13 DATE OF WEIGHING BEFORE FEEDING_ENDLINE (MM/DD/YY)',
            'B.20 AGE IN MONTHS BEFORE FEEDING_ENDLINE',
            'B.21 HEIGHT BEFORE FEEDING_ENDLINE',
            'B.22 WEIGHT BEFORE FEEDING_ENDLINE',
            'B.23 NS WEIGHT FOR HEIGHT_ENDLINE',
            'B.24 NS HEIGHT FOR AGE_ENDLINE',
            'B.25 NS WEIGHT FOR AGE_ENDLINE',
            'B.26 DEWORMED (1-YES, 0-NO)',
            'B.27 PROVIDED WITH VIATMIN A SUPPLEMENTATION (1-YES, 0-NO)',
            'B.27 FOOD INTOLERANCE/FOOD ALLERGY (if any)',
            'B.29 OTHER MEDICAL CONDITIONS (if any)',
            'REFERRED FOR OTHER SOCIAL SERVICES (1-YES, 0-NO)',
            'C.1 LAST NAME OF PARENT/GUARDIAN',
            'C.2 FIRST NAME OF PARENT/GUARDIAN',
            'C.3 MIDDLE NAME OF PARENT/GUARDIAN',
            'C.4 EXT NAME OF PARENT/GUARDIAN',
            'C.5 SEX_PARENT/GUARDIAN',
            'C.6 PARENT/GUARDIAN PSN/PHILSYS NUMBER',
            'C.7 SOURCE OF INCOME (Salary, wages, MSME, etc)',
            'C.5 INDIGENOUS PEOPLE AFFILIATION (e.i. Igorot, Lumad, Mangyan, Ieta',
            'C.9 PANTAWID MEMBER (specify RCCT / 4ps or MCCT and indicate reference number)',
            'C.10 WITH DISABILITY_PARENT/GUARDIAN (1-YES , 0-NO)',
            'C.11 PREVIOUSLY ATTENDED PARENT EFFICTIVENESS SESSION (1-YES, 0-NO)',
            'C.12 MODULES OF PES COMPLETED',
            'D.1 START OF FEEDING_FORTIFIED MEALS (MM/DD/YY)',
            'D.2 FREQUENCY  OF FEEDING_FORTIFIED MEALS',
            'D.3 NUMBER OF FEEDING DAYS_FORTIFIED MEALS',
            'D.4 STATUS_FORTIFIED MEALS',
            'D.5 END OF FEEDING_FORTIFIED MEALS (MM/DD/YY)',
            'D.6 WITH  MILK (1-YES, 0-NO)',
            'D.7 START OF FEEDING_MILK (MM/DD/YY)',
            'D.8 FREQUENCY  OF FEEDING_MILK',
            'D.9 NUMBER OF FEEDING DAYS_MILK',
            'D.11 STATUS_MILK',
            'D.10 END OF FEEDING_MILK (MM/DD/YY)',
            'PES MANUAL',
            'STATUS'
        ]);
        // Write data rows
        foreach ($groupedChildren as $centerId => $childrenGroup) {
            // $centerName = optional($childrenGroup->records->first()->center)->center_name ?? 'N/A';
            // $centerId = $child->records->first()->center->id;

            foreach ($childrenGroup as $child) {
                $centerName = optional($child->records->first()->center)->center_name ?? 'N/A';
                $centerId = $child->records->first()->center->id;

                $centerWorker = ChildDevelopmentCenter::with([
                    'users' => function ($query) {
                        $query->role('child development worker');
                    }
                ])
                    ->findOrFail($centerId);

                $worker = $centerWorker->users;
                $province = $centerWorker->psgc->province_name;
                $district = $centerWorker->psgc->district;
                $city = $centerWorker->psgc->city_name;
                $brgy = $centerWorker->psgc->brgy_name;

                $age_in_months = optional($child->nutritionalStatus->first())->age_in_months ?? 0;

                if ($age_in_months) {
                    $childAge = floor($age_in_months / 12);
                } else {
                    $childAge = 0;
                }

                fputcsv($handle, [
                    $province,
                    $district,
                    $city,
                    $brgy,
                    '', //ppan are
                    '', // implementation scheme
                    '', // pr mode
                    $centerName,
                    '', // registration date
                    '', // facility category
                    $worker->first()?->lastname ?? '',
                    $worker->first()?->firstname ?? '',
                    $worker->first()?->middlename ?? '',
                    $worker->first()?->extension_name ?? '',
                    '', //with wash facility
                    '', // with community garden
                    $child->psgc->brgy_name,
                    $child->lastname,
                    $child->firstname,
                    $child->middlename,
                    $child->extension_name,
                    '', // duplication checking
                    $child->sex->name,
                    $child->person_with_disability_details ? '1' : '0',
                    $child->person_with_disability_details,
                    $child->is_child_of_soloparent ? '1' : '0',
                    '', // type of bene
                    $child->date_of_birth->format('m-d-Y'),
                    $childAge,
                    $child->nutritionalStatus->first()?->actual_weighing_date,
                    $child->nutritionalStatus->first()?->age_in_months,
                    $child->nutritionalStatus->first()?->height,
                    $child->nutritionalStatus->first()?->weight,
                    $child->nutritionalStatus->first()?->weight_for_height,
                    $child->nutritionalStatus->first()?->height_for_age,
                    $child->nutritionalStatus->first()?->weight_for_age,
                    $child->nutritionalStatus->get(1)?->actual_weighing_date,
                    $child->nutritionalStatus->get(1)?->age_in_months,
                    $child->nutritionalStatus->get(1)?->height,
                    $child->nutritionalStatus->get(1)?->weight,
                    $child->nutritionalStatus->get(1)?->weight_for_height,
                    $child->nutritionalStatus->get(1)?->height_for_age,
                    $child->nutritionalStatus->get(1)?->weight_for_age,
                    $child->nutritionalStatus->first()?->deworming_date ? '1' : '0',
                    $child->nutritionalStatus->first()?->vitamin_a_date ? '1' : '0',
                    '', // food allergies
                    '', // other medical conditions
                    '', // referred to other social services
                    '', // parent lastname
                    '', // parent firstname
                    '', // parent middlename
                    '', // parent extname
                    '', // sex
                    '', // parent philsys no
                    '', // source of income
                    '', // ip affiliation
                    '', // pantawid
                    '', // disability
                    '', // prev attended pes
                    '', // pes modules completed
                    '', // start of feeding meals
                    '', // frequency
                    '', // no of feeding
                    '', // status
                    '', // end of feeding meals
                    '', // with milk
                    '', // start of milk feeding
                    '', // frequency
                    '', // no of milk feeding
                    '', // status
                    '', // end of milk feeding
                    '', // pes manual
                    '', // status
                ]);



            }
        }

        fclose($handle);


        return response()->download($filepath)->deleteFileAfterSend(true);
    }
    public function showNutritionalStatus(Request $request, $reportType, $nsType)
    {
        session(['report_cycle_id' => $request->input(key: 'ns_cycle_id')]);

        $nsType = $request->input('ns_type');
        $reportType = $request->input('report_type');

        return redirect()->route('reports.print', ['reportType' => $reportType,'nsType' => $nsType]);
    }
    public function nutritionalStatus($reportType, $nsType)
    {
        $cycleID = session('report_cycle_id');

        $cycle = Implementation::find($cycleID);
        $cycleStatus = $cycle->status;

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $province = null;
        $city = null;
        $adminDistrict = null;
        $status = null;

        if ($cycleStatus == 'closed') {
            $status = 'inactive';
        } elseif ($cycleStatus == 'active') {
            $status = 'active';
        } else {
            $status = null;
        }

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all();
        } elseif (auth()->user()->hasRole('lgu focal') || auth()->user()->hasRole('sfp coordinator')) {
            $userID = auth()->id();
            $centers = ChildDevelopmentCenter::whereIn('id', function ($query) use ($userID) {
                $query->select('child_development_center_id')
                    ->from('user_centers')
                    ->where('user_id', $userID);
            })->with('users.roles')->get();
            $centerIDs = $centers->pluck('id');

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $adminDistrict = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('subdistrict')
                ->unique();
        }

        if($nsType == 'upon-entry'){
            $nutritionalIds = DB::table('nutritional_statuses')
            ->select(DB::raw('MIN(id) as id'))
            ->groupBy('child_id');
        } else{
            $nutritionalIds = DB::table('nutritional_statuses')
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('child_id');
        }

        $results = DB::table('nutritional_statuses')
            ->joinSub($nutritionalIds, 'nutritionalstatus', function ($join) {
                $join->on('nutritional_statuses.id', '=', 'nutritionalstatus.id');
            })
            ->join('children', 'children.id', '=', 'nutritional_statuses.child_id')
            ->join('child_records', function ($join) use ($cycle, $status) {
                $join->on('children.id', '=', 'child_records.child_id')
                    ->where('child_records.implementation_id', '=', $cycle->id)
                    ->where('child_records.action_type', '=', $status)
                    ->where('child_records.funded', '=', 1);
            })
            ->join('child_development_centers', 'child_development_centers.id', '=', 'child_records.child_development_center_id')
            ->select([
                'child_development_centers.id as center_id',
                'child_development_centers.center_name as center_name',
                'children.sex_id',
                'nutritional_statuses.age_in_years',
                'nutritional_statuses.weight_for_age',
                'nutritional_statuses.height_for_age',
                'nutritional_statuses.weight_for_height',
                DB::raw('COUNT(*) as total')
            ])
            ->where('nutritional_statuses.implementation_id', $cycle->id)
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

            $cat = $row->weight_for_age;
            $sex = $sexMap[$row->sex_id] ?? null;
            $age = $row->age_in_years;

            if (isset($wfaCounts[$centerId]['data'][$cat][$sex][$age])) {
                $wfaCounts[$centerId]['data'][$cat][$sex][$age] += $row->total;

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

            $agetotals = [];
            foreach ($categories as $category) {
                foreach ($sexLabels as $sex) {
                    foreach ($ages as $age) {
                        $agetotals[$category][$sex][$age] = 0;
                    }
                }
            }

            foreach ($wfaCounts as $centerId => $center) {
                if (
                    auth()->user()->hasRole('admin') ||
                    (isset($centerIDs) && $centerIDs->contains($centerId))
                ) {
                    foreach ($categories as $category) {
                        foreach ($sexLabels as $sex) {
                            foreach ($ages as $age) {
                                $agetotals[$category][$sex][$age] += $center['data'][$category][$sex][$age] ?? 0;
                            }
                        }
                    }
                }
            }

            $sexAgeTotals = [
                'M' => [2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'F' => [2 => 0, 3 => 0, 4 => 0, 5 => 0],
            ];

            foreach ($wfaCounts as $centerId => $center) {
                if (
                    auth()->user()->hasRole('admin') ||
                    (isset($centerIDs) && $centerIDs->contains($centerId))
                ) {
                    foreach ($sexLabels as $sex) {
                        foreach ($ages as $age) {
                            foreach ($categories as $category) {
                                $sexAgeTotals[$sex][$age] += $center['data'][$category][$sex][$age] ?? 0;
                            }
                        }
                    }
                }
            }

            $totalPerSex = [
                'M' => array_sum($sexAgeTotals['M']),
                'F' => array_sum($sexAgeTotals['F']),
            ];

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

        }



        $pdf = PDF::loadView('reports.print.weight-for-age-upon-entry', compact('cycle', 'province', 'city', 'adminDistrict', 'results', 'centers', 'wfaCounts', 'ages', 'sexLabels', 'cat', 'categories', 'agetotals', 'ageTotalsPerCategory', 'totalsPerCategory', 'overallTotals', 'sexAgeTotals', 'totalPerSex'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 50,
                'margin-left' => 1,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->stream();
    }
    public function nutritionalStatusHFA(Request $request)
    {
        ini_set('memory_limit', '512M');

        $cycleID = session('report_cycle_id');
        $cycle = Implementation::find($cycleID);
        $cycleStatus = $cycle->status;

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $province = null;
        $city = null;
        $adminDistrict = null;
        $status = null;

        if ($cycleStatus == 'closed') {
            $status = 'inactive';
        } elseif ($cycleStatus == 'active') {
            $status = 'active';
        } else {
            $status = null;
        }

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all();
        } elseif (auth()->user()->hasRole('lgu focal') || auth()->user()->hasRole('sfp coordinator')) {
            $userID = auth()->id();
            $centers = ChildDevelopmentCenter::whereIn('id', function ($query) use ($userID) {
                $query->select('child_development_center_id')
                    ->from('user_centers')
                    ->where('user_id', $userID);
            })->with('users.roles')->get();
            $centerIDs = $centers->pluck('id');

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $adminDistrict = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('subdistrict')
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
            ->join('child_records', function ($join) use ($cycle, $status) {
                $join->on('children.id', '=', 'child_records.child_id')
                    ->where('child_records.implementation_id', '=', $cycle->id)
                    ->where('child_records.action_type', '=', $status)
                    ->where('child_records.funded', '=', 1);
            })
            ->join('child_development_centers', 'child_development_centers.id', '=', 'child_records.child_development_center_id')
            ->select([
                'child_development_centers.id as center_id',
                'child_development_centers.center_name as center_name',
                'children.sex_id',
                'nutritional_statuses.age_in_years',
                'nutritional_statuses.height_for_age',
                DB::raw('COUNT(*) as total')
            ])
            ->where('nutritional_statuses.implementation_id', $cycle->id)
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



        if($reportType == 'weight-for-age'){
            $categories = ['Normal', 'Underweight', 'Severely Underweight', 'Overweight'];
        } elseif($reportType == 'height-for-age'){
            $categories = ['Normal', 'Stunted', 'Severely Stunted', 'Tall'];
        } elseif ($reportType == 'weight-for-height'){
            $categories = ['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese'];
        }

        $nsCounts = [];

        $overallTotals = [
            'total_children' => 0,
            'total_male' => 0,
            'total_female' => 0,
        ];

        foreach ($results as $row) {
            $centerId = $row->center_id;
            $centerName = $row->center_name;

            if (!isset($nsCounts[$centerId])) {
                $nsCounts[$centerId] = [
                    'center_name' => $centerName,
                    'data' => [],
                    'total_children' => 0,
                    'total_male' => 0,
                    'total_female' => 0,
                ];

                foreach ($categories as $category) {
                    foreach ($sexLabels as $sex) {
                        foreach ($ages as $age) {
                            $nsCounts[$centerId]['data'][$category][$sex][$age] = 0;
                        }
                    }
                }
            }

            $cat = $row->height_for_age;
            $sex = $sexMap[$row->sex_id] ?? null;
            $age = $row->age_in_years;

            if (isset($hfaCounts[$centerId]['data'][$cat][$sex][$age])) {
                $hfaCounts[$centerId]['data'][$cat][$sex][$age] += $row->total;
            }

            $nsCounts[$centerId]['total_children'] += $row->total;
            $overallTotals['total_children'] += $row->total;

            if ($sex === 'M') {
                $nsCounts[$centerId]['total_male'] += $row->total;
                $overallTotals['total_male'] += $row->total;
            } elseif ($sex === 'F') {
                $nsCounts[$centerId]['total_female'] += $row->total;
                $overallTotals['total_female'] += $row->total;
            }

            if ($sex === 'M' && isset($maleAgeTotals[$age])) {
                $maleAgeTotals[$age] += $row->total;
            } elseif ($sex === 'F' && isset($femaleAgeTotals[$age])) {
                $femaleAgeTotals[$age] += $row->total;
            }

            $agetotals = [];
            foreach ($categories as $category) {
                foreach ($sexLabels as $sex) {
                    foreach ($ages as $age) {
                        $agetotals[$category][$sex][$age] = 0;
                    }
                }
            }

            foreach ($hfaCounts as $centerId => $center) {
                if (
                    auth()->user()->hasRole('admin') ||
                    (isset($centerIDs) && $centerIDs->contains($centerId))
                ) {
                    foreach ($categories as $category) {
                        foreach ($sexLabels as $sex) {
                            foreach ($ages as $age) {
                                $agetotals[$category][$sex][$age] += $center['data'][$category][$sex][$age] ?? 0;
                            }
                        }
                    }
                }
            }

            $sexAgeTotals = [
                'M' => [2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'F' => [2 => 0, 3 => 0, 4 => 0, 5 => 0],
            ];

            foreach ($hfaCounts as $centerId => $center) {
                if (
                    auth()->user()->hasRole('admin') ||
                    (isset($centerIDs) && $centerIDs->contains($centerId))
                ) {
                    foreach ($sexLabels as $sex) {
                        foreach ($ages as $age) {
                            foreach ($categories as $category) {
                                $sexAgeTotals[$sex][$age] += $center['data'][$category][$sex][$age] ?? 0;
                            }
                        }
                    }
                }
            }

            $totalPerSex = [
                'M' => array_sum($sexAgeTotals['M']),
                'F' => array_sum($sexAgeTotals['F']),
            ];

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

        }

        $pdf = PDF::loadView('reports.print.height-for-age-upon-entry', compact('cycle', 'province', 'city', 'adminDistrict', 'results', 'centers', 'hfaCounts', 'ages', 'sexLabels', 'cat', 'categories', 'agetotals', 'ageTotalsPerCategory', 'totalsPerCategory', 'overallTotals', 'sexAgeTotals', 'totalPerSex'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 50,
                'margin-left' => 1,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->stream();
    }
    public function nutritionalStatusWFH(Request $request)
    {
        ini_set('memory_limit', '512M');

        $cycleID = session('report_cycle_id');
        $cycle = Implementation::find($cycleID);
        $cycleStatus = $cycle->status;

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $province = null;
        $city = null;
        $adminDistrict = null;
        $status = null;

        if ($cycleStatus == 'closed') {
            $status = 'inactive';
        } elseif ($cycleStatus == 'active') {
            $status = 'active';
        } else {
            $status = null;
        }

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all();
        } elseif (auth()->user()->hasRole('lgu focal') || auth()->user()->hasRole('sfp coordinator')) {
            $userID = auth()->id();
            $centers = ChildDevelopmentCenter::whereIn('id', function ($query) use ($userID) {
                $query->select('child_development_center_id')
                    ->from('user_centers')
                    ->where('user_id', $userID);
            })->with('users.roles')->get();
            $centerIDs = $centers->pluck('id');

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $adminDistrict = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('subdistrict')
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
            ->join('child_records', function ($join) use ($cycle, $status) {
                $join->on('children.id', '=', 'child_records.child_id')
                    ->where('child_records.implementation_id', '=', $cycle->id)
                    ->where('child_records.action_type', '=', $status)
                    ->where('child_records.funded', '=', 1);
            })
            ->join('child_development_centers', 'child_development_centers.id', '=', 'child_records.child_development_center_id')
            ->select([
                'child_development_centers.id as center_id',
                'child_development_centers.center_name as center_name',
                'children.sex_id',
                'nutritional_statuses.age_in_years',
                'nutritional_statuses.weight_for_height',
                DB::raw('COUNT(*) as total')
            ])
            ->where('nutritional_statuses.implementation_id', $cycle->id)
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

            $cat = $row->weight_for_height;
            $sex = $sexMap[$row->sex_id] ?? null;
            $age = $row->age_in_years;

            if (isset($wfhCounts[$centerId]['data'][$cat][$sex][$age])) {
                $wfhCounts[$centerId]['data'][$cat][$sex][$age] += $row->total;
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
            } elseif ($sex === 'F' && isset($femaleAgeTotals[$age])) {
                $femaleAgeTotals[$age] += $row->total;
            }

            $agetotals = [];
            foreach ($categories as $category) {
                foreach ($sexLabels as $sex) {
                    foreach ($ages as $age) {
                        $agetotals[$category][$sex][$age] = 0;
                    }
                }
            }

            foreach ($wfhCounts as $centerId => $center) {
                if (
                    auth()->user()->hasRole('admin') ||
                    (isset($centerIDs) && $centerIDs->contains($centerId))
                ) {
                    foreach ($categories as $category) {
                        foreach ($sexLabels as $sex) {
                            foreach ($ages as $age) {
                                $agetotals[$category][$sex][$age] += $center['data'][$category][$sex][$age] ?? 0;
                            }
                        }
                    }
                }
            }

            $sexAgeTotals = [
                'M' => [2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'F' => [2 => 0, 3 => 0, 4 => 0, 5 => 0],
            ];

            foreach ($wfhCounts as $centerId => $center) {
                if (
                    auth()->user()->hasRole('admin') ||
                    (isset($centerIDs) && $centerIDs->contains($centerId))
                ) {
                    foreach ($sexLabels as $sex) {
                        foreach ($ages as $age) {
                            foreach ($categories as $category) {
                                $sexAgeTotals[$sex][$age] += $center['data'][$category][$sex][$age] ?? 0;
                            }
                        }
                    }
                }
            }

            $totalPerSex = [
                'M' => array_sum($sexAgeTotals['M']),
                'F' => array_sum($sexAgeTotals['F']),
            ];

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

        }

        $pdf = PDF::loadView('reports.print.weight-for-height-upon-entry', compact('cycle', 'province', 'city', 'adminDistrict', 'results', 'centers', 'wfhCounts', 'ages', 'sexLabels', 'cat', 'categories', 'agetotals', 'ageTotalsPerCategory', 'totalsPerCategory', 'overallTotals', 'sexAgeTotals', 'totalPerSex'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 50,
                'margin-left' => 1,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true,
            ]);

        return $pdf->stream();
    }
}
