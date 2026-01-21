<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ChildDevelopmentCenter;
use App\Models\Implementation;
use App\Models\User;
use App\Models\UserCenter;
use Barryvdh\DomPDF\Facade\Pdf;

class UnfundedReportGeneration extends Controller
{
    public static function generateUnfundedReport($userId, $cdcId)
    {
        $user = User::find($userId);
        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();

        if (!$cycle) {
            throw new \Exception('No active regular cycle found.');
        }

        $selectedCenter = null;

        $unfundedChildren = Child::with('records', 'sex', 'nutritionalStatus')
            ->whereHas('records', function ($query) use ($cycle) {
                $query->where('implementation_id', $cycle->id)
                    ->where('action_type', 'active')
                    ->where('funded', 0);
            });


        if ($user->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            if ((int)$cdcId === 0) {
                $isNotFunded = $unfundedChildren->paginate(20);
            } else {
                $isNotFunded = $unfundedChildren->whereHas('records', function ($query) use ($cycle, $cdcId) {
                    $query->where('child_development_center_id', $cdcId);
                })
                    ->paginate(20);

                $selectedCenter = ChildDevelopmentCenter::with('psgc')->find($cdcId);
            }
        } else {
            $centers = UserCenter::where('user_id', $userId)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ((int)$cdcId === 0) {
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

        $folder = public_path("generated_reports/{$userId}");
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $fileName = "Monitoring_" . now()->format('m_d_Y_H_m_s') . ".pdf";
        $filePath = $folder . '/' . $fileName;

        // 4️⃣ Save PDF
        $pdf->save($filePath);
    }
}
