<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        .header {
            font-family: 'Arial', sans-serif;
            text-align: center;
            font-size: 12px;
        }

        .header-section {
            width: 100%;
            margin: 0 auto;
        }

        .table {
            width: 100%;
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            border: 5px
        }

        .table td {
            padding: 5px;
            vertical-align: top;
        }

        .p {
            margin: 1px 0;
        }

        .header-bg{
            background-color: #ABC9B6;
        }

        .space-only{
            background-color: #BCBCBC;
        }

        .age-bracket-after-120-table{
            font-size: 9px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-collapse: collapse;
        }

        .age-bracket-after-120-table th,
        .age-bracket-after-120-table td {
            border: 1px solid rgba(0, 0, 0, 0.5);
            padding: 1px;
            text-transform: uppercase;
        }

        .age-bracket-after-120-table td:first-child {
            width: 20%;
            text-align: left;
            position: absolute;
            bottom: 0;
            left: 0;
        }

        .footer-table {
            width: 100%;
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            border: 5px
        }

        .footer-table p {
            margin: 0;
        }

        .footer-table td {
            padding: 10px;
            vertical-align: top;
        }

        .no-wrap {
            white-space: nowrap;
        }

        @page {
            margin-top: 20px;
            margin-bottom: 0;
            margin-right: 30px;
            margin-left: 30px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
        }

        .pagenum:before {
            content: "Page " counter(page) " of ";
            text-align: center;
            display: flex;
        }

    </style>
</head>

<body>
    {{-- <input type="hidden" id="funded_center_name" name="funded_center_name" value="{{ $center->id}}"> --}}
    <div class="header">
        <p>Department of Social Welfare and Development, Field Office XI<br>
            Supplementary Feeding Program<br>
            {{ $cycleImplementation->cycle_name }} ( SY {{ $cycleImplementation->cycle_school_year }} )<br>
            <b>CONSOLIDATED NUTRITIONAL STATUS (NS) PER AGE BRACKET</b><br>
            <i>AFTER 120 FEEDING DAYS</i>
        </p>
        <br>
    </div>

    @if ($selectedCenter)
        <table class="table">
            <tr>
                <td>
                    <p>Province: <u>{{ $selectedCenter->psgc->province_name }}</u></p>
                    <p>Child Development Center: <u>{{ $selectedCenter->center_name }}</u></p>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>
                    <p>City / Municipality: <u>{{ $selectedCenter->psgc->city_name }}</u></p>
                    <p>Barangay: <u>{{ $selectedCenter->psgc->brgy_name }}</u></p>
                </td>
            </tr>
        </table>
    @else
        <p>Child Development Center: <u>All Child Development Centers</u></p>
    @endif

    <table id='age-bracket-after-120-table' class="table datatable age-bracket-after-120-table w-full">
        <thead class="header-bg">
            <tr>
                <th rowspan="2">WEIGHT FOR AGE</th>
                <th colspan="2">2 YEARS OLD</th>
                <th colspan="2">3 YEARS OLD</th>
                <th colspan="2">4 YEARS OLD</th>
                <th colspan="2">5 YEARS OLD</th>
                <th colspan="2">TOTAL:</th>
            </tr>
            <tr>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
            </tr>
        </thead>
        <tbody class="weight-for-age-table text-xs">

            @php
                $totalNormalMale = 0;
                $totalNormalFemale = 0;
                $totalUWMale = 0;
                $totalUWFemale = 0;
                $totalSUWMale = 0;
                $totalSUWFemale = 0;
                $totalOWMale = 0;
                $totalOWFemale = 0;

                foreach ([2, 3, 4, 5] as $age) {
                    $totalNormalMale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_normal']['male'] ?? 0;
                    $totalNormalFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_normal']['female'] ?? 0;
                    $totalUWMale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_underweight']['male'] ?? 0;
                    $totalUWFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_underweight']['female'] ?? 0;
                    $totalSUWMale +=
                        $exitCountsPerNutritionalStatus[$age]['weight_for_age_severely_underweight']['male'] ?? 0;
                    $totalSUWFemale +=
                        $exitCountsPerNutritionalStatus[$age]['weight_for_age_severely_underweight']['female'] ?? 0;
                    $totalOWMale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_overweight']['male'] ?? 0;
                    $totalOWFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_overweight']['female'] ?? 0;
                }
            @endphp
            <tr>
                <td class="text-left">Normal (N)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_normal']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_normal']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_normal']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_normal']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_normal']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_normal']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_normal']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_normal']['female'] ?? 0 }}</td>
                <td>{{ $totalNormalMale }}</td>
                <td>{{ $totalNormalFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Underweight (UW)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_underweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_underweight']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_underweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_underweight']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_underweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_underweight']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_underweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_underweight']['female'] ?? 0 }}</td>
                <td>{{ $totalUWMale }}</td>
                <td>{{ $totalUWFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Severely Underweight (SUW)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
                <td>{{ $totalSUWMale }}</td>
                <td>{{ $totalSUWFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Overweight (OW)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_overweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_overweight']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_overweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_overweight']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_overweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_overweight']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_overweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_overweight']['female'] ?? 0 }}</td>
                <td>{{ $totalOWMale }}</td>
                <td>{{ $totalOWFemale }}</td>
            </tr>
        </tbody>

        <tr class="space-only"><td colspan="11"></td>
        </tr>

        <thead class="header-bg">
            <tr>
                <th rowspan="2">WEIGHT FOR HEIGHT</th>
                <th colspan="2">2 YEARS OLD</th>
                <th colspan="2">3 YEARS OLD</th>
                <th colspan="2">4 YEARS OLD</th>
                <th colspan="2">5 YEARS OLD</th>
                <th colspan="2">TOTAL:</th>
            </tr>
            <tr>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
            </tr>
        </thead>
        <tbody class="weight-for-height-table text-xs">
            @php
                $totalNormalMale = 0;
                $totalNormalFemale = 0;
                $totalWMale = 0;
                $totalWFemale = 0;
                $totalSWMale = 0;
                $totalSWFemale = 0;
                $totalOWMale = 0;
                $totalOWFemale = 0;
                $totalObMale = 0;
                $totalObFemale = 0;

                // Loop through each age group to calculate the total
                foreach ([2, 3, 4, 5] as $age) {
                    $totalNormalMale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_normal']['male'] ?? 0;
                    $totalNormalFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_normal']['female'] ?? 0;
                    $totalWMale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_wasted']['male'] ?? 0;
                    $totalWFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_wasted']['female'] ?? 0;
                    $totalSWMale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_severely_wasted']['male'] ?? 0;
                    $totalSWFemale +=
                        $exitCountsPerNutritionalStatus[$age]['weight_for_height_severely_wasted']['female'] ?? 0;
                    $totalOWMale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_overweight']['male'] ?? 0;
                    $totalOWFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_overweight']['female'] ?? 0;
                    $totalObMale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_obese']['male'] ?? 0;
                    $totalObFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_obese']['female'] ?? 0;
                }
            @endphp
            <tr>
                <td class="text-left">Normal (N)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_normal']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_normal']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_normal']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_normal']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_normal']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_normal']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_normal']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_normal']['female'] ?? 0 }}</td>
                <td>{{ $totalNormalMale }}</td>
                <td>{{ $totalNormalFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Wasted (W)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_wasted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_wasted']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_wasted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_wasted']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_wasted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_wasted']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_wasted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_wasted']['female'] ?? 0 }}</td>
                <td>{{ $totalWMale }}</td>
                <td>{{ $totalWFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Severely Wasted (SW)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_severely_wasted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_severely_wasted']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_severely_wasted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_severely_wasted']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_severely_wasted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_severely_wasted']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_severely_wasted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_severely_wasted']['female'] ?? 0 }}</td>
                <td>{{ $totalSWMale }}</td>
                <td>{{ $totalSWFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Overweight (OW)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_overweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_overweight']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_overweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_overweight']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_overweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_overweight']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_overweight']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_overweight']['female'] ?? 0 }}</td>
                <td>{{ $totalOWMale }}</td>
                <td>{{ $totalOWFemale }}</td>
            </tr>
            <tr>
                <td class="text-left">Obese (Ob)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_obese']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_obese']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_obese']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_obese']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_obese']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_obese']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_obese']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_obese']['female'] ?? 0 }}</td>
                <td>{{ $totalObMale }}</td>
                <td>{{ $totalObFemale }}</td>
            </tr>
        </tbody>

        <tr class="space-only"><td colspan="11"></td>
        </tr>

        <thead class="header-bg">
            <tr>
                <th rowspan="2">HEIGHT FOR AGE</th>
                <th colspan="2">2 YEARS OLD</th>
                <th colspan="2">3 YEARS OLD</th>
                <th colspan="2">4 YEARS OLD</th>
                <th colspan="2">5 YEARS OLD</th>
                <th colspan="2">TOTAL:</th>
            </tr>
            <tr>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
            </tr>
        </thead>
        <tbody class="weight-for-age-table text-xs">
            @php
                $totalNormalMale = 0;
                $totalNormalFemale = 0;
                $totalSMale = 0;
                $totalSFemale = 0;
                $totalSSMale = 0;
                $totalSSFemale = 0;
                $totalTMale = 0;
                $totalTFemale = 0;

                foreach ([2, 3, 4, 5] as $age) {
                    $totalNormalMale += $exitCountsPerNutritionalStatus[$age]['height_for_age_normal']['male'] ?? 0;
                    $totalNormalFemale += $exitCountsPerNutritionalStatus[$age]['height_for_age_normal']['female'] ?? 0;
                    $totalSMale += $exitCountsPerNutritionalStatus[$age]['height_for_age_stunted']['male'] ?? 0;
                    $totalSFemale += $exitCountsPerNutritionalStatus[$age]['height_for_age_stunted']['female'] ?? 0;
                    $totalSSMale += $exitCountsPerNutritionalStatus[$age]['height_for_age_severely_stunted']['male'] ?? 0;
                    $totalSSFemale +=
                        $exitCountsPerNutritionalStatus[$age]['height_for_age_severely_stunted']['female'] ?? 0;
                    $totalTMale += $exitCountsPerNutritionalStatus[$age]['height_for_age_tall']['male'] ?? 0;
                    $totalTFemale += $exitCountsPerNutritionalStatus[$age]['height_for_age_tall']['female'] ?? 0;
                }
            @endphp
            <tr>
                <td class="text-left">Normal (N)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_normal']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_normal']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_normal']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_normal']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_normal']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_normal']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_normal']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_normal']['female'] ?? 0 }}</td>
                <td>{{ $totalNormalMale }}</td>
                <td>{{ $totalNormalFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Stunted (S)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_stunted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_stunted']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_stunted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_stunted']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_stunted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_stunted']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_stunted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_stunted']['female'] ?? 0 }}</td>
                <td>{{ $totalSMale }}</td>
                <td>{{ $totalSFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Severely Stunted (SS)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_severely_stunted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_severely_stunted']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_severely_stunted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_severely_stunted']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_severely_stunted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_severely_stunted']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_severely_stunted']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_severely_stunted']['female'] ?? 0 }}</td>
                <td>{{ $totalSSMale }}</td>
                <td>{{ $totalSSFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Tall (T)</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_tall']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_tall']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_tall']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_tall']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_tall']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_tall']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_tall']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_tall']['female'] ?? 0 }}</td>
                <td>{{ $totalTMale }}</td>
                <td>{{ $totalTFemale }}</td>
            </tr>
        </tbody>

        <tr class="space-only"><td colspan="11"></td>
        </tr>

        <thead class="header-bg">
            <tr>
                <th rowspan="2"></th>
                <th class="border border-white w-20" colspan="2">2 YEARS OLD</th>
                <th class="border border-white w-20" colspan="2">3 YEARS OLD</th>
                <th class="border border-white w-20" colspan="2">4 YEARS OLD</th>
                <th class="border border-white w-20" colspan="2">5 YEARS OLD</th>
                <th class="border border-white w-20" colspan="2">TOTAL:</th>
            </tr>
            <tr>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
                <th>Male</th>
                <th>Female</th>
            </tr>
        </thead>
        @php
            $totalUndernourishMale = 0;
            $totalUndernourishFemale = 0;
            $totalDewormedMale = 0;
            $totalDewormedFemale = 0;
            $totalVitAMale = 0;
            $totalVitAFemale = 0;
            $totalPantawidMale = 0;
            $totalPantawidFemale = 0;
            $totalIPMale = 0;
            $totalIPFemale = 0;
            $totalPWDMale = 0;
            $totalPWDFemale = 0;
            $totalSoloParentMale = 0;
            $totalSoloParentFemale = 0;
            $totalLactoseIntolerantMale = 0;
            $totalLactoseIntolerantFemale = 0;

            // Loop through each age group to calculate the total
            foreach ([2, 3, 4, 5] as $age) {
                $totalUndernourishMale += $exitCountsPerNutritionalStatus[$age]['is_undernourish']['male'] ?? 0;
                $totalUndernourishFemale += $exitCountsPerNutritionalStatus[$age]['is_undernourish']['female'] ?? 0;
                $totalDewormedMale += $exitCountsPerNutritionalStatus[$age]['dewormed']['male'] ?? 0;
                $totalDewormedFemale += $exitCountsPerNutritionalStatus[$age]['dewormed']['female'] ?? 0;
                $totalVitAMale += $exitCountsPerNutritionalStatus[$age]['vitamin_a']['male'] ?? 0;
                $totalVitAFemale += $exitCountsPerNutritionalStatus[$age]['vitamin_a']['female'] ?? 0;
                $totalPantawidMale += $exitCountsPerNutritionalStatus[$age]['pantawid']['male'] ?? 0;
                $totalPantawidFemale += $exitCountsPerNutritionalStatus[$age]['pantawid']['female'] ?? 0;
                $totalIPMale += $exitCountsPerNutritionalStatus[$age]['indigenous_people']['female'] ?? 0;
                $totalIPFemale += $exitCountsPerNutritionalStatus[$age]['indigenous_people']['male'] ?? 0;
                $totalPWDMale += $exitCountsPerNutritionalStatus[$age]['pwd']['female'] ?? 0;
                $totalPWDFemale += $exitCountsPerNutritionalStatus[$age]['pwd']['male'] ?? 0;
                $totalSoloParentMale += $exitCountsPerNutritionalStatus[$age]['child_of_soloparent']['male'] ?? 0;
                $totalSoloParentFemale += $exitCountsPerNutritionalStatus[$age]['child_of_soloparent']['female'] ?? 0;
                $totalLactoseIntolerantMale += $exitCountsPerNutritionalStatus[$age]['lactose_intolerant']['female'] ?? 0;
                $totalLactoseIntolerantFemale += $exitCountsPerNutritionalStatus[$age]['lactose_intolerant']['male'] ?? 0;
            }
        @endphp
        
        <tbody class="profile-table text-xs">
            <tr>
                <td class="text-left">Summary of Undernourished Children</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['is_undernourish']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['is_undernourish']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['is_undernourish']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['is_undernourish']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['is_undernourish']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['is_undernourish']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['is_undernourish']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['is_undernourish']['female'] ?? 0 }}</td>
                <td>{{ $totalUndernourishMale }}</td>
                <td>{{ $totalUndernourishFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Deworming</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['dewormed']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['dewormed']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['dewormed']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['dewormed']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['dewormed']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['dewormed']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['dewormed']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['dewormed']['female'] ?? 0 }}</td>
                <td>{{ $totalDewormedMale }}</td>
                <td>{{ $totalDewormedFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Vitamin A Supplementation</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['vitamin_a']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['vitamin_a']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['vitamin_a']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['vitamin_a']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['vitamin_a']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['vitamin_a']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['vitamin_a']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['vitamin_a']['female'] ?? 0 }}</td>
                <td>{{ $totalVitAMale }}</td>
                <td>{{ $totalVitAFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">4Ps Member</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['pantawid']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['pantawid']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['pantawid']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['pantawid']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['pantawid']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['pantawid']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['pantawid']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['pantawid']['female'] ?? 0 }}</td>
                <td>{{ $totalPantawidMale }}</td>
                <td>{{ $totalPantawidFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">IP Member</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['indigenous_people']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['indigenous_people']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['indigenous_people']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['indigenous_people']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['indigenous_people']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['indigenous_people']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['indigenous_people']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['indigenous_people']['female'] ?? 0 }}</td>
                <td>{{ $totalIPMale }}</td>
                <td>{{ $totalIPFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">PWD</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['pwd']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['pwd']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['pwd']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['pwd']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['pwd']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['pwd']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['pwd']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['pwd']['female'] ?? 0 }}</td>
                <td>{{ $totalPWDMale }}</td>
                <td>{{ $totalPWDFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Child of Solo Parent</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['child_of_soloparent']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['child_of_soloparent']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['child_of_soloparent']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['child_of_soloparent']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['child_of_soloparent']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['child_of_soloparent']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['child_of_soloparent']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['child_of_soloparent']['female'] ?? 0 }}</td>
                <td>{{ $totalSoloParentMale }}</td>
                <td>{{ $totalSoloParentFemale }}</td>
            </tr>

            <tr>
                <td class="text-left">Lactose Intolerant</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['lactose_intolerant']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[2]['lactose_intolerant']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['lactose_intolerant']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[3]['lactose_intolerant']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['lactose_intolerant']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[4]['lactose_intolerant']['female'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['lactose_intolerant']['male'] ?? 0 }}</td>
                <td>{{ $exitCountsPerNutritionalStatus[5]['lactose_intolerant']['female'] ?? 0 }}</td>
                <td>{{ $totalLactoseIntolerantMale }}</td>
                <td>{{ $totalLactoseIntolerantFemale }}</td>
            </tr>

        </tbody>
    </table>

    <table class="footer-table">
        <tr></tr>
        <tr></tr>
        <tr>
            <td colspan="3">
                <br>
                <br>
                <p>Prepare by:</p>
                <br>
                <p>______________________________________</p>
                <p>Child Development Worker/Teacher</p>
            </td>
            <td>
                <br>
                <br>
                <p>Noted by:</p>
                <br>
                <p>______________________________________</p>
                <p>SFP Focal Person</p>
            </td>
            <td>
                <br>
                <br>
                <p>Approved by:</p>
                <br>
                <p>______________________________________</p>
                <p>C/MSWDO/District Head</p>
            </td>
        </tr>
    </table>
    <div class="footer">
        SFP Forms 2.1 (c/o CDW/CDT)
        <span class="pagenum"></span>
    </div>


</body>

</html>
