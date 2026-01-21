<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChildCenter;
use App\Models\ChildDevelopmentCenter;
use App\Models\Psgc;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Child;
use App\Models\UserCenter;
use App\Models\User;
use App\Models\Implementation;

class UndernourishedAfter120ReportGeneration extends Controller
{
    public static function generateUndernourishedAfter120Report($userId)
    {
        $user = User::find($userId);
        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();

        if (!$cycle) {
            throw new \Exception('No active regular cycle found.');
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
                    ->where('funded', 1)
                    ->where('action_type', 'active');
            })
                ->whereHas('nutritionalStatus', function ($query) {
                    $query->whereIn('age_in_years', [2, 3, 4, 5])
                    ->where('is_undernourish', true);
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
                    ->where('funded', 1)
                    ->where('action_type', 'active');
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

            $folder = public_path("generated_reports/{$userId}");
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $fileName = "Undernourished After 120_" . now()->format('m_d_Y_H_m_s') . ".pdf";
        $filePath = $folder . '/' . $fileName;

        // 4️⃣ Save PDF
        $pdf->save($filePath);
    }
}
