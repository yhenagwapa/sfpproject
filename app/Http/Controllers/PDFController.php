<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChildDevelopmentCenter;
use App\Models\Psgc;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Child;
use App\Models\UserCenter;
use App\Models\Implementation;

class PDFController extends Controller
{
    public function printMasterlist(Request $request)
    {
        $cycle = Implementation::where('id', $request->cycle_id)->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        $fundedChildren = Child::with('records','nutritionalStatus', 'sex');

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');
            $centerIDs = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                    if ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                                ->where('funded', 1);
                    }
                })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id);
                })
                ->paginate(5);

            } else {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
                    if ($cycle) {
                        $query->where('child_development_center_id', $cdcId)
                                ->where('implementation_id', $cycle->id)
                                ->where('funded', 1);
                    }
                })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id);
                })
                ->paginate(5);
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
                            ->where('funded', 1);
                    }
                })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id);
                })
                ->paginate(5);
            } else {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
                    if ($cycle) {
                        $query->where('child_development_center_id', $cdcId)
                                ->where('implementation_id', $cycle->id)
                                ->where('funded', 1);
                    }
                })
                ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                    $query->where('implementation_id', $cycle->id);
                })
                ->paginate(5);
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
            }

        }

        $pdf = PDF::loadView('reports.print.masterlist', compact('isFunded', 'centers', 'cdcId', 'selectedCenter', 'cycle', 'centerNames'))
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
        $cycle = Implementation::where('status', 'active')
            ->where('type', 'regular')->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $fundedChildren = Child::with('records','nutritionalStatus', 'sex');

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
                if ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                            ->where('status', 'active')
                            ->where('funded', 1)
                            ->orderBy('child_development_center_id');
                }
            })
            ->whereHas('sex', function ($query) {
                $query->where('name', 'Male');
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
                    if ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                                ->where('status', 'active')
                                ->where('funded', 1)
                                ->whereIn('child_development_center_id', $centerIDs)
                                ->orderBy('child_development_center_id');
                    }
                })
                ->whereHas('sex', function ($query) {
                    $query->where('name', 'Male');
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
    public function printDisabilities()
    {
        $cycle = Implementation::where('status', 'active')
            ->where('type', 'regular')->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

        $childrenWithDisabilities = Child::with('records','nutritionalStatus', 'sex')
            ->where('person_with_disability_details', '!=', null);

        $province = null;
        $city = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            $isPwdChildren = $childrenWithDisabilities->whereHas('records', function ($query) use ($cycle) {
                if ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                            ->where('status', 'active')
                            ->where('funded', 1)
                            ->orderBy('child_development_center_id');
                }
            })
            ->whereHas('sex', function ($query) {
                $query->where('name', 'Male');
            })
            ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                $query->where('implementation_id', $cycle->id);;
            })
            ->paginate('10');

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $userID = auth()->id();
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $focalCenters = ChildDevelopmentCenter::whereIn('id', $centerIDs);

            $isPwdChildren = $childrenWithDisabilities->whereHas('records', function ($query) use ($cycle, $centerIDs) {
                if ($cycle) {
                    $query->where('implementation_id', $cycle->id)
                            ->where('status', 'active')
                            ->where('funded', 1)
                            ->whereIn('child_development_center_id', $centerIDs)
                            ->orderBy('child_development_center_id');
                }
            })
            ->whereHas('sex', function ($query) {
                $query->where('name', 'Male');
            })
            ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                $query->where('implementation_id', $cycle->id);;
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
    public function printUndernourishedUponEntry()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $province = null;
        $city = null;

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

        $pdf = PDF::loadView('reports.print.undernourished-upon-entry', compact('cycleImplementation', 'centers', 'ageGroupsPerCenter', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycleImplementation->cycle_name . ' Undernourished Upon Entry.pdf');
    }
    public function printUndernourishedAfter120()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $province = null;
        $city = null;

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

        $pdf = PDF::loadView('reports.print.undernourished-after-120', compact('cycleImplementation', 'centers', 'exitAgeGroupsPerCenter', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycleImplementation->cycle_name . ' Undernourished After 120 Feedings.pdf');
    }
    public function printWeightForAgeUponEntry()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        $province = null;
        $city = null;

        if(auth()->user()->hasRole('admin')){
            $centers = ChildDevelopmentCenter::paginate(20);
            $centers->getCollection()->keyBy('id');

            $province = null;
            $city = null;

            $fundedChildren = Child::with('sex', 'nutritionalStatus', 'center')
            ->where('children.is_funded', true)
            ->where('children.cycle_implementation_id', $cycleImplementation->id)
            ->whereHas('nutritionalStatus', function ($query) {
                $query->whereIn('age_in_years', [2, 3, 4, 5]);
            })
            ->get();

        } elseif(auth()->user()->hasRole('lgu focal')){
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

        $pdf = PDF::loadView('reports.print.weight-for-age-upon-entry', compact('cycleImplementation', 'centers', 'ageGroupsPerCenter', 'totals', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 0.5,
                'margin-bottom' => 0.5,
                'margin-left' => 0.5
            ]);


        return $pdf->stream($cycleImplementation->cycle_name . ' Weight for Age Upon Entry.pdf');

    }
    public function printWeightForAgeAfter120()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        $province = null;
        $city = null;

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

        $pdf = PDF::loadView('reports.print.weight-for-age-after-120', compact('cycleImplementation', 'centers', 'ageGroupsPerCenter', 'totals', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycleImplementation->cycle_name . ' Weight for After 120 Feeding Days.pdf');

    }
    public function printWeightForHeightUponEntry()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        $province = null;
        $city = null;

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

        $pdf = PDF::loadView('reports.print.weight-for-height-upon-entry', compact('cycleImplementation', 'centers', 'ageGroupsPerCenter', 'totals', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycleImplementation->cycle_name . ' Weight for Height Upon Entry.pdf');
    }
    public function printWeightForHeightAfter120()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        $province = null;
        $city = null;

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

        $pdf = PDF::loadView('reports.print.weight-for-height-after-120', compact('cycleImplementation', 'centers', 'ageGroupsPerCenter', 'totals', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycleImplementation->cycle_name . ' Weight for Height After 120 Feeding Days.pdf');
    }
    public function printHeightForAgeUponEntry()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        $province = null;
        $city = null;

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

        $pdf = PDF::loadView('reports.print.height-for-age-upon-entry', compact('cycleImplementation', 'centers', 'ageGroupsPerCenter', 'totals', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycleImplementation->cycle_name . ' Height for Age Upon Entry.pdf');

    }
    public function printHeightForAgeAfter120()
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        $province = null;
        $city = null;

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

        $pdf = PDF::loadView('reports.print.height-for-age-after-120', compact('cycleImplementation', 'centers', 'ageGroupsPerCenter', 'totals', 'province', 'city'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycleImplementation->cycle_name . ' Weight for Height After 120 Feeding Days.pdf');

    }
    public function printAgeBracketUponEntry(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

        if (!$cycleImplementation) {
            return view('reports.monitoring', [
                'fundedChildren' => collect(),
            ]);
        }

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all();
            $centerIds = $centers->pluck('id');

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
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
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
                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
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
                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
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



        $pdf = PDF::loadView('reports.print.age-bracket-upon-entry', compact('cycleImplementation', 'fundedChildren','countsPerNutritionalStatus', 'centers', 'cdcId', 'selectedCenter'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycleImplementation->cycle_name . ' Age Bracket Upon Entry.pdf');
    }
    public function printAgeBracketAfter120(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

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
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
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
                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
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
                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
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

        $exitCountsPerNutritionalStatus = $allCountsPerNutritionalStatus->map(function ($child) {
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
                            return $child['sex_id'] == 1 && $child['exit']->is_child_of_soloparent == true;
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

            $pdf = PDF::loadView('reports.print.age-bracket-after-120', compact('cycleImplementation', 'fundedChildren', 'exitCountsPerNutritionalStatus', 'centers', 'cdcId', 'selectedCenter'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);


        return $pdf->stream($cycleImplementation->cycle_name . ' Age Bracket After 120 Feeding Days.pdf');
    }

    public function printMonitoring(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

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

                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
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

                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
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

                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
            }

        }

        $pdf = PDF::loadView('reports.print.monitoring', compact('cycleImplementation', 'isFunded', 'centers', 'cdcId', 'selectedCenter'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycleImplementation->cycle_name . ' Weight and Height Monitoring.pdf');

    }
    public function printUnfunded(Request $request)
    {
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $cdcId = $request->input('center_name', 'all_center');
        $selectedCenter = null;

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
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
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
                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
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
                $selectedCenter = ChildDevelopmentCenter::find($cdcId);
            }

        }

        $pdf = PDF::loadView('reports.print.unfunded', compact('cycleImplementation', 'isNotFunded', 'centers', 'cdcId', 'selectedCenter'))
            ->setPaper('folio', 'portrait')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 1,
                'margin-bottom' => 0.5,
                'margin-left' => 1
            ]);

        return $pdf->stream($cycleImplementation->cycle_name . ' Unfunded Children.pdf');

    }
}
