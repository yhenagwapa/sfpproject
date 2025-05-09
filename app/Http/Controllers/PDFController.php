<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PDF\FocalReports;
use App\Http\Controllers\PDF\NutritionalStatusReport;
use App\Http\Controllers\PDF\WorkerReports;
use App\Models\ChildCenter;
use Illuminate\Http\Request;
use App\Models\ChildDevelopmentCenter;
use App\Models\Psgc;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Child;
use App\Models\UserCenter;
use App\Models\User;
use App\Models\Implementation;

class PDFController extends Controller
{
    use WorkerReports, NutritionalStatusReport, FocalReports;

    public function printMasterlist(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                    if ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('funded', 1)
                            ->where('status', 'active');
                    }
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->get();

            } else {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
                    if ($cycle) {
                        $query->where('child_development_center_id', $cdcId)
                            ->where('implementation_id', $cycle->id)
                            ->where('funded', 1)
                            ->where('status', 'active');
                    }
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->get();
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
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
                            ->where('funded', 1)
                            ->where('status', 'active');
                    }
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->get();
            } else {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
                    if ($cycle) {
                        $query->where('child_development_center_id', $cdcId)
                            ->where('implementation_id', $cycle->id)
                            ->where('funded', 1)
                            ->where('status', 'active');
                    }
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->get();
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
            }

        }

        // dd($isFunded);

        $pdf = Pdf::loadView('reports.print.masterlist', compact('isFunded', 'centers', 'cdcId', 'selectedCenter', 'cycle', 'centerNames'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycle->name . ' Masterlist.pdf');
    }

    public function printMalnourish(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        $childrenWithNutritionalStatus = [];
        $province = null;
        $city = null;

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

            $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                $query->where('implementation_id', $cycle->id)
                    ->where('funded', 1)
                    ->where('status', 'active')
                    ->orderBy('child_development_center_id');
            })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('is_malnourish', 1);
                })
                ->get();

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $focalCenters = ChildDevelopmentCenter::whereIn('id', $centerIDs);

            $getPsgc = $focalCenters->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->where('funded', 1)
                    ->where('status', 'active')
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->orderBy('child_development_center_id');
            })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('is_malnourish', 1);
                })
                ->get();
        }

        $pdf = Pdf::loadView('reports.print.malnourished', compact('cycle', 'isFunded', 'centers', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycle->cycle_name . ' Malnourished.pdf');
    }
    public function printDisabilities(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id2)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $childrenWithDisabilities = Child::with('records', 'nutritionalStatus', 'sex')
            ->where('person_with_disability_details', '!=', null);

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            $isPwdChildren = $childrenWithDisabilities->whereHas('records', function ($query) use ($cycle) {
                $query->where('implementation_id', $cycle->id)
                    ->where('funded', 1)
                    ->where('status', 'active')
                    ->orderBy('child_development_center_id');
            })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id);
                    ;
                })
                ->get();

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $focalCenters = ChildDevelopmentCenter::whereIn('id', $centerIDs);

            $isPwdChildren = $childrenWithDisabilities->whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->where('funded', 1)
                    ->where('status', 'active')
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->orderBy('child_development_center_id');
            })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id);
                    ;
                })
                ->get();
        }

        $pdf = Pdf::loadView('reports.print.disabilities', compact('cycle', 'isPwdChildren', 'centers', 'province', 'city'))
            ->setPaper('folio')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycle->name . ' Persons with Disability.pdf');
    }
    public function printUndernourishedUponEntry(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id2)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::with([
                'users' => function ($query) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('name', 'child development worker');
                    });
                }
            ])->get()->keyBy('id');

            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
                })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5]);
                })
                ->with('records')
                ->get();

            $centers = ChildDevelopmentCenter::paginate(15);
            $centers->getCollection()->keyBy('id');

        } else if (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5]);
                })
                ->with([
                    'records' => function ($query) {
                        $query->select('child_id', 'child_development_center_id', 'status');
                    }
                ])
                ->get();
        }

        $nutritionalStatusOccurrences = $fundedChildren->map(callback: function (Child $child): array {
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

        $ageGroupsPerCenter = $fundedChildren->groupBy(function ($child) {
            $activeRecord = $child->records->firstWhere('status', 'active');
            return $activeRecord->child_development_center_id;
        })->mapWithKeys(function ($childrenByCenter, $centerID) use ($nutritionalStatusOccurrences) {
            return [
                $centerID => [
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
                            return $firstStatus && $firstStatus->deworming_date != null && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                            return $firstStatus && $firstStatus->deworming_date != null && $child->sex_id == 2;
                        })->count(),
                    ],
                    'vitamin_a' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                            return $firstStatus && $firstStatus->vitamin_a_date != null && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                            return $firstStatus && $firstStatus->vitamin_a_date != null && $child->sex_id == 2;
                        })->count(),
                    ],

                ]
            ];
        });

        $pdf = PDF::loadView('reports.print.undernourished-upon-entry', compact('cycle', 'centerNames', 'ageGroupsPerCenter', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycle->name . ' Undernourished Upon Entry.pdf');
    }
    public function printUndernourishedAfter120(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id2)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::with([
                'users' => function ($query) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('name', 'child development worker');
                    });
                }
            ])->get()->keyBy('id');

            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5]);
                })
                ->with('records')
                ->get();

            $centers = ChildDevelopmentCenter::paginate(15);
            $centers->getCollection()->keyBy('id');

        } elseif (auth()->user()->hasRole('lgu focal')) {

            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');



            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5]);
                })
                ->with([
                    'records' => function ($query) {
                        $query->select('child_id', 'child_development_center_id', 'status');
                    }
                ])
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

        $exitAgeGroupsPerCenter = $fundedChildren->groupBy(function ($child) {
            $activeRecord = $child->records->firstWhere('status', 'active');
            return $activeRecord->child_development_center_id;
        })->mapWithKeys(function ($childrenByCenter, $centerID) use ($nutritionalStatusOccurrences) {
            return [
                $centerID => [
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
                ]
            ];
        });

        $pdf = PDF::loadView('reports.print.undernourished-after-120', compact('cycle', 'centers', 'exitAgeGroupsPerCenter', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycle->name . ' Undernourished After 120 Feedings.pdf');
    }
    public function printWeightForAgeUponEntry(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id2)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::with([
                'users' => function ($query) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('name', 'child development worker');
                    });
                }
            ])->get()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5])
                        ->whereIn('weight_for_age', ['Normal', 'Underweight', 'Severely Underweight', 'Overweight']);
                })
                ->with('records')
                ->get();

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5])
                        ->whereIn('weight_for_age', ['Normal', 'Underweight', 'Severely Underweight', 'Overweight']);
                })
                ->with([
                    'records' => function ($query) {
                        $query->select('child_id', 'child_development_center_id', 'status');
                    }
                ])
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

        $ageGroupsPerCenter = $fundedChildren->groupBy(function ($child) {
            $activeRecord = $child->records->firstWhere('status', 'active');
            return $activeRecord->child_development_center_id;
        })->mapWithKeys(function ($childrenByCenter, $centerID) use ($nutritionalStatusOccurrences) {
            return [
                $centerID => [
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
                ]
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

        $pdf = PDF::loadView('reports.print.weight-for-age-upon-entry', compact('cycle', 'centers', 'ageGroupsPerCenter', 'totals', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 0.5,
                'margin-bottom' => 0.5,
                'margin-left' => 0.5
            ]);


        return $pdf->stream($cycle->name . ' Weight for Age Upon Entry.pdf');

    }
    public function printWeightForAgeAfter120(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id2)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::with([
                'users' => function ($query) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('name', 'child development worker');
                    });
                }
            ])->get()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5])
                        ->whereIn('weight_for_age', ['Normal', 'Underweight', 'Severely Underweight', 'Overweight']);
                })
                ->with('records')
                ->get();

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->paginate(20);
            $centerIds = $centers->pluck('id');

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

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

        $ageGroupsPerCenter = $fundedChildren->groupBy(function ($child) {
            $activeRecord = $child->records->firstWhere('status', 'active');
            return $activeRecord->child_development_center_id;
        })->mapWithKeys(function ($childrenByCenter, $centerID) use ($nutritionalStatusOccurrences) {
            return [
                $centerID => [
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
                ]
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

        $pdf = PDF::loadView('reports.print.weight-for-age-after-120', compact('cycle', 'centers', 'ageGroupsPerCenter', 'totals', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycle->name . ' Weight for After 120 Feeding Days.pdf');

    }
    public function printWeightForHeightUponEntry(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id2)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::with([
                'users' => function ($query) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('name', 'child development worker');
                    });
                }
            ])->get()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5])
                        ->whereIn('weight_for_height', ['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese']);
                })
                ->with('records')
                ->get();

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5])
                        ->whereIn('weight_for_height', ['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese']);
                })
                ->with([
                    'records' => function ($query) {
                        $query->select('child_id', 'child_development_center_id', 'status');
                    }
                ])
                ->get();

        }

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

        $ageGroupsPerCenter = $fundedChildren->groupBy(function ($child) {
            $activeRecord = $child->records->firstWhere('status', 'active');
            return $activeRecord->child_development_center_id;
        })->mapWithKeys(function ($childrenByCenter, $centerID) use ($nutritionalStatusOccurrences) {
            return [
                $centerID => [
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
                ]
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

        $pdf = PDF::loadView('reports.print.weight-for-height-upon-entry', compact('cycle', 'centers', 'ageGroupsPerCenter', 'totals', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycle->cycle_name . ' Weight for Height Upon Entry.pdf');
    }
    public function printWeightForHeightAfter120(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id2)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::with([
                'users' => function ($query) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('name', 'child development worker');
                    });
                }
            ])->get()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5])
                        ->whereIn('weight_for_height', ['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese']);
                })
                ->with('records')
                ->get();

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5])
                        ->whereIn('weight_for_height', ['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese']);
                })
                ->with([
                    'records' => function ($query) {
                        $query->select('child_id', 'child_development_center_id', 'status');
                    }
                ])
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

        $ageGroupsPerCenter = $fundedChildren->groupBy(function ($child) {
            $activeRecord = $child->records->firstWhere('status', 'active');
            return $activeRecord->child_development_center_id;
        })->mapWithKeys(function ($childrenByCenter, $centerID) use ($nutritionalStatusOccurrences) {
            return [
                $centerID => [
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
                ]
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

        $pdf = PDF::loadView('reports.print.weight-for-height-after-120', compact('cycle', 'centers', 'ageGroupsPerCenter', 'totals', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycle->name . ' Weight for Height After 120 Feeding Days.pdf');
    }
    public function printHeightForAgeUponEntry(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id2)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::with([
                'users' => function ($query) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('name', 'child development worker');
                    });
                }
            ])->get()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5])
                        ->whereIn('height_for_age', ['Normal', 'Stunted', 'Severely Stunted', 'Tall']);
                })
                ->with('records')
                ->get();

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5])
                        ->whereIn('height_for_age', ['Normal', 'Stunted', 'Severely Stunted', 'Tall']);
                })
                ->with([
                    'records' => function ($query) {
                        $query->select('child_id', 'child_development_center_id', 'status');
                    }
                ])
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

        $ageGroupsPerCenter = $fundedChildren->groupBy(function ($child) {
            $activeRecord = $child->records->firstWhere('status', 'active');
            return $activeRecord->child_development_center_id;
        })->mapWithKeys(function ($childrenByCenter, $centerID) use ($nutritionalStatusOccurrences) {
            return [
                $centerID => [
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
                ]
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

        $pdf = PDF::loadView('reports.print.height-for-age-upon-entry', compact('cycle', 'centers', 'ageGroupsPerCenter', 'totals', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycle->name . ' Height for Age Upon Entry.pdf');

    }
    public function printHeightForAgeAfter120(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id2)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::with([
                'users' => function ($query) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('name', 'child development worker');
                    });
                }
            ])->get()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5])
                        ->whereIn('height_for_age', ['Normal', 'Stunted', 'Severely Stunted', 'Tall']);
                })
                ->with('records')
                ->get();

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            $getPsgc = $centers->pluck('psgc_id');

            $province = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('province_name')
                ->unique();

            $city = Psgc::whereIn('psgc_id', $getPsgc)
                ->pluck('city_name')
                ->unique();

            $fundedChildren = Child::whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->where('funded', 1);
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->where('is_undernourish', true)
                        ->whereIn('age_in_years', [2, 3, 4, 5])
                        ->whereIn('height_for_age', ['Normal', 'Stunted', 'Severely Stunted', 'Tall']);
                })
                ->with([
                    'records' => function ($query) {
                        $query->select('child_id', 'child_development_center_id', 'status');
                    }
                ])
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

        $ageGroupsPerCenter = $fundedChildren->groupBy(function ($child) {
            $activeRecord = $child->records->firstWhere('status', 'active');
            return $activeRecord->child_development_center_id;
        })->mapWithKeys(function ($childrenByCenter, $centerID) use ($nutritionalStatusOccurrences) {
            return [
                $centerID => [
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
                ]
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

        $pdf = PDF::loadView('reports.print.height-for-age-after-120', compact('cycle', 'centers', 'ageGroupsPerCenter', 'totals', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycle->name . ' Weight for Height After 120 Feeding Days.pdf');

    }
    public function printAgeBracketUponEntry(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('funded', 1);
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->paginate(5);

                $allCountsPerNutritionalStatus = Child::with([
                    'nutritionalStatus' => function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }
                ])
                    ->whereHas('records', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('funded', 1);
                    })
                    ->get()
                    ->filter(function ($child) {
                        return $child->nutritionalStatus->isNotEmpty();
                    });

            } else {
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle, $cdcId) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('child_development_center_id', $cdcId)
                        ->where('funded', 1);
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->paginate(5);

                $allCountsPerNutritionalStatus = Child::with([
                    'nutritionalStatus' => function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }
                ])
                    ->whereHas('records', function ($query) use ($cycle, $cdcId) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('child_development_center_id', $cdcId)
                            ->where('funded', 1);
                    })
                    ->get()
                    ->filter(function ($child) {
                        return $child->nutritionalStatus->isNotEmpty();
                    });
            }

        } else {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle, $centerIDs) {
                    $query->where('implementation_id', $cycle->id)
                        ->whereIn('child_development_center_id', $centerIDs)
                        ->where('funded', 1);
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->paginate(5);

                $allCountsPerNutritionalStatus = Child::with([
                    'nutritionalStatus' => function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }
                ])
                    ->whereHas('records', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('funded', 1);
                    })
                    ->get()
                    ->filter(function ($child) {
                        return $child->nutritionalStatus->isNotEmpty();
                    });

            } else {
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle, $cdcId) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('child_development_center_id', $cdcId)
                        ->where('funded', 1);
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->paginate(5);

                $allCountsPerNutritionalStatus = Child::with([
                    'nutritionalStatus' => function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }
                ])
                    ->whereHas('records', function ($query) use ($cycle, $cdcId) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('child_development_center_id', $cdcId)
                            ->where('funded', 1);
                    })
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
                            return $child->sex_id == 1 && $child->nutritionalStatus->first()->deworming_date != null;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->sex_id == 2 && $child->nutritionalStatus->first()->deworming_date != null;
                        })->count(),
                    ],
                    'vitamin_a' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->sex_id == 1 && $child->nutritionalStatus->first()->vitamin_a_date != null;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->sex_id == 2 && $child->nutritionalStatus->first()->vitamin_a_date != null;
                        })->count(),
                    ],

                ];
            });



        $pdf = PDF::loadView('reports.print.age-bracket-upon-entry', compact('cycle', 'fundedChildren', 'countsPerNutritionalStatus', 'centerNames', 'cdcId', 'selectedCenter'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycle->name . ' Age Bracket Upon Entry.pdf');
    }

    public function printAgeBracketAfter120(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('funded', 1);
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->paginate(5);

                $allCountsPerNutritionalStatus = Child::with([
                    'nutritionalStatus' => function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }
                ])
                    ->whereHas('records', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('funded', 1);
                    })
                    ->get()
                    ->filter(function ($child) {
                        return $child->nutritionalStatus->isNotEmpty();
                    });

            } else {
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle, $cdcId) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('child_development_center_id', $cdcId)
                        ->where('funded', 1);
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->paginate(5);

                $allCountsPerNutritionalStatus = Child::with([
                    'nutritionalStatus' => function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }
                ])
                    ->whereHas('records', function ($query) use ($cycle, $cdcId) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('child_development_center_id', $cdcId)
                            ->where('funded', 1);
                    })
                    ->get()
                    ->filter(function ($child) {
                        return $child->nutritionalStatus->isNotEmpty();
                    });
            }

        } else {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle, $centerIDs) {
                    $query->where('implementation_id', $cycle->id)
                        ->whereIn('child_development_center_id', $centerIDs)
                        ->where('funded', 1);
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->paginate(5);

                $allCountsPerNutritionalStatus = Child::with([
                    'nutritionalStatus' => function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }
                ])
                    ->whereHas('records', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('funded', 1);
                    })
                    ->get()
                    ->filter(function ($child) {
                        return $child->nutritionalStatus->isNotEmpty();
                    });

            } else {
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle, $cdcId) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('child_development_center_id', $cdcId)
                        ->where('funded', 1);
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->paginate(5);

                $allCountsPerNutritionalStatus = Child::with([
                    'nutritionalStatus' => function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }
                ])
                    ->whereHas('records', function ($query) use ($cycle, $cdcId) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('child_development_center_id', $cdcId)
                            ->where('funded', 1);
                    })
                    ->get()
                    ->filter(function ($child) {

                        return $child->nutritionalStatus->isNotEmpty();
                    });
            }
        }

        $exitCountsPerNutritionalStatus = $allCountsPerNutritionalStatus->map(function ($child) {
            $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5]);

            $entry = $statuses->first();
            $exit = $statuses->skip(1)->first();

            return (object) [
                'child' => $child,
                'entry' => $entry,
                'exit' => $exit,
            ];
        })
            ->filter(function ($child) {
                return $child->exit !== null;
            })
            ->groupBy(function ($child) {
                return $child->exit->age_in_years ?? null;
            })
            ->map(function ($childrenByAge) {
                return [
                    'weight_for_age_normal' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->weight_for_age == 'Normal';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->weight_for_age == 'Normal';
                        })->count(),
                    ],
                    'weight_for_age_underweight' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->weight_for_age == 'Underweight';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->weight_for_age == 'Underweight';
                        })->count(),
                    ],
                    'weight_for_age_severely_underweight' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->weight_for_age == 'Severely Underweight';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->weight_for_age == 'Severely Underweight';
                        })->count(),
                    ],
                    'weight_for_age_overweight' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->weight_for_age == 'Overweight';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->weight_for_age == 'Overweight';
                        })->count(),
                    ],
                    'weight_for_height_normal' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->weight_for_height == 'Normal';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->weight_for_height == 'Normal';
                        })->count(),
                    ],
                    'weight_for_height_wasted' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->weight_for_height == 'Wasted';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->weight_for_height == 'Wasted';
                        })->count(),
                    ],
                    'weight_for_height_severely_wasted' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->weight_for_height == 'Severely Wasted';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->weight_for_height == 'Severely Wasted';
                        })->count(),
                    ],
                    'weight_for_height_overweight' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->weight_for_height == 'Overweight';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->weight_for_height == 'Overweight';
                        })->count(),
                    ],
                    'weight_for_height_obese' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->weight_for_height == 'Obese';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->weight_for_height == 'Obese';
                        })->count(),
                    ],
                    'height_for_age_normal' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->height_for_age == 'Normal';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->height_for_age == 'Normal';
                        })->count(),
                    ],
                    'height_for_age_stunted' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->height_for_age == 'Stunted';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->height_for_age == 'Stunted';
                        })->count(),
                    ],
                    'height_for_age_severely_stunted' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->height_for_age == 'Severely Stunted';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->height_for_age == 'Severely Stunted';
                        })->count(),
                    ],
                    'height_for_age_tall' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->height_for_age == 'Tall';
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->height_for_age == 'Tall';
                        })->count(),
                    ],
                    'is_undernourish' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->is_undernourish == true;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->is_undernourish == true;
                        })->count(),
                    ],
                    'indigenous_people' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->child->is_indigenous_people == true;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->child->is_indigenous_people == true;
                        })->count(),
                    ],
                    'pantawid' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->child->pantawid_details != null;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->child->pantawid_details != null;
                        })->count(),
                    ],
                    'pwd' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->child->person_with_disability_details != null;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->child->person_with_disability_details != null;
                        })->count(),
                    ],
                    'lactose_intolerant' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->child->is_lactose_intolerant == true;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->child->is_lactose_intolerant == true;
                        })->count(),
                    ],
                    'child_of_soloparent' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->child->is_child_of_soloparent == true;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->child->is_child_of_soloparent == true;
                        })->count(),
                    ],
                    'dewormed' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->entry->deworming_date != null;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->entry->deworming_date != null;
                        })->count(),
                    ],
                    'vitamin_a' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->entry->vitamin_a_date != null;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->entry->vitamin_a_date != null;
                        })->count(),
                    ],
                ];
            });

        $pdf = PDF::loadView('reports.print.age-bracket-after-120', compact('cycle', 'fundedChildren', 'exitCountsPerNutritionalStatus', 'centers', 'cdcId', 'selectedCenter'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycle->name . ' Age Bracket After 120 Feeding Days.pdf');
    }

    public function printMonitoring(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex')
            ->whereHas('records', function ($query) use ($cycle) {
                $query->where('implementation_id', $cycle->id)
                    ->where('funded', 1);
            });

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
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if($cdcId == 'all_center') {

                $isFunded = Child::with('records', 'nutritionalStatus', 'sex')
                    ->whereHas('records', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('funded', 1)
                            ->where('status', 'active');
                    })
                    ->whereHas('nutritionalStatus')
                    ->get();

            } else {
                $isFunded = Child::with('records', 'nutritionalStatus', 'sex')
                    ->whereHas('records', function ($query) use ($cycle, $cdcId) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('child_development_center_id', $cdcId)
                            ->where('funded', 1)
                            ->where('status', 'active');
                    })
                    ->whereHas('nutritionalStatus')
                    ->get();

                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
            }

        } else {

            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = Child::with('records', 'nutritionalStatus', 'sex')
                    ->whereHas('records', function ($query) use ($cycle, $centerIDs) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('child_development_center_id', $centerIDs)
                            ->where('funded', 1)
                            ->where('status', 'active');
                    })
                    ->whereHas('nutritionalStatus')
                    ->get();

            } else {
                $isFunded = Child::with('records', 'nutritionalStatus', 'sex')
                    ->whereHas('records', function ($query) use ($cycle, $cdcId) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('child_development_center_id', $cdcId)
                            ->where('funded', 1)
                            ->where('status', 'active');
                    })
                    ->whereHas('nutritionalStatus')
                    ->get();

                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
            }
        }

        $pdf = Pdf::loadView('reports.print.monitoring', compact('cycle', 'isFunded', 'centers', 'cdcId', 'selectedCenter'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycle->name . ' Weight and Height Monitoring.pdf');

    }
    public function printUnfunded(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $unfundedChildren = Child::with('records', 'sex', 'psgc')
            ->whereHas('records', function ($query) use ($cycle) {
                $query->where('implementation_id', $cycle->id)
                    ->where('status', 'active')
                    ->where('funded', 0);
            });


        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            if ($cdcId == 'all_center') {
                $isNotFunded = $unfundedChildren->paginate(20);
            } else {
                $isNotFunded = $unfundedChildren->whereHas('records', function ($query) use ($cycle, $cdcId) {
                    $query->where('child_development_center_id', $cdcId);
                })
                    ->paginate(20);

                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
            }
        } else {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isNotFunded = $unfundedChildren->whereHas('records', function ($query) use ($cycle, $centerIDs) {
                    $query->whereIn('child_development_center_id', $centerIDs);
                })
                    ->paginate(20);

            } else {
                $isNotFunded = $unfundedChildren->whereHas('records', function ($query) use ($cycle, $cdcId) {
                    $query->where('child_development_center_id', $cdcId);
                })
                    ->paginate(20);

                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
            }
        }

        $pdf = Pdf::loadView('reports.print.unfunded', compact('cycle', 'isNotFunded', 'centers', 'cdcId', 'selectedCenter'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycle->name . ' Unfunded Children.pdf');

    }
}
