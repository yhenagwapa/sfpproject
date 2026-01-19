<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ChildDevelopmentCenter;
use App\Models\Implementation;
use App\Models\User;
use App\Models\UserCenter;
use Barryvdh\DomPDF\Facade\Pdf;

class AgeBracketUponEntry extends Controller
{
    public static function generateAgeBracketUponEntryReport($userId, $cdcId)
    {
        $user = User::find($userId);
        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();

        if (!$cycle) {
            throw new \Exception('No active regular cycle found.');
        }
        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        if ($user->hasRole('admin')) {
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


        $folder = public_path("generated_reports/{$userId}");
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $fileName = "Age Bracket Upon Entry Report_" . now()->format('m_d_Y_H_m_s') . ".pdf";
        $filePath = $folder . '/' . $fileName;

        // 4️⃣ Save PDF
        $pdf->save($filePath);

//        return back()->with('success', 'Age Bracket Upon Entry Report is now available for download.');
    }
}
