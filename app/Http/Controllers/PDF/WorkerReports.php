<?php

namespace App\Http\Controllers\PDF;

use App\Models\Child;
use App\Models\ChildDevelopmentCenter;
use App\Models\Implementation;
use App\Models\UserCenter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

trait WorkerReports
{

    public function printMasterlist2(Request $request)
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
                    $query->where('child_development_center_id', $cdcId->center_name)
                            ->where('implementation_id', $cycle->id)
                            ->where('funded', 1)
                            ->where('status', 'active');
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

        $pdf = PDF::loadView('reports.print.masterlist', compact('isFunded', 'centers', 'cdcId', 'selectedCenter', 'cycle', 'centerNames'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => .05,
                'margin-bottom' => 0.5,
                'margin-left' => 0.5
            ]);

        return $pdf->stream($cycle->name . ' Masterlist.pdf');
    }


    public function printAgeBracketUponEntry2(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = session('filter_cdc_id');
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


    public function printAgeBracketAfter1202(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = session('filter_cdc_id');
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


    public function printMonitoring2(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = session('filter_cdc_id');
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
                            ->where('funded', 1);
                    })
                    ->whereHas('nutritionalStatus')
                    ->paginate(10);


            } else {
                $isFunded = Child::with('records', 'nutritionalStatus', 'sex')
                    ->whereHas('records', function ($query) use ($cycle, $cdcId) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('child_development_center_id', $cdcId)
                            ->where('funded', 1);
                    })
                    ->whereHas('nutritionalStatus')
                    ->paginate(10);

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
                            ->where('funded', 1);
                    })
                    ->whereHas('nutritionalStatus')
                    ->paginate(10);

            } else {
                $isFunded = Child::with('records', 'nutritionalStatus', 'sex')
                    ->whereHas('records', function ($query) use ($cycle, $cdcId) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('child_development_center_id', $cdcId)
                            ->where('funded', 1);
                    })
                    ->whereHas('nutritionalStatus')
                    ->paginate(10);

                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
            }
        }

        $pdf = PDF::loadView('reports.print.monitoring', compact('cycle', 'isFunded', 'centers', 'cdcId', 'selectedCenter'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycle->name . ' Weight and Height Monitoring.pdf');

    }
    public function printUnfunded2(Request $request)
    {
        $cycleID = session('report_cycle_id');
        $cycle = Implementation::where('id', $cycleID)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = session('filter_cdc_id');
        $selectedCenter = null;

        $unfundedChildren = Child::with('records', 'nutritionalStatus', 'sex', 'psgc')
            ->whereHas('records', function ($query) use ($cycle) {
                $query->where('implementation_id', $cycle->id)
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

        $pdf = PDF::loadView('reports.print.unfunded', compact('cycle', 'isNotFunded', 'centers', 'cdcId', 'selectedCenter'))
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
