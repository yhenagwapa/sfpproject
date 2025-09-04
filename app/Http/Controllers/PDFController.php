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

    public function showMasterlist(Request $request)
    {
        session(['report_cycle_id' => $request->input(key: 'cycle_id')]);
        session(['center_name' => $request->input(key: 'center_name')]);

        return redirect()->route('reports.print.masterlist');
    }
    public function printMasterlist(Request $request)
    {

        $cycleID = session('report_cycle_id');
        $cycle = Implementation::find($cycleID);

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = session('center_name');
        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex')
            ->orderByRaw("CASE WHEN sex_id = 1 THEN 0 ELSE 1 END")
            ->orderByRaw("LOWER(lastname) ASC");

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                    if ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('funded', 1)
                            ->where('action_type', 'active');
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

        //  // sort by gender
        // $isFunded = $isFunded->sort(function($a, $b) {
        //     // 1) gender priority
        //     if ($a->sex->name === 'Male' && $b->sex->name !== 'Male') {
        //         return -1;
        //     }
        //     if ($b->sex->name === 'Male' && $a->sex->name !== 'Male') {
        //         return 1;
        //     }
        //     // 2) same gender, compare full_name
        //     return strcmp($a->full_name, $b->full_name);
        // });

        $pdf = Pdf::loadView('reports.print.masterlist', compact('isFunded', 'centers', 'cdcId', 'selectedCenter', 'cycle', 'centerNames'))
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

        return $pdf->stream($cycle->name . ' Masterlist.pdf');
    }

    public function showAgeBracketUponEntry(Request $request)
    {
        session(['report_cycle_id' => $request->input(key: 'cycle_id')]);
        session(['center_name' => $request->input(key: 'center_name')]);

        return redirect()->route('reports.print.age-bracket-upon-entry');
    }
    public function printAgeBracketUponEntry(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = session('center_name');
        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('funded', 1)
                        ->where('action_type', 'active');
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

            } else {
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle, $cdcId) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('child_development_center_id', $cdcId)
                        ->where('funded', 1)
                        ->where('status', 'active');
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();
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
                        ->where('funded', 1)
                        ->where('status', 'active');
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();



            } else {
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle, $cdcId) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('child_development_center_id', $cdcId)
                        ->where('funded', 1)
                        ->where('status', 'active');
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();


            }
        }

        $allCountsPerNutritionalStatus = $isFunded->filter(function ($child) {
            return $child->nutritionalStatus->isNotEmpty();
        });

        $countsPerNutritionalStatus = $allCountsPerNutritionalStatus->groupBy(function ($child) {
            $oldestStatus = $child->nutritionalStatus()->oldest()->first();
            $ageInYears = $oldestStatus->age_in_years;
            return $ageInYears ?? null;
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
                'margin-bottom' => 50,
                'margin-left' => 1,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true
            ]);


        return $pdf->stream($cycle->name . ' Age Bracket Upon Entry.pdf');
    }

    public function showAgeBracketAfter120(Request $request)
    {
        session(['report_cycle_id' => $request->input(key: 'cycle_id')]);
        session(['center_name' => $request->input(key: 'center_name')]);

        return redirect()->route('reports.print.age-bracket-after-120');
    }
    public function printAgeBracketAfter120(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = session('center_name');
        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');
        $allCountsPerNutritionalStatus = collect();

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('funded', 1)
                        ->where('action_type', 'active');
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

            } else {
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);

                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle, $cdcId) {
                    $query->where('implementation_id', $cycle->id)
                        ->where('child_development_center_id', $cdcId)
                        ->where('funded', 1)
                        ->where('status', 'active');
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with([
                    'nutritionalStatus' => function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }
                ])
                    ->whereHas('records', function ($query) use ($cycle, $cdcId) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('child_development_center_id', $cdcId)
                            ->where('funded', 1)
                            ->where('status', 'active');
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
                        ->where('funded', 1)
                        ->where('status', 'active');
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with([
                    'nutritionalStatus' => function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }
                ])
                    ->whereHas('records', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('funded', 1)
                            ->where('status', 'active');
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
                        ->where('funded', 1)
                        ->where('status', 'active');
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->whereIn('age_in_years', [2, 3, 4, 5]);
                    })
                    ->get();

                $allCountsPerNutritionalStatus = Child::with([
                    'nutritionalStatus' => function ($query) {
                        $query->whereIn('age_in_years', [2, 3, 4, 5]);
                    }
                ])
                    ->whereHas('records', function ($query) use ($cycle, $cdcId) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('child_development_center_id', $cdcId)
                            ->where('funded', 1)
                            ->where('status', 'active');
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
                            return $child->child->sex_id == 1 && $child->exit->deworming_date != null;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->deworming_date != null;
                        })->count(),
                    ],
                    'vitamin_a' => [
                        'male' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 1 && $child->exit->vitamin_a_date != null;
                        })->count(),
                        'female' => $childrenByAge->filter(function ($child) {
                            return $child->child->sex_id == 2 && $child->exit->vitamin_a_date != null;
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
    public function showMonitoring(Request $request)
    {
        session(['report_cycle_id' => $request->input(key: 'cycle_id')]);
        session(['center_name' => $request->input(key: 'center_name')]);

        return redirect()->route('reports.print.monitoring');
    }
    public function printMonitoring(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = session('center_name');
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
                            ->where('action_type', 'active');
                    })
                    ->whereHas('nutritionalStatus')
                    ->orderByRaw("CASE WHEN sex_id = 1 THEN 0 ELSE 1 END")
                    ->orderByRaw("LOWER(lastname) ASC")
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
                    ->orderByRaw("CASE WHEN sex_id = 1 THEN 0 ELSE 1 END")
                    ->orderByRaw("LOWER(lastname) ASC")
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
                    ->orderByRaw("CASE WHEN sex_id = 1 THEN 0 ELSE 1 END")
                    ->orderByRaw("LOWER(lastname) ASC")
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
                    ->orderByRaw("CASE WHEN sex_id = 1 THEN 0 ELSE 1 END")
                    ->orderByRaw("LOWER(lastname) ASC")
                    ->get();

                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
            }
        }

        // sort by gender
        // $isFunded = $isFunded->sort(function($a, $b) {
        //     // 1) gender priority
        //     if ($a->sex->name === 'Male' && $b->sex->name !== 'Male') {
        //         return -1;
        //     }
        //     if ($b->sex->name === 'Male' && $a->sex->name !== 'Male') {
        //         return 1;
        //     }
        //     // 2) same gender, compare full_name
        //     return strcmp($a->full_name, $b->full_name);
        // });

        $pdf = Pdf::loadView('reports.print.monitoring', compact('cycle', 'isFunded', 'centers', 'cdcId', 'selectedCenter'))
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

        return $pdf->stream($cycle->name . ' Weight and Height Monitoring.pdf');

    }
    public function showUnfunded(Request $request)
    {
        session(['report_cycle_id' => $request->input(key: 'cycle_id')]);
        session(['center_name' => $request->input(key: 'center_name')]);

        return redirect()->route('reports.print.unfunded');
    }
    public function printUnfunded(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::find($cycleID);

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = session('center_name');
        $selectedCenter = null;

        $unfundedChildren = Child::with('records', 'sex', 'nutritionalStatus')
            ->whereHas('records', function ($query) use ($cycle) {
                $query->where('implementation_id', $cycle->id)
                    ->where('action_type', 'active')
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
                'margin-bottom' => 50,
                'margin-left' => 1,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->stream($cycle->name . ' Unfunded Children.pdf');

    }
    public function showMalnourished(Request $request)
    {
        session(['report_cycle_id2' => $request->input(key: 'cycle_id2')]);

        return redirect()->route('reports.print.malnourished');
    }
    public function printMalnourish(Request $request)
    {
        $cycleID = session('report_cycle_id2');
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
                    ->where('action_type', 'active')
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
                'margin-bottom' => 50,
                'margin-left' => 1,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->stream($cycle->cycle_name . ' Malnourished.pdf');
    }
    public function showDisabilities(Request $request)
    {
        session(['report_cycle_id2' => $request->input(key: 'cycle_id2')]);
        session(['center_name2' => $request->input(key: 'center_name2')]);

        return redirect()->route('reports.print.disabilities');
    }
    public function printDisabilities(Request $request)
    {
        $cycleID = session('report_cycle_id2');
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
                    ->where('action_type', 'active')
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
                'margin-bottom' => 50,
                'margin-left' => 1,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->stream($cycle->name . ' Persons with Disability.pdf');
    }
    public function showUndernourishedUponEntry(Request $request)
    {
        session(['report_cycle_id2' => $request->input(key: 'cycle_id2')]);

        return redirect()->route('reports.print.undernourished-upon-entry');
    }
    public function printUndernourishedUponEntry(Request $request)
    {
        $cycleID = session('report_cycle_id2');
        $cycle = Implementation::where('id', $cycleID)->first();

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
                    $query->whereIn('age_in_years', [2, 3, 4, 5])
                        ->where('is_undernourish', true);
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
                    $query->whereIn('age_in_years', [2, 3, 4, 5])
                        ->where('is_undernourish', true);
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
            $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5])
                ->where('is_undernourish', true);

            $entry = $statuses->first();

            return [
                'child_id' => $child->id,
                'entry' => $entry,
            ];
        });

        $ageGroupsPerCenter = $fundedChildren->filter(function ($child) {
            return $child->records->firstWhere('status', 'active') !== null;
        })->groupBy(function ($child) {
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
                            return $firstStatus && $child->is_indigenous_people == true && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $firstStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['entry'];
                            return $firstStatus && $child->is_indigenous_people == true && $child->sex_id == 2;
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
                'margin-bottom' => 50,
                'margin-left' => 1,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->stream($cycle->name . ' Undernourished Upon Entry.pdf');
    }
    public function showUndernourishedAfter120(Request $request)
    {
        session(['report_cycle_id2' => $request->input(key: 'cycle_id2')]);

        return redirect()->route('reports.print.undernourished-after-120');
    }
    public function printUndernourishedAfter120(Request $request)
    {
        $cycleID = session('report_cycle_id2');
        $cycle = Implementation::where('id', $cycleID)->first();

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
                    ->where('funded', 1)
                    ->where('status', 'active');
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5])
                    ->where('is_undernourish', true);
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
                    ->where('funded', 1)
                    ->where('status', 'active');
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5])
                    ->where('is_undernourish', true);
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
            $statuses = $child->nutritionalStatus->whereIn('age_in_years', [2, 3, 4, 5])
                ->where('is_undernourish', true);

            $exit = $statuses->skip(1)->first();

            return [
                'child_id' => $child->id,
                'exit' => $exit,
            ];
        });

        $exitAgeGroupsPerCenter = $fundedChildren->filter(function ($child) {
            return $child->records->firstWhere('status', 'active') !== null;
        })->groupBy(function ($child) {
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
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $child->is_indigenous_people == true && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $child->is_indigenous_people == true && $child->sex_id == 2;
                        })->count(),
                    ],
                    'pantawid' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $child->pantawid_details != null && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $child->pantawid_details != null && $child->sex_id == 2;
                        })->count(),
                    ],
                    'pwd' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $child->person_with_disability_details != null && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $child->person_with_disability_details != null && $child->sex_id == 2;
                        })->count(),
                    ],
                    'lactose_intolerant' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $child->is_lactose_intolerant == true && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $child->is_lactose_intolerant == true && $child->sex_id == 2;
                        })->count(),
                    ],
                    'child_of_solo_parent' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $child->is_child_of_soloparent == true && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $child->is_child_of_soloparent == true && $child->sex_id == 2;
                        })->count(),
                    ],
                    'dewormed' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $exitStatus->deworming_date != null && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $exitStatus->deworming_date != null && $child->sex_id == 2;
                        })->count(),
                    ],
                    'vitamin_a' => [
                        'male' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $exitStatus->vitamin_a_date != null && $child->sex_id == 1;
                        })->count(),
                        'female' => $childrenByCenter->filter(function ($child) use ($nutritionalStatusOccurrences) {
                            $exitStatus = $nutritionalStatusOccurrences->firstWhere('child_id', $child->id)['exit'];
                            return $exitStatus && $exitStatus->vitamin_a_date != null && $child->sex_id == 2;
                        })->count(),
                    ],
                ]
            ];
        });

        $pdf = PDF::loadView('reports.print.undernourished-after-120', compact('cycle', 'centerNames', 'exitAgeGroupsPerCenter', 'province', 'city'))
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

        return $pdf->stream($cycle->name . ' Undernourished After 120 Feedings.pdf');
    }
}
