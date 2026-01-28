<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DisabilityReportGeneration extends Controller
{
    public static function generateDisabilityReport($userId, $cdcId = 0)
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
        $reportId = DB::table('disability_reports')->insertGetId([
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
            DB::table('disability_reports')
                ->where('id', $reportId)
                ->update([
                    'status' => 'pending',
                    'total_records' => $totalRecords,
                    'updated_at' => now(),
                ]);

            return $reportId;

        } catch (\Exception $e) {
            DB::table('disability_reports')
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

        // Get pending report
        $report = DB::table('disability_reports')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$report) {
            throw new \Exception('No pending report found for this user.');
        }

        // Update status to generating
        DB::table('disability_reports')
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

            // Create custom TCPDF instance with pagination footer
            $pdf = new class('L', 'mm', 'FOLIO', true, 'UTF-8', false) extends \TCPDF {
                public function Footer() {
                    // Position at 15 mm from bottom
                    $this->SetY(-15);
                    // Set font
                    $this->SetFont('helvetica', '', 8);
                    // Page number
                    $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
                }
            };

            // Set document information
            $pdf->SetCreator('SFP System');
            $pdf->SetAuthor($user->full_name);
            $pdf->SetTitle('List of PWD Children');
            $pdf->SetSubject('PWD Children Report');

            // Remove default header but keep footer for pagination
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(true);

            // Set margins
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(true, 20);

            // Set font
            $pdf->SetFont('helvetica', '', 8);

            // Add a page
            $pdf->AddPage();

            // Header
            $html = '<h3 style="text-align:center; font-size:11px; line-height:1.2;">
                Department of Social Welfare and Development, Field Office XI<br>
                Supplementary Feeding Program<br>
                ' . mb_strtoupper(htmlspecialchars($cycle->name, ENT_QUOTES, 'UTF-8')) . ' ( CY ' . htmlspecialchars($cycle->school_year_from) . ' )<br>
                <b>LIST OF PWD CHILDREN</b>
            </h3>';

            $html .= '<p style="font-size:9px;">Province: <u>' . htmlspecialchars($provinceNames) . '</u><br>
            City / Municipality: <u>' . htmlspecialchars($cityNames) . '</u></p>';

            // Build complete table HTML at once
            $html .= '<table border="1" cellpadding="2" cellspacing="0" style="font-size:7px; border-collapse:collapse; width:100%;" align="center">
                <thead>
                    <tr style="background-color:#f0f0f0; font-weight:bold;">
                        <th width="5%">No.</th>
                        <th width="25%">Name of Child</th>
                        <th width="25%">Name of Child Development Center</th>
                        <th width="5%">Sex</th>
                        <th width="15%">Date of Birth</th>
                        <th width="25%">Type of Disability</th>
                    </tr>
                </thead>
                <tbody>';

            // Process data in chunks to manage memory
            $counter = 1;
            $rowsOnCurrentPage = 0;
            $isFirstPage = true;
            $maxRowsFirstPage = 20;
            $maxRowsPerPage = 25;

            DB::table('disability_report_data')
                ->where('disability_report_id', $report->id)
                ->orderBy('id')
                ->chunk(100, function ($children) use (&$html, &$counter, &$rowsOnCurrentPage, &$isFirstPage, &$pdf, $maxRowsFirstPage, $maxRowsPerPage) {
                    foreach ($children as $child) {
                        // Check if we need to add a new page
                        $maxRows = $isFirstPage ? $maxRowsFirstPage : $maxRowsPerPage;
                        if ($rowsOnCurrentPage >= $maxRows) {
                            // Close current table
                            $html .= '</tbody></table>';

                            // Write current page HTML
                            $pdf->writeHTML($html, true, false, true, false, '');

                            // Add new page
                            $pdf->AddPage();

                            // Reset HTML and start new table with header
                            $html = '<table border="1" cellpadding="2" cellspacing="0" style="font-size:7px; border-collapse:collapse; width:100%;">
                                <thead>
                                    <tr style="background-color:#f0f0f0; font-weight:bold; text-align:center;">
                                        <th style="width:5%; text-align:center;">No.</th>
                                        <th style="width:25%; text-align:center;">Name of Child</th>
                                        <th style="width:25%; text-align:center;">Name of CDC</th>
                                        <th style="width:5%; text-align:center;">Sex</th>
                                        <th style="width:15%; text-align:center;">Date of Birth</th>
                                        <th style="width:25%; text-align:center;">Type of Disability</th>
                                    </tr>
                                </thead>
                                <tbody>';

                            $rowsOnCurrentPage = 0;
                            $isFirstPage = false;
                        }

                        $fullName = strtoupper(trim(
                            $child->lastname . ', ' . $child->firstname . ' ' .
                            ($child->middlename ? strtoupper(substr($child->middlename, 0, 1)) . '.' : '') . ' ' .
                            ($child->extension_name ?? '')
                        ));

                        $html .= '<tr>';
                        $html .= '<td width="5%">' . $counter++ . '</td>';
                        $html .= '<td width="25%">' . mb_strtoupper(htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8')) . '</td>';
                        $html .= '<td width="25%">' . mb_strtoupper(htmlspecialchars($child->center_name ?? 'N/A', ENT_QUOTES, 'UTF-8')) . '</td>';
                        $html .= '<td width="5%">' . ($child->sex_name == 'Male' ? 'M' : 'F') . '</td>';
                        $html .= '<td width="15%">' . ($child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->format('m-d-Y') : '') . '</td>';
                        $html .= '<td width="25%">' . mb_strtoupper(htmlspecialchars($child->person_with_disability_details ?? '', ENT_QUOTES, 'UTF-8')) . '</td>';
                        $html .= '</tr>';

                        $rowsOnCurrentPage++;
                    }
                });

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

            // Write HTML to PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            $folder = public_path("generated_reports/{$userId}");
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $fileName = "Persons_with_Disability_" . now()->format('m_d_Y_H_m_s') . ".pdf";
            $filePath = $folder . '/' . $fileName;

            // Save PDF
            $pdf->Output($filePath, 'F');

            // Update report with file path and completed status
            DB::table('disability_reports')
                ->where('id', $report->id)
                ->update([
                    'status' => 'completed',
                    'file_path' => "generated_reports/{$userId}/{$fileName}",
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            // Delete report data entries after successful PDF generation
            DB::table('disability_report_data')
                ->where('disability_report_id', $report->id)
                ->delete();

            return $filePath;

        } catch (\Exception $e) {
            DB::table('disability_reports')
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
                'c.person_with_disability_details',
                's.name as sex_name',
                'cdc.center_name',
            ])
            ->join('sexes as s', 'c.sex_id', '=', 's.id')
            ->join('child_records as cr', 'c.id', '=', 'cr.child_id')
            ->join('child_development_centers as cdc', 'cr.child_development_center_id', '=', 'cdc.id')
            ->whereNotNull('c.person_with_disability_details')
            ->where('c.person_with_disability_details', '!=', '')
            ->whereExists(function ($query) use ($cycleId) {
                $query->select(DB::raw(1))
                    ->from('nutritional_statuses as ns')
                    ->whereColumn('ns.child_id', 'c.id')
                    ->where('ns.implementation_id', $cycleId);
            })
            ->where('cr.implementation_id', $cycleId)
            ->where('cr.funded', 1)
            ->where('cr.action_type', 'active');

        if ($centerIDs) {
            $query->whereIn('cr.child_development_center_id', $centerIDs);
        }

        $query->distinct()->orderBy('c.id')->chunk(250, function ($children) use ($reportId, &$totalRecords) {
            $dataToInsert = [];

            foreach ($children as $child) {
                $dataToInsert[] = [
                    'disability_report_id' => $reportId,
                    'child_id' => $child->id,
                    'lastname' => $child->lastname,
                    'firstname' => $child->firstname,
                    'middlename' => $child->middlename,
                    'extension_name' => $child->extension_name,
                    'date_of_birth' => $child->date_of_birth,
                    'sex_name' => $child->sex_name,
                    'center_name' => $child->center_name,
                    'person_with_disability_details' => $child->person_with_disability_details,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $totalRecords++;
            }

            if (!empty($dataToInsert)) {
                DB::table('disability_report_data')->insert($dataToInsert);
            }
        });
    }
}
