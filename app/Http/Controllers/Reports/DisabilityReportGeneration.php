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

class DisabilityReportGeneration extends Controller
{
    public static function generateDisabilityReport($userId, $cdcId)
    {
        $user = User::find($userId);
        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();

        if (!$cycle) {
            throw new \Exception('No active regular cycle found.');
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
                    ->where('action_type', 'active')
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

        $folder = public_path("generated_reports/{$userId}");
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $fileName = "Persons with Disability_" . now()->format('m_d_Y_H_m_s') . ".pdf";
        $filePath = $folder . '/' . $fileName;

        // 4️⃣ Save PDF
        $pdf->save($filePath);
    }
}
