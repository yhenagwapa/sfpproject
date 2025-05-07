<?php

namespace App\Http\Controllers\PDF;

use App\Models\Child;
use App\Models\ChildDevelopmentCenter;
use App\Models\Implementation;
use App\Models\Psgc;
use App\Models\UserCenter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

trait FocalReports
{
    public function printMalnourish2(Request $request)
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
                    ->orderBy('child_development_center_id');
            })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('is_malnourish', 1);
                })
                ->paginate('10');

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
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->orderBy('child_development_center_id');
            })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('is_malnourish', 1);
                })
                ->paginate('10');
        }

        $pdf = PDF::loadView('reports.print.malnourished', compact('cycle', 'isFunded', 'centers', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycle->cycle_name . ' Malnourished.pdf');
    }

    public function printDisabilities2(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

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
                    ->orderBy('child_development_center_id');
            })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id);
                    ;
                })
                ->paginate('10');

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $focalCenters = ChildDevelopmentCenter::whereIn('id', $centerIDs);

            $isPwdChildren = $childrenWithDisabilities->whereHas('records', function ($query) use ($cycle, $centerIDs) {
                $query->where('implementation_id', $cycle->id)
                    ->where('funded', 1)
                    ->whereIn('child_development_center_id', $centerIDs)
                    ->orderBy('child_development_center_id');
            })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id);
                    ;
                })
                ->paginate('10');
        }

        $pdf = PDF::loadView('reports.print.disabilities', compact('cycle', 'isPwdChildren', 'centers', 'province', 'city'))
            ->setPaper('folio')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycle->name . ' Persons with Disability.pdf');
    }
    public function printUndernourishedUponEntry2(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = session('filter_cdc_id');
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
    public function printUndernourishedAfter1202(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = session('filter_cdc_id');
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
}
