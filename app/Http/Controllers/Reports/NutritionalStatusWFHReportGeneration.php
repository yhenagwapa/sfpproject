<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NutritionalStatusWFHReportGeneration extends Controller
{
    private static $categories = ['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese'];
    private static $categoryKeys = ['normal', 'wasted', 'severely_wasted', 'overweight', 'obese'];
    private static $ages = [2, 3, 4, 5];
    private static $sexLabels = ['M', 'F'];

    public static function generate($userId, $cdcId = 0)
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
        $reportId = DB::table('ns_wfh_reports')->insertGetId([
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

            DB::table('ns_wfh_reports')
                ->where('id', $reportId)
                ->update([
                    'status' => 'pending',
                    'total_records' => $totalRecords,
                    'updated_at' => now(),
                ]);

            return $reportId;

        } catch (\Exception $e) {
            DB::table('ns_wfh_reports')
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

        $report = DB::table('ns_wfh_reports')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$report) {
            throw new \Exception('No pending NS Weight-for-Height report found for this user.');
        }

        DB::table('ns_wfh_reports')
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
            $reportData = DB::table('ns_wfh_report_data')
                ->where('report_id', $report->id)
                ->orderBy('center_name')
                ->get();

            // Create custom TCPDF instance
            $pdf = new class('L', 'mm', 'FOLIO', true, 'UTF-8', false) extends \TCPDF {
                public function Footer() {
                    $this->SetY(-15);
                    $this->SetFont('helvetica', '', 7);
                    $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
                }
            };

            $pdf->SetCreator('SFP System');
            $pdf->SetAuthor($user->full_name);
            $pdf->SetTitle('Consolidated Nutritional Status Report - Weight for Height');
            $pdf->SetSubject('NS Weight-for-Height Report');

            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(true);

            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(true, 20);

            $pdf->SetFont('helvetica', '', 8);
            $pdf->AddPage();

            $categories = self::$categories;
            $categoryKeys = self::$categoryKeys;
            $ages = self::$ages;
            $sexLabels = self::$sexLabels;

            // Header
            $html = '<h3 style="text-align:center; font-size:11px; line-height:1.2;">
                Department of Social Welfare and Development, Field Office XI<br>
                Supplementary Feeding Program<br>
                ' . mb_strtoupper(htmlspecialchars($cycle->name, ENT_QUOTES, 'UTF-8')) . ' ( CY ' . htmlspecialchars($cycle->school_year_from) . ' )<br>
                <b>CONSOLIDATED NUTRITIONAL STATUS REPORT</b><br>
                <i>(Weight-for-Height)<br>Upon Entry</i>
            </h3>';

            $html .= '<p style="font-size:9px;">Province: <u>' . htmlspecialchars($provinceNames) . '</u><br>
            City / Municipality: <u>' . htmlspecialchars($cityNames) . '</u></p>';

            // Build table header
            $html .= self::buildTableHeaderHtml($categories);

            $counter = 1;

            // Aggregate totals
            $grandTotals = self::initGrandTotals($categoryKeys, $ages);

            foreach ($reportData as $row) {
                // Build row data
                $html .= '<tr>';
                $html .= '<td width="2%">' . $counter++ . '</td>';
                $html .= '<td width="7%">' . mb_strtoupper(htmlspecialchars($row->center_name, ENT_QUOTES, 'UTF-8')) . '</td>';
                $html .= '<td width="7%">' . mb_strtoupper(htmlspecialchars($row->worker_name ?? 'No Worker Assigned', ENT_QUOTES, 'UTF-8')) . '</td>';
                $html .= '<td width="3%">' . $row->total_children . '</td>';
                $html .= '<td width="2%">' . $row->total_male . '</td>';
                $html .= '<td width="2%">' . $row->total_female . '</td>';

                // Row totals for "Total" columns at the end
                $rowTotalsBySex = ['M' => [], 'F' => []];
                foreach ($ages as $age) {
                    $rowTotalsBySex['M'][$age] = 0;
                    $rowTotalsBySex['F'][$age] = 0;
                }

                // Category columns
                foreach ($categoryKeys as $catIndex => $catKey) {
                    foreach (['male', 'female'] as $sexIndex => $sexStr) {
                        $sexLabel = $sexIndex === 0 ? 'M' : 'F';
                        foreach ($ages as $age) {
                            $colName = "{$catKey}_age_{$age}_{$sexStr}";
                            $val = $row->$colName;
                            $html .= '<td width="1.625%">' . $val . '</td>';
                            $rowTotalsBySex[$sexLabel][$age] += $val;

                            // Accumulate grand totals
                            $grandTotals['categories'][$catKey][$sexLabel][$age] += $val;
                        }
                    }
                }

                // Total columns (M and F by age)
                foreach ($sexLabels as $sex) {
                    foreach ($ages as $age) {
                        $html .= '<td width="1.75%">' . $rowTotalsBySex[$sex][$age] . '</td>';
                        $grandTotals['sexAge'][$sex][$age] += $rowTotalsBySex[$sex][$age];
                    }
                }

                $html .= '</tr>';

                $grandTotals['overall_total'] += $row->total_children;
                $grandTotals['overall_male'] += $row->total_male;
                $grandTotals['overall_female'] += $row->total_female;
            }

            // Footer rows with totals
            $html .= self::buildTotalRows($grandTotals, $categories, $categoryKeys, $ages, $sexLabels);

            $html .= '</tbody></table>';

            // Footer section
            $html .= '<table border="0" cellpadding="5" style="font-size:9px; margin-top:5px; text-align: center;">
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

            $html .= '<p style="font-size:7px; text-align:left;">SFP Forms 4.3 (c/o SFP Focal Person)</p>';

            $pdf->writeHTML($html, true, false, true, false, '');

            $folder = public_path("generated_reports/{$userId}");
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $fileName = "NS_Weight_For_Height_" . now()->format('m_d_Y_H_i_s') . ".pdf";
            $filePath = $folder . '/' . $fileName;

            $pdf->Output($filePath, 'F');

            DB::table('ns_wfh_reports')
                ->where('id', $report->id)
                ->update([
                    'status' => 'completed',
                    'file_path' => "generated_reports/{$userId}/{$fileName}",
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::table('ns_wfh_report_data')
                ->where('report_id', $report->id)
                ->delete();

            return $filePath;

        } catch (\Exception $e) {
            DB::table('ns_wfh_reports')
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
        $centersQuery = DB::table('child_development_centers as cdc')
            ->select(['cdc.id as center_id', 'cdc.center_name']);

        if ($centerIDs) {
            $centersQuery->whereIn('cdc.id', $centerIDs);
        }

        $centers = $centersQuery->orderBy('cdc.center_name')->get();

        $categories = self::$categories;
        $categoryKeys = self::$categoryKeys;
        $ages = self::$ages;

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

            // Get upon-entry nutritional status for funded active children at this center
            $entryNsIds = DB::table('nutritional_statuses')
                ->select(DB::raw('MIN(id) as id'), 'child_id')
                ->whereIn('child_id', function ($query) use ($cycleId, $center) {
                    $query->select('child_id')
                        ->from('child_records')
                        ->where('implementation_id', $cycleId)
                        ->where('funded', 1)
                        ->where('action_type', 'active')
                        ->where('child_development_center_id', $center->center_id);
                })
                ->where('implementation_id', $cycleId)
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
                    'ns.age_in_years',
                    'ns.weight_for_height',
                ])
                ->get();

            // Initialize counts
            $counts = [];
            foreach ($categoryKeys as $catKey) {
                foreach ($ages as $age) {
                    $counts["{$catKey}_age_{$age}_male"] = 0;
                    $counts["{$catKey}_age_{$age}_female"] = 0;
                }
            }

            $totalMale = 0;
            $totalFemale = 0;
            $totalChildren = 0;

            foreach ($children as $child) {
                $sexStr = $child->sex_id == 1 ? 'male' : 'female';
                $age = $child->age_in_years;
                $wfh = $child->weight_for_height;

                // Map the weight_for_height value to category key
                $catKeyIndex = array_search($wfh, $categories);
                if ($catKeyIndex !== false && in_array($age, $ages)) {
                    $catKey = $categoryKeys[$catKeyIndex];
                    $counts["{$catKey}_age_{$age}_{$sexStr}"]++;
                }

                if ($child->sex_id == 1) {
                    $totalMale++;
                } else {
                    $totalFemale++;
                }
                $totalChildren++;
            }

            DB::table('ns_wfh_report_data')->insert(array_merge([
                'report_id' => $reportId,
                'center_id' => $center->center_id,
                'center_name' => $center->center_name,
                'worker_name' => $worker->full_name ?? null,
                'total_children' => $totalChildren,
                'total_male' => $totalMale,
                'total_female' => $totalFemale,
                'created_at' => now(),
                'updated_at' => now(),
            ], $counts));

            $totalRecords++;
        }
    }

    private static function initGrandTotals($categoryKeys, $ages)
    {
        $totals = [
            'overall_total' => 0,
            'overall_male' => 0,
            'overall_female' => 0,
            'categories' => [],
            'sexAge' => [
                'M' => [],
                'F' => [],
            ],
        ];

        foreach ($categoryKeys as $catKey) {
            foreach (['M', 'F'] as $sex) {
                foreach ($ages as $age) {
                    $totals['categories'][$catKey][$sex][$age] = 0;
                }
            }
        }

        foreach ($ages as $age) {
            $totals['sexAge']['M'][$age] = 0;
            $totals['sexAge']['F'][$age] = 0;
        }

        return $totals;
    }

    /**
     * Build the main table header HTML with explicit percentage widths.
     *
     * Column width distribution (must total 100%):
     * - No.: 2%
     * - CDC Name: 7%
     * - CDW Name: 7%
     * - Total Served: 3%
     * - M/F totals: 4% (2% each)
     * - 5 Categories × 8 cols = 40 data columns @ 1.625% each = 65%
     * - Total section: 8 cols @ 1.75% each = 14%
     *
     * Using table-layout: fixed forces the browser to respect these widths.
     */
    private static function buildTableHeaderHtml($categories)
    {
        $html = '<table border="1" cellpadding="1" cellspacing="0" style="font-size:5px; border-collapse:collapse; table-layout:fixed;" width="100%">
            <thead>
                <tr style="background-color:#f0f0f0; font-weight:bold;">
                    <th rowspan="3" width="2%">No.</th>
                    <th rowspan="3" width="7%">Name of Child Development Center</th>
                    <th rowspan="3" width="7%">Name of Child Development Worker</th>
                    <th rowspan="3" width="3%">Total Number Served</th>
                    <th rowspan="2" colspan="2" width="4%">Total No of CDC/SNP Served</th>';

        // 5 categories × 8 columns each, plus Total section (8 columns)
        foreach ($categories as $category) {
            $html .= '<th colspan="8" width="13%">' . htmlspecialchars($category) . '</th>';
        }
        $html .= '<th colspan="8" width="14%">Total</th>';
        $html .= '</tr>';

        // Second header row - M/F per category
        $html .= '<tr style="background-color:#f0f0f0; font-weight:bold;">';
        foreach ($categories as $category) {
            $html .= '<th colspan="4">M</th><th colspan="4">F</th>';
        }
        $html .= '<th colspan="4">M</th><th colspan="4">F</th>';
        $html .= '</tr>';

        // Third header row - M/F label + ages
        $html .= '<tr style="background-color:#f0f0f0; font-weight:bold;">';
        $html .= '<th width="2%">M</th><th width="2%">F</th>';
        foreach ($categories as $category) {
            $html .= '<th>2</th><th>3</th><th>4</th><th>5</th>';
            $html .= '<th>2</th><th>3</th><th>4</th><th>5</th>';
        }
        $html .= '<th>2</th><th>3</th><th>4</th><th>5</th>';
        $html .= '<th>2</th><th>3</th><th>4</th><th>5</th>';
        $html .= '</tr></thead><tbody>';

        return $html;
    }

    private static function buildTotalRows($grandTotals, $categories, $categoryKeys, $ages, $sexLabels)
    {
        $html = '';

        // Row 1: TOTAL PER AGE BRACKET
        $html .= '<tr style="font-weight:bold;">';
        $html .= '<td colspan="3">TOTAL PER AGE BRACKET &gt;</td>';
        $html .= '<td rowspan="3">' . $grandTotals['overall_total'] . '</td>';
        $html .= '<td>' . $grandTotals['overall_male'] . '</td>';
        $html .= '<td>' . $grandTotals['overall_female'] . '</td>';

        foreach ($categoryKeys as $catKey) {
            foreach ($sexLabels as $sex) {
                foreach ($ages as $age) {
                    $html .= '<td>' . $grandTotals['categories'][$catKey][$sex][$age] . '</td>';
                }
            }
        }

        foreach ($sexLabels as $sex) {
            foreach ($ages as $age) {
                $html .= '<td>' . $grandTotals['sexAge'][$sex][$age] . '</td>';
            }
        }
        $html .= '</tr>';

        // Row 2: TOTAL MALE/FEMALE
        $totalAllGender = $grandTotals['overall_male'] + $grandTotals['overall_female'];
        $html .= '<tr style="font-weight:bold;">';
        $html .= '<td colspan="3">TOTAL MALE/FEMALE &gt;</td>';
        $html .= '<td rowspan="2" colspan="2">' . $totalAllGender . '</td>';

        foreach ($categoryKeys as $catKey) {
            foreach ($sexLabels as $sex) {
                $catSexTotal = 0;
                foreach ($ages as $age) {
                    $catSexTotal += $grandTotals['categories'][$catKey][$sex][$age];
                }
                $html .= '<td colspan="4">' . $catSexTotal . '</td>';
            }
        }

        foreach ($sexLabels as $sex) {
            $sexTotal = 0;
            foreach ($ages as $age) {
                $sexTotal += $grandTotals['sexAge'][$sex][$age];
            }
            $html .= '<td colspan="4">' . $sexTotal . '</td>';
        }
        $html .= '</tr>';

        // Row 3: TOTAL CHILD BENEFICIARIES
        $html .= '<tr style="font-weight:bold;">';
        $html .= '<td colspan="3">TOTAL CHILD BENEFICIARIES &gt;</td>';

        foreach ($categoryKeys as $catKey) {
            $catTotal = 0;
            foreach ($sexLabels as $sex) {
                foreach ($ages as $age) {
                    $catTotal += $grandTotals['categories'][$catKey][$sex][$age];
                }
            }
            $html .= '<td colspan="8">' . $catTotal . '</td>';
        }

        $html .= '<td colspan="8">' . $grandTotals['overall_total'] . '</td>';
        $html .= '</tr>';

        return $html;
    }
}
