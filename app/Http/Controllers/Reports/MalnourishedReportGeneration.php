<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class MalnourishedReportGeneration extends Controller
{
    public static function generateMalnourishedReport($userId)
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
        if (!$user->hasRole('admin')) {
            $centerIDs = DB::table('user_centers')
                ->where('user_id', $userId)
                ->pluck('child_development_center_id')
                ->toArray();
        }

        // Create report record
        $reportId = DB::table('malnourished_reports')->insertGetId([
            'user_id' => $userId,
            'implementation_id' => $cycle->id,
            'status' => 'generating',
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $totalRecords = 0;

            // Fetch and store children data in chunks
            self::fetchAndStoreChildrenData($reportId, $cycle->id, $centerIDs, $totalRecords);

            // Update report status
            DB::table('malnourished_reports')
                ->where('id', $reportId)
                ->update([
                    'status' => 'pending',
                    'total_records' => $totalRecords,
                    'updated_at' => now(),
                ]);

            return $reportId;

        } catch (\Exception $e) {
            DB::table('malnourished_reports')
                ->where('id', $reportId)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'updated_at' => now(),
                ]);

            throw $e;
        }
    }

    public static function generatePDF_dompdf($userId)
    {
        ini_set('memory_limit', '4096M');
        set_time_limit(1800);

        // Get pending report
        $report = DB::table('malnourished_reports')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$report) {
            throw new \Exception('No pending report found for this user.');
        }

        // Update status to generating
        DB::table('malnourished_reports')
            ->where('id', $report->id)
            ->update([
                'status' => 'generating',
                'updated_at' => now(),
            ]);

        try {
            $user = User::find($userId);
            $cycle = DB::table('implementations')->find($report->implementation_id);

            // Get province and city info
            $province = null;
            $city = null;

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

                $province = DB::table('psgcs')
                    ->whereIn('psgc_id', $psgcIds)
                    ->distinct()
                    ->pluck('province_name');

                $city = DB::table('psgcs')
                    ->whereIn('psgc_id', $psgcIds)
                    ->distinct()
                    ->pluck('city_name');
            }

            // Get report data and convert to simple arrays to reduce memory
            $isFunded = DB::table('malnourished_report_data')
                ->where('malnourished_report_id', $report->id)
                ->get()
                ->map(function ($item) {
                    // Convert to array and pre-process data to reduce blade processing
                    return (object) [
                        'full_name' => trim(
                            $item->lastname . ', ' . $item->firstname . ' ' .
                            ($item->middlename ? strtoupper(substr($item->middlename, 0, 1)) . '.' : '') . ' ' .
                            ($item->extension_name ?? '')
                        ),
                        'center_name' => $item->center_name ?? 'N/A',
                        'sex_initial' => $item->sex_name == 'Male' ? 'M' : 'F',
                        'date_of_birth' => $item->date_of_birth,
                        'entry_weighing_date' => $item->entry_weighing_date,
                        'entry_weight' => $item->entry_weight,
                        'entry_height' => $item->entry_height,
                        'entry_age_months' => $item->entry_age_months,
                        'entry_age_years' => $item->entry_age_years,
                        'entry_weight_for_age' => $item->entry_weight_for_age,
                        'entry_weight_for_height' => $item->entry_weight_for_height,
                        'entry_height_for_age' => $item->entry_height_for_age,
                        'exit_weighing_date' => $item->exit_weighing_date,
                        'exit_weight' => $item->exit_weight,
                        'exit_height' => $item->exit_height,
                        'exit_age_months' => $item->exit_age_months,
                        'exit_age_years' => $item->exit_age_years,
                        'exit_weight_for_age' => $item->exit_weight_for_age,
                        'exit_weight_for_height' => $item->exit_weight_for_height,
                        'exit_height_for_age' => $item->exit_height_for_age,
                    ];
                });

            // Free up memory
            gc_collect_cycles();

            // Generate PDF with optimized settings
            $pdf = Pdf::loadView('reports.print.malnourished_optimized', compact('user', 'cycle', 'isFunded', 'province', 'city'))
                ->setPaper('folio', 'landscape')
                ->setOptions([
                    'margin-top' => 0.5,
                    'margin-right' => 1,
                    'margin-bottom' => 50,
                    'margin-left' => 1,
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isPhpEnabled' => true,
                    'chroot' => public_path(),
                    'enable_font_subsetting' => true,
                    'debugKeepTemp' => false,
                ]);

            $folder = public_path("generated_reports/{$userId}");
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $fileName = "Malnourished_Children_" . now()->format('m_d_Y_H_m_s') . ".pdf";
            $filePath = $folder . '/' . $fileName;

            $pdf->save($filePath);

            // Update report with file path and completed status
            DB::table('malnourished_reports')
                ->where('id', $report->id)
                ->update([
                    'status' => 'completed',
                    'file_path' => "generated_reports/{$userId}/{$fileName}",
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            // Delete report data entries after successful PDF generation
            DB::table('malnourished_report_data')
                ->where('malnourished_report_id', $report->id)
                ->delete();

            return $filePath;

        } catch (\Exception $e) {
            DB::table('malnourished_reports')
                ->where('id', $report->id)
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

        // Get pending report
        $report = DB::table('malnourished_reports')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$report) {
            throw new \Exception('No pending report found for this user.');
        }

        // Update status to generating
        DB::table('malnourished_reports')
            ->where('id', $report->id)
            ->update([
                'status' => 'generating',
                'updated_at' => now(),
            ]);

        try {
            $user = User::find($userId);
            $cycle = DB::table('implementations')->find($report->implementation_id);

            // Get province and city info
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

            // Create TCPDF instance
            $pdf = new \TCPDF('L', 'mm', 'FOLIO', true, 'UTF-8', false);

            // Set document information
            $pdf->SetCreator('SFP System');
            $pdf->SetAuthor($user->full_name);
            $pdf->SetTitle('List of Malnourished Children');
            $pdf->SetSubject('Malnourished Children Report');

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margins
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(true, 15);

            // Set font
            $pdf->SetFont('helvetica', '', 8);

            // Add a page
            $pdf->AddPage();

            // Header
            $html = '<h3 style="text-align:center; font-size:11px; line-height:1.2;">
                Department of Social Welfare and Development, Field Office XI<br>
                Supplementary Feeding Program<br>
                ' . mb_strtoupper(htmlspecialchars($cycle->name, ENT_QUOTES, 'UTF-8')) . ' ( CY ' . htmlspecialchars($cycle->school_year_from) . ' )<br>
                <b>LIST OF MALNOURISHED CHILDREN</b>
            </h3>';

            $html .= '<p style="font-size:9px;">Province: <u>' . htmlspecialchars($provinceNames) . '</u><br>
            City / Municipality: <u>' . htmlspecialchars($cityNames) . '</u></p>';

            // Build complete table HTML at once
            $html .= '<table border="1" cellpadding="2" cellspacing="0" style="font-size:7px; border-collapse:collapse; width:100%;" align="center">
                <thead>
                    <tr style="background-color:#f0f0f0; font-weight:bold;">
                        <th rowspan="2" width="2.5%;">No.</th>
                        <th rowspan="2" width="18%;">Name of Child</th>
                        <th rowspan="2" width="15%;">Name of Child Development Center</th>
                        <th rowspan="2" width="2.5%;">Sex</th>
                        <th rowspan="2" width="5%;">Date of Birth</th>
                        <th rowspan="2" width="5%;">Actual Date of Weighing</th>
                        <th rowspan="2" width="3.5%;">Weight in kg</th>
                        <th rowspan="2" width="3.5%;">Height in cm</th>
                        <th colspan="2" width="6%;">Age in Month/Year</th>
                        <th colspan="3" width="10.5%;">NS Upon Entry</th>
                        <th rowspan="2" width="5%;">Actual Date of Weighing</th>
                        <th rowspan="2" width="3.5%;">Weight in kg</th>
                        <th rowspan="2" width="3.5%;">Height in cm</th>
                        <th colspan="2" width="6%;">Age in Month/Year</th>
                        <th colspan="3" width="10.5%;">NS After 120 Feedings</th>
                    </tr>
                    <tr style="background-color:#f0f0f0; font-weight:bold;">
                        <th width="3%;">Month</th>
                        <th width="3%;">Year</th>
                        <th width="3.5%;">Weight for Age</th>
                        <th width="3.5%;">Weight for Height</th>
                        <th width="3.5%;">Height for Age</th>
                        <th width="3%;">Month</th>
                        <th width="3%;">Year</th>
                        <th width="3.5%;">Weight for Age</th>
                        <th width="3.5%;">Weight for Height</th>
                        <th width="3.5%;">Height for Age</th>
                    </tr>
                </thead>
                <tbody>';

            // Process data in chunks to manage memory
            $counter = 1;
            DB::table('malnourished_report_data')
                ->where('malnourished_report_id', $report->id)
                ->orderBy('id')
                ->chunk(100, function ($children) use (&$html, &$counter) {
                    foreach ($children as $child) {
                        $fullName = trim(
                            $child->lastname . ', ' . $child->firstname . ' ' .
                            ($child->middlename ? strtoupper(substr($child->middlename, 0, 1)) . '.' : '') . ' ' .
                            ($child->extension_name ?? '')
                        );

                        $html .= '<tr>';
                        $html .= '<td width="2.5%">' . $counter++ . '</td>';
                        $html .= '<td width="18%">' . mb_strtoupper(htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8')) . '</td>';
                        $html .= '<td width="15%">' . mb_strtoupper(htmlspecialchars($child->center_name ?? 'N/A', ENT_QUOTES, 'UTF-8')) . '</td>';
                        $html .= '<td width="2.5%">' . ($child->sex_name == 'Male' ? 'M' : 'F') . '</td>';
                        $html .= '<td width="5%">' . ($child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->format('m-d-Y') : '') . '</td>';
                        $html .= '<td width="5%">' . ($child->entry_weighing_date ? \Carbon\Carbon::parse($child->entry_weighing_date)->format('m-d-Y') : '') . '</td>';
                        $html .= '<td width="3.5%">' . ($child->entry_weight ? number_format($child->entry_weight, 1) : '') . '</td>';
                        $html .= '<td width="3.5%">' . ($child->entry_height ? number_format($child->entry_height, 1) : '') . '</td>';
                        $html .= '<td width="3%">' . ($child->entry_age_months ?? '') . '</td>';
                        $html .= '<td width="3%">' . ($child->entry_age_years ?? '') . '</td>';
                        $html .= '<td width="3.5%">' . ($child->entry_weight_for_age ?? '') . '</td>';
                        $html .= '<td width="3.5%">' . ($child->entry_weight_for_height ?? '') . '</td>';
                        $html .= '<td width="3.5%">' . ($child->entry_height_for_age ?? '') . '</td>';
                        $html .= '<td width="5%">' . ($child->exit_weighing_date ? \Carbon\Carbon::parse($child->exit_weighing_date)->format('m-d-Y') : '') . '</td>';
                        $html .= '<td width="3.5%">' . ($child->exit_weight ? number_format($child->exit_weight, 1) : '') . '</td>';
                        $html .= '<td width="3.5%">' . ($child->exit_height ? number_format($child->exit_height, 1) : '') . '</td>';
                        $html .= '<td width="3%">' . ($child->exit_age_months ?? '') . '</td>';
                        $html .= '<td width="3%">' . ($child->exit_age_years ?? '') . '</td>';
                        $html .= '<td width="3.5%">' . ($child->exit_weight_for_age ?? '') . '</td>';
                        $html .= '<td width="3.5%">' . ($child->exit_weight_for_height ?? '') . '</td>';
                        $html .= '<td width="3.5%">' . ($child->exit_height_for_age ?? '') . '</td>';
                        $html .= '</tr>';
                    }
                });

            $html .= '</tbody></table>';

            // Footer section
            $html .= '<br><br><table border="0" cellpadding="5" style="font-size:9px;">
                <tr>
                    <td width="50%">
                        <p>Noted by:</p><br><br>
                        <p><u>' . ($user->hasRole('lgu focal') ? mb_strtoupper(htmlspecialchars($user->full_name, ENT_QUOTES, 'UTF-8')) : str_repeat('_', 40)) . '</u></p>
                        <p>SFP Focal Person</p>
                    </td>
                    <td width="50%">
                        <p>Approved by:</p><br><br>
                        <p><u>' . str_repeat('_', 40) . '</u></p>
                        <p>C/MSWDO/District Head</p>
                    </td>
                </tr>
            </table>';

            // Write HTML to PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            $folder = public_path("generated_reports/{$userId}");
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $fileName = "Malnourished_Children_" . now()->format('m_d_Y_H_m_s') . ".pdf";
            $filePath = $folder . '/' . $fileName;

            // Save PDF
            $pdf->Output($filePath, 'F');

            // Update report with file path and completed status
            DB::table('malnourished_reports')
                ->where('id', $report->id)
                ->update([
                    'status' => 'completed',
                    'file_path' => "generated_reports/{$userId}/{$fileName}",
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            // Delete report data entries after successful PDF generation
            DB::table('malnourished_report_data')
                ->where('malnourished_report_id', $report->id)
                ->delete();

            return $filePath;

        } catch (\Exception $e) {
            DB::table('malnourished_reports')
                ->where('id', $report->id)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'updated_at' => now(),
                ]);

            throw $e;
        }
    }

    private static function fetchAndStoreChildrenData($reportId, $cycleId, $centerIDs, &$totalRecords)
    {
        $query = DB::table('children as c')
            ->select([
                'c.id',
                'c.lastname',
                'c.firstname',
                'c.middlename',
                'c.extension_name',
                'c.date_of_birth',
                's.name as sex_name',
                'cdc.center_name',
                DB::raw('(SELECT actual_weighing_date FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1) as entry_weighing_date'),
                DB::raw('(SELECT weight FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1) as entry_weight'),
                DB::raw('(SELECT height FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1) as entry_height'),
                DB::raw('(SELECT age_in_months FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1) as entry_age_months'),
                DB::raw('(SELECT age_in_years FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1) as entry_age_years'),
                DB::raw('(SELECT weight_for_age FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1) as entry_weight_for_age'),
                DB::raw('(SELECT weight_for_height FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1) as entry_weight_for_height'),
                DB::raw('(SELECT height_for_age FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1) as entry_height_for_age'),
                DB::raw('(SELECT actual_weighing_date FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1 OFFSET 1) as exit_weighing_date'),
                DB::raw('(SELECT weight FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1 OFFSET 1) as exit_weight'),
                DB::raw('(SELECT height FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1 OFFSET 1) as exit_height'),
                DB::raw('(SELECT age_in_months FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1 OFFSET 1) as exit_age_months'),
                DB::raw('(SELECT age_in_years FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1 OFFSET 1) as exit_age_years'),
                DB::raw('(SELECT weight_for_age FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1 OFFSET 1) as exit_weight_for_age'),
                DB::raw('(SELECT weight_for_height FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1 OFFSET 1) as exit_weight_for_height'),
                DB::raw('(SELECT height_for_age FROM nutritional_statuses WHERE child_id = c.id AND implementation_id = ? AND is_malnourish = 1 ORDER BY actual_weighing_date ASC LIMIT 1 OFFSET 1) as exit_height_for_age'),
            ])
            ->join('sexes as s', 'c.sex_id', '=', 's.id')
            ->join('child_records as cr', 'c.id', '=', 'cr.child_id')
            ->join('child_development_centers as cdc', 'cr.child_development_center_id', '=', 'cdc.id')
            ->whereExists(function ($query) use ($cycleId) {
                $query->select(DB::raw(1))
                    ->from('nutritional_statuses as ns')
                    ->whereColumn('ns.child_id', 'c.id')
                    ->where('ns.implementation_id', $cycleId)
                    ->where('ns.is_malnourish', 1);
            })
            ->where('cr.implementation_id', $cycleId)
            ->where('cr.funded', 1)
            ->where('cr.action_type', 'active');

        if ($centerIDs) {
            $query->whereIn('cr.child_development_center_id', $centerIDs);
        }

        $bindings = array_fill(0, 16, $cycleId);
        $query->addBinding($bindings, 'select');

        $query->distinct()->orderBy('c.id')->chunk(250, function ($children) use ($reportId, &$totalRecords) {
            $dataToInsert = [];

            foreach ($children as $child) {
                $dataToInsert[] = [
                    'malnourished_report_id' => $reportId,
                    'child_id' => $child->id,
                    'lastname' => $child->lastname,
                    'firstname' => $child->firstname,
                    'middlename' => $child->middlename,
                    'extension_name' => $child->extension_name,
                    'date_of_birth' => $child->date_of_birth,
                    'sex_name' => $child->sex_name,
                    'center_name' => $child->center_name,
                    'entry_weighing_date' => $child->entry_weighing_date,
                    'entry_weight' => $child->entry_weight,
                    'entry_height' => $child->entry_height,
                    'entry_age_months' => $child->entry_age_months,
                    'entry_age_years' => $child->entry_age_years,
                    'entry_weight_for_age' => $child->entry_weight_for_age,
                    'entry_weight_for_height' => $child->entry_weight_for_height,
                    'entry_height_for_age' => $child->entry_height_for_age,
                    'exit_weighing_date' => $child->exit_weighing_date,
                    'exit_weight' => $child->exit_weight,
                    'exit_height' => $child->exit_height,
                    'exit_age_months' => $child->exit_age_months,
                    'exit_age_years' => $child->exit_age_years,
                    'exit_weight_for_age' => $child->exit_weight_for_age,
                    'exit_weight_for_height' => $child->exit_weight_for_height,
                    'exit_height_for_age' => $child->exit_height_for_age,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $totalRecords++;
            }

            if (!empty($dataToInsert)) {
                DB::table('malnourished_report_data')->insert($dataToInsert);
            }
        });
    }
}
