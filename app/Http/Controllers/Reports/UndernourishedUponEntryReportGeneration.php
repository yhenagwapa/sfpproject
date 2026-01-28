<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UndernourishedUponEntryReportGeneration extends Controller
{
    public static function generateReport($userId, $cdcId = 0)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(600);

        $user = User::find($userId);

        $cycle = DB::table('implementations')
            ->where('status', 'active')
            ->where('type', 'regular')
            ->first();

        if (!$cycle) {
            throw new \Exception('No active regular cycle found.');
        }

        $centerIDs = null;
        if ($cdcId && $cdcId != 0) {
            $centerIDs = [$cdcId];
        } elseif (!$user->hasRole('admin')) {
            $centerIDs = DB::table('user_centers')
                ->where('user_id', $userId)
                ->pluck('child_development_center_id')
                ->toArray();
        }

        // Create report record
        $reportId = DB::table('undernourished_upon_entry_reports')->insertGetId([
            'user_id' => $userId,
            'implementation_id' => $cycle->id,
            'status' => 'generating',
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $totalRecords = 0;

            self::fetchAndStoreData($reportId, $cycle->id, $centerIDs, $totalRecords);

            DB::table('undernourished_upon_entry_reports')
                ->where('id', $reportId)
                ->update([
                    'status' => 'pending',
                    'total_records' => $totalRecords,
                    'updated_at' => now(),
                ]);

            return $reportId;

        } catch (\Exception $e) {
            DB::table('undernourished_upon_entry_reports')
                ->where('id', $reportId)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'updated_at' => now(),
                ]);

            throw $e;
        }
    }

    public static function generatePDF($userId)
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(1800);

        $report = DB::table('undernourished_upon_entry_reports')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$report) {
            throw new \Exception('No pending undernourished upon entry report found for this user.');
        }

        DB::table('undernourished_upon_entry_reports')
            ->where('id', $report->id)
            ->update([
                'status' => 'generating',
                'updated_at' => now(),
            ]);

        try {
            $user = User::find($userId);
            $cycle = DB::table('implementations')->find($report->implementation_id);

            $provinceNames = 'All Provinces';
            $cityNames = 'All Cities';

            if (!$user->hasRole('admin')) {
                $centerIDs = DB::table('user_centers')
                    ->where('user_id', $userId)
                    ->pluck('child_development_center_id')
                    ->toArray();

                $psgcIds = DB::table('child_development_centers')
                    ->whereIn('id', $centerIDs)
                    ->pluck('psgc_id')
                    ->unique()
                    ->toArray();

                $provinces = DB::table('psgcs')
                    ->whereIn('psgc_id', $psgcIds)
                    ->distinct()
                    ->pluck('province_name');

                $cities = DB::table('psgcs')
                    ->whereIn('psgc_id', $psgcIds)
                    ->distinct()
                    ->pluck('city_name');

                $provinceNames = $provinces->implode(', ') ?: 'All Provinces';
                $cityNames = $cities->implode(', ') ?: 'All Cities';
            }

            // Get stored report data
            $reportData = DB::table('undernourished_upon_entry_report_data')
                ->where('report_id', $report->id)
                ->orderBy('center_name')
                ->get();

            // Create custom TCPDF instance
            $pdf = new class('L', 'mm', 'FOLIO', true, 'UTF-8', false) extends \TCPDF {
                public function Footer() {
                    $this->SetY(-15);
                    $this->SetFont('helvetica', '', 8);
                    $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
                }
            };

            $pdf->SetCreator('SFP System');
            $pdf->SetAuthor($user->full_name);
            $pdf->SetTitle('Summary of Undernourished Children Upon Entry');
            $pdf->SetSubject('Undernourished Children Upon Entry Report');

            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(true);

            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(true, 20);

            $pdf->SetFont('helvetica', '', 8);
            $pdf->AddPage();

            // Header
            $html = '<h3 style="text-align:center; font-size:11px; line-height:1.2;">
                Department of Social Welfare and Development, Field Office XI<br>
                Supplementary Feeding Program<br>
                ' . mb_strtoupper(htmlspecialchars($cycle->name, ENT_QUOTES, 'UTF-8')) . ' ( CY ' . htmlspecialchars($cycle->school_year_from) . ' )<br>
                <b>SUMMARY OF UNDERNOURISHED CHILDREN, ETHNICITY, 4Ps, DEWORMING &amp; VITAMIN A</b><br>
                <i>Upon Entry</i>
            </h3>';

            $html .= '<p style="font-size:9px;">Province: <u>' . htmlspecialchars($provinceNames) . '</u><br>
            City / Municipality: <u>' . htmlspecialchars($cityNames) . '</u></p>';

            // Table header
            $html .= '<table border="1" cellpadding="2" cellspacing="0" style="font-size:6px; border-collapse:collapse; width:100%;" align="center">
                <thead>
                    <tr style="background-color:#f0f0f0; font-weight:bold;">
                        <th rowspan="3" width="3%">No.</th>
                        <th rowspan="3" width="12%">Name of Child Development Center</th>
                        <th rowspan="3" width="10%">Name of Child Development Worker</th>
                        <th colspan="8" width="20%">Summary of Undernourished Children</th>
                        <th colspan="10" width="35%">Beneficiaries Profile</th>
                        <th colspan="4" width="20%">Deworming &amp; Vitamin A Record</th>
                    </tr>
                    <tr style="background-color:#f0f0f0; font-weight:bold;">
                        <th colspan="2">2 y/o</th>
                        <th colspan="2">3 y/o</th>
                        <th colspan="2">4 y/o</th>
                        <th colspan="2">5 y/o</th>
                        <th colspan="2">No. of Ethnic Children</th>
                        <th colspan="2">No. of 4Ps Children</th>
                        <th colspan="2">No. of PWD</th>
                        <th colspan="2">No. of Children with Lactose Intolerance</th>
                        <th colspan="2">No. of Children with Solo Parent</th>
                        <th colspan="2">No. of Dewormed Children</th>
                        <th colspan="2">No. of Children with Vit. A Supp.</th>
                    </tr>
                    <tr style="background-color:#f0f0f0; font-weight:bold;">
                        <th>M</th><th>F</th>
                        <th>M</th><th>F</th>
                        <th>M</th><th>F</th>
                        <th>M</th><th>F</th>
                        <th>M</th><th>F</th>
                        <th>M</th><th>F</th>
                        <th>M</th><th>F</th>
                        <th>M</th><th>F</th>
                        <th>M</th><th>F</th>
                        <th>M</th><th>F</th>
                        <th>M</th><th>F</th>
                    </tr>
                </thead>
                <tbody>';

            $counter = 1;
            $rowsOnCurrentPage = 0;
            $isFirstPage = true;
            $maxRowsFirstPage = 15;
            $maxRowsPerPage = 20;

            foreach ($reportData as $row) {
                $maxRows = $isFirstPage ? $maxRowsFirstPage : $maxRowsPerPage;
                if ($rowsOnCurrentPage >= $maxRows) {
                    $html .= '</tbody></table>';
                    $pdf->writeHTML($html, true, false, true, false, '');
                    $pdf->AddPage();

                    $html = '<table border="1" cellpadding="2" cellspacing="0" style="font-size:6px; border-collapse:collapse; width:100%;">
                        <thead>
                            <tr style="background-color:#f0f0f0; font-weight:bold;">
                                <th rowspan="3" width="3%">No.</th>
                                <th rowspan="3" width="12%">CDC</th>
                                <th rowspan="3" width="10%">CDW</th>
                                <th colspan="8" width="20%">Summary of Undernourished Children</th>
                                <th colspan="10" width="35%">Beneficiaries Profile</th>
                                <th colspan="4" width="20%">Deworming &amp; Vitamin A</th>
                            </tr>
                            <tr style="background-color:#f0f0f0; font-weight:bold;">
                                <th colspan="2">2 y/o</th>
                                <th colspan="2">3 y/o</th>
                                <th colspan="2">4 y/o</th>
                                <th colspan="2">5 y/o</th>
                                <th colspan="2">Ethnic</th>
                                <th colspan="2">4Ps</th>
                                <th colspan="2">PWD</th>
                                <th colspan="2">Lactose</th>
                                <th colspan="2">Solo Parent</th>
                                <th colspan="2">Dewormed</th>
                                <th colspan="2">Vit. A</th>
                            </tr>
                            <tr style="background-color:#f0f0f0; font-weight:bold;">
                                <th>M</th><th>F</th>
                                <th>M</th><th>F</th>
                                <th>M</th><th>F</th>
                                <th>M</th><th>F</th>
                                <th>M</th><th>F</th>
                                <th>M</th><th>F</th>
                                <th>M</th><th>F</th>
                                <th>M</th><th>F</th>
                                <th>M</th><th>F</th>
                                <th>M</th><th>F</th>
                                <th>M</th><th>F</th>
                            </tr>
                        </thead>
                        <tbody>';

                    $rowsOnCurrentPage = 0;
                    $isFirstPage = false;
                }

                $html .= '<tr>';
                $html .= '<td width="3%">' . $counter++ . '</td>';
                $html .= '<td width="12%">' . mb_strtoupper(htmlspecialchars($row->center_name, ENT_QUOTES, 'UTF-8')) . '</td>';
                $html .= '<td width="10%">' . mb_strtoupper(htmlspecialchars($row->worker_name ?? 'No Worker Assigned', ENT_QUOTES, 'UTF-8')) . '</td>';
                $html .= '<td>' . $row->age_2_male . '</td>';
                $html .= '<td>' . $row->age_2_female . '</td>';
                $html .= '<td>' . $row->age_3_male . '</td>';
                $html .= '<td>' . $row->age_3_female . '</td>';
                $html .= '<td>' . $row->age_4_male . '</td>';
                $html .= '<td>' . $row->age_4_female . '</td>';
                $html .= '<td>' . $row->age_5_male . '</td>';
                $html .= '<td>' . $row->age_5_female . '</td>';
                $html .= '<td>' . $row->indigenous_male . '</td>';
                $html .= '<td>' . $row->indigenous_female . '</td>';
                $html .= '<td>' . $row->pantawid_male . '</td>';
                $html .= '<td>' . $row->pantawid_female . '</td>';
                $html .= '<td>' . $row->pwd_male . '</td>';
                $html .= '<td>' . $row->pwd_female . '</td>';
                $html .= '<td>' . $row->lactose_intolerant_male . '</td>';
                $html .= '<td>' . $row->lactose_intolerant_female . '</td>';
                $html .= '<td>' . $row->solo_parent_male . '</td>';
                $html .= '<td>' . $row->solo_parent_female . '</td>';
                $html .= '<td>' . $row->dewormed_male . '</td>';
                $html .= '<td>' . $row->dewormed_female . '</td>';
                $html .= '<td>' . $row->vitamin_a_male . '</td>';
                $html .= '<td>' . $row->vitamin_a_female . '</td>';
                $html .= '</tr>';

                $rowsOnCurrentPage++;
            }

            $html .= '</tbody></table>';

            // Footer section
            $html .= '<table border="0" cellpadding="5" style="font-size:9px; margin-top:5px;">
                <tr>
                    <td width="50%">
                        <p>Prepared by:</p><br><br>
                        <p><u>' . ($user->hasRole('lgu focal') ? mb_strtoupper(htmlspecialchars($user->full_name, ENT_QUOTES, 'UTF-8')) : str_repeat('_', 40)) . '</u></p>
                        <p>SFP Focal Person</p>
                    </td>
                    <td width="50%">
                        <p style="margin:0;">Noted by:</p><br><br>
                        <p style="margin:0;"><u>' . str_repeat('_', 40) . '</u></p>
                        <p style="margin:0;">C/MSWDO/District Head</p>
                    </td>
                </tr>
            </table>';

            $pdf->writeHTML($html, true, false, true, false, '');

            $folder = public_path("generated_reports/{$userId}");
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $fileName = "Undernourished_Upon_Entry_" . now()->format('m_d_Y_H_m_s') . ".pdf";
            $filePath = $folder . '/' . $fileName;

            $pdf->Output($filePath, 'F');

            DB::table('undernourished_upon_entry_reports')
                ->where('id', $report->id)
                ->update([
                    'status' => 'completed',
                    'file_path' => "generated_reports/{$userId}/{$fileName}",
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::table('undernourished_upon_entry_report_data')
                ->where('report_id', $report->id)
                ->delete();

            return $filePath;

        } catch (\Exception $e) {
            DB::table('undernourished_upon_entry_reports')
                ->where('id', $report->id)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'updated_at' => now(),
                ]);

            throw $e;
        }
    }

    private static function fetchAndStoreData($reportId, $cycleId, $centerIDs, &$totalRecords)
    {
        // Get centers with their workers
        $centersQuery = DB::table('child_development_centers as cdc')
            ->select([
                'cdc.id as center_id',
                'cdc.center_name',
            ]);

        if ($centerIDs) {
            $centersQuery->whereIn('cdc.id', $centerIDs);
        }

        $centers = $centersQuery->orderBy('cdc.center_name')->get();

        foreach ($centers as $center) {
            // Get worker name for this center
            $worker = DB::table('user_centers as uc')
                ->join('users as u', 'uc.user_id', '=', 'u.id')
                ->join('model_has_roles as mhr', 'u.id', '=', 'mhr.model_id')
                ->join('roles as r', 'mhr.role_id', '=', 'r.id')
                ->where('uc.child_development_center_id', $center->center_id)
                ->where('r.name', 'child development worker')
                ->select(DB::raw("CONCAT(u.firstname, ' ', COALESCE(u.middlename, ''), ' ', u.lastname, ' ', COALESCE(u.extension_name, '')) as full_name"))
                ->first();

            // Get the first (upon entry) nutritional status IDs for undernourished children at this center
            $childIds = DB::table('child_records as cr')
                ->join('nutritional_statuses as ns', function ($join) use ($cycleId) {
                    $join->on('cr.child_id', '=', 'ns.child_id')
                        ->where('ns.implementation_id', '=', $cycleId)
                        ->whereIn('ns.age_in_years', [2, 3, 4, 5])
                        ->where('ns.is_undernourish', '=', 1);
                })
                ->where('cr.implementation_id', $cycleId)
                ->where('cr.funded', 1)
                ->where('cr.action_type', 'active')
                ->where('cr.child_development_center_id', $center->center_id)
                ->distinct()
                ->pluck('cr.child_id');

            if ($childIds->isEmpty()) {
                continue;
            }

            // Get earliest nutritional status per child (upon entry)
            $entryNsIds = DB::table('nutritional_statuses')
                ->select(DB::raw('MIN(id) as id'), 'child_id')
                ->whereIn('child_id', $childIds)
                ->where('implementation_id', $cycleId)
                ->where('is_undernourish', 1)
                ->whereIn('age_in_years', [2, 3, 4, 5])
                ->groupBy('child_id')
                ->pluck('id');

            if ($entryNsIds->isEmpty()) {
                continue;
            }

            // Get children with their entry nutritional status
            $children = DB::table('children as c')
                ->join('nutritional_statuses as ns', 'ns.child_id', '=', 'c.id')
                ->whereIn('ns.id', $entryNsIds)
                ->select([
                    'c.id',
                    'c.sex_id',
                    'c.is_indigenous_people',
                    'c.pantawid_details',
                    'c.person_with_disability_details',
                    'c.is_lactose_intolerant',
                    'c.is_child_of_soloparent',
                    'ns.age_in_years',
                    'ns.deworming_date',
                    'ns.vitamin_a_date',
                ])
                ->get();

            $counts = [
                'age_2_male' => 0, 'age_2_female' => 0,
                'age_3_male' => 0, 'age_3_female' => 0,
                'age_4_male' => 0, 'age_4_female' => 0,
                'age_5_male' => 0, 'age_5_female' => 0,
                'indigenous_male' => 0, 'indigenous_female' => 0,
                'pantawid_male' => 0, 'pantawid_female' => 0,
                'pwd_male' => 0, 'pwd_female' => 0,
                'lactose_intolerant_male' => 0, 'lactose_intolerant_female' => 0,
                'solo_parent_male' => 0, 'solo_parent_female' => 0,
                'dewormed_male' => 0, 'dewormed_female' => 0,
                'vitamin_a_male' => 0, 'vitamin_a_female' => 0,
            ];

            foreach ($children as $child) {
                $sexKey = $child->sex_id == 1 ? 'male' : 'female';

                // Age bracket
                if (in_array($child->age_in_years, [2, 3, 4, 5])) {
                    $counts["age_{$child->age_in_years}_{$sexKey}"]++;
                }

                // Beneficiary profile
                if ($child->is_indigenous_people) {
                    $counts["indigenous_{$sexKey}"]++;
                }
                if ($child->pantawid_details != null) {
                    $counts["pantawid_{$sexKey}"]++;
                }
                if ($child->person_with_disability_details != null) {
                    $counts["pwd_{$sexKey}"]++;
                }
                if ($child->is_lactose_intolerant) {
                    $counts["lactose_intolerant_{$sexKey}"]++;
                }
                if ($child->is_child_of_soloparent) {
                    $counts["solo_parent_{$sexKey}"]++;
                }

                // Deworming & Vitamin A
                if ($child->deworming_date != null) {
                    $counts["dewormed_{$sexKey}"]++;
                }
                if ($child->vitamin_a_date != null) {
                    $counts["vitamin_a_{$sexKey}"]++;
                }
            }

            DB::table('undernourished_upon_entry_report_data')->insert(array_merge([
                'report_id' => $reportId,
                'center_id' => $center->center_id,
                'center_name' => $center->center_name,
                'worker_name' => $worker->full_name ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ], $counts));

            $totalRecords++;
        }
    }
}
