<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ChildDevelopmentCenter;
use App\Models\Implementation;
use App\Models\User;
use App\Models\UserCenter;
use Barryvdh\DomPDF\Facade\Pdf;

class AgeBracketAfter120 extends Controller
{
    public static function generateAgeBracketAfter120Report($userId, $cdcId)
    {
        $user = User::find($userId);
        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();

        if (!$cycle) {
            return back()->with('error', 'No active regular cycle found.');
        }

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
                        ->where('action_type', 'active');
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
                            ->where('action_type', 'active');
                    })
                    ->get()
                    ->filter(function ($child) {
                        return $child->nutritionalStatus->isNotEmpty();
                    });
            }

        } else {
            $centers = UserCenter::where('user_id', $userId)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cycle, $centerIDs) {
                    $query->where('implementation_id', $cycle->id)
                        ->whereIn('child_development_center_id', $centerIDs)
                        ->where('funded', 1)
                        ->where('action_type', 'active');
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
                            ->where('action_type', 'active');
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
                        ->where('action_type', 'active');
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
                            ->where('action_type', 'active');
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


        $folder = public_path("generated_reports/{$userId}");
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $fileName = "Age Bracket After 120 Report_" . now()->format('m_d_Y_H_m_s') . ".pdf";
        $filePath = $folder . '/' . $fileName;

        // 4️⃣ Save PDF
        $pdf->save($filePath);

        return back()->with('success', 'Age Bracket Upon Entry Report is now available for download.');
    }
}
