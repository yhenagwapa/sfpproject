<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\ChildDevelopmentCenter;
use App\Models\Psgc;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Child;
use App\Models\UserCenter;
use App\Models\User;
use App\Models\Implementation;

class MalnourishedReportGeneration extends Controller
{
    public static function generateMalnourishedReport($userId, $cdcId)
    {
        $user = User::find($userId);

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();

        if (!$cycle) {
            throw new \Exception('No active regular cycle found.');
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
            $centers = UserCenter::where('user_id', $userId)->get();
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
                    ->where('action_type', 'active')
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

        $folder = public_path("generated_reports/{$userId}");
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $fileName = "Malnourished Children_" . now()->format('m_d_Y_H_m_s') . ".pdf";
        $filePath = $folder . '/' . $fileName;

        // 4️⃣ Save PDF
        $pdf->save($filePath);
    }
}
