<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ChildDevelopmentCenter;
use App\Models\Implementation;
use App\Models\User;
use App\Models\UserCenter;
use Barryvdh\DomPDF\Facade\Pdf;

class MasterlistReportGeneration extends Controller
{
    public static function generateMasterlistReport($userId, $cdcId)
    {

        $user = User::find($userId);

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();

        if (!$cycle) {
            throw new \Exception('No active regular cycle found.');
        }

        $selectedCenter = null;

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex')
            ->orderByRaw("CASE WHEN sex_id = 1 THEN 0 ELSE 1 END")
            ->orderByRaw("LOWER(lastname) ASC");

        if ($user->hasRole('admin')) {
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
                        $query->where('implementation_id', $cycle->id)
                            ->where('funded', 1)
                            ->where('action_type', 'active')
                            ->where('child_development_center_id', $cdcId);
                    }
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->get();
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
            }

        } else {
            $centers = UserCenter::where('user_id', $userId)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($centerIDs, $cycle) {
                    if ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('funded', 1)
                            ->where('action_type', 'active')
                            ->where('child_development_center_id', $centerIDs);
                    }
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->get();
            } else {
                $isFunded = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
                    if ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('funded', 1)
                            ->where('action_type', 'active')
                            ->where('child_development_center_id', $cdcId);
                    }
                })
                    ->whereHas('nutritionalStatus', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id);
                    })
                    ->get();
                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
            }

        }

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

        $folder = public_path("generated_reports/{$userId}");
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $fileName = "Masterlist_" . now()->format('m_d_Y_H_m_s') . ".pdf";
        $filePath = $folder . '/' . $fileName;

        // 4️⃣ Save PDF
        $pdf->save($filePath);

    }
}
