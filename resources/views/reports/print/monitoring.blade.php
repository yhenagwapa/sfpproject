<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .header {
            font-family: 'Arial', sans-serif;
            text-align: center;
            font-size: 12px;
        }

        .header-section .footer-section {
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
            padding: 10px;
            vertical-align: top;
        }

        .p {
            margin: 5px 0;
        }

        .monitoring-table {
            font-size: 9px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-collapse: collapse;
        }

        .monitoring-table th, .monitoring-table td {
            border: 1px solid rgba(0, 0, 0, 0.5);
            text-transform: uppercase;
        }

        .monitoring-table td:nth-child(2){
            width: 15%;
        }

        .footer-table {
            width: 100%;
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            border: 5px
        }

        .footer-table p{
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
            {{ $cycle->name}} ( SY {{ $cycle->school_year_from }} - {{ $cycle->school_year_to }} )</p>
        <p><b>WEIGHT AND HEIGHT MONITORING</b></p>
        <br>
    </div>

    <div class="header-section">
        @if($selectedCenter)
            <table class="table">
                <tr>
                    <td>
                        <p>Province: <u>{{ $selectedCenter->psgc->province_name }}</u></p>
                        <p>Child Development Center: <u>{{ $selectedCenter->center_name }}</u></p>
                    </td>
                    <td>
                        <p>City / Municipality: <u>{{ $selectedCenter->psgc->city_name }}</u></p>
                        <p>Barangay: <u>{{ $selectedCenter->psgc->brgy_name }}</u></p>
                    </td>
                </tr>
            </table>
        @else
            <p>Child Development Center: <u>All Child Development Centers</u></p>
        @endif
    </div>

    <table id='monitoring-table' class="table datatable monitoring-table w-full">
        <thead>
            <tr>
                <th class="border border-white" rowspan="2">No.</th>
                <th class="border border-white w-40" rowspan="2">Name of Child</th>
                <th class="border border-white" rowspan="2">Sex</th>
                <th class="border border-white w-24" rowspan="2">Date of Birth</th>
                <th class="border border-white w-24" rowspan="2">Actual Date <br>of Weighing</th>
                <th class="border border-white" rowspan="2">Weight <br>in kg.</th>
                <th class="border border-white" rowspan="2">Height <br>in cm.</th>
                <th class="border border-white" colspan="2">Age in month/year</th>
                <th class="border border-white" colspan="3">Nutritional Status <br>Upon Entry</th>
                <th class="border border-white w-10" rowspan="2">Summary of Under-<br>nourished Children</th>
                <th class="border border-white w-24" rowspan="2">Actual Date <br>of Weighing</th>
                <th class="border border-white" rowspan="2">Weight <br>in kg.</th>
                <th class="border border-white" rowspan="2">Height <br>in cm.</th>
                <th class="border border-white" colspan="2">Age in month/year</th>
                <th class="border border-white" colspan="3">Nutritional Status <br>After 120 Feedings</th>
                <th class="border border-white w-10" rowspan="2">Summary of Under-<br>nourished Children</th>
            </tr>
            <tr>
                <th class="border border-white">Month</th>
                <th class="border border-white">Year</th>
                <th class="border border-white">Weight for<br> Age</th>
                <th class="border border-white">Weight for Height</th>
                <th class="border border-white">Height for Age</th>
                <th class="border border-white">Month</th>
                <th class="border border-white">Year</th>
                <th class="border border-white">Weight for<br> Age</th>
                <th class="border border-white">Weight for Height</th>
                <th class="border border-white">Height for Age</th>
            </tr>
        </thead>
        {{-- @php
        dd($isFunded); // This will stop execution and show the contents of $isFunded
    @endphp --}}
        <tbody class="monitoring-table text-xs">
            @forelse ($isFunded as $fundedChild)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $fundedChild->full_name }}</td>
                    <td>{{ $fundedChild->sex->name == 'Male' ? 'M' : 'F' }}</td>
                    <td>{{ $fundedChild->date_of_birth->format('Y-m-d') }}</td>

                    @if ($fundedChild->nutritionalStatus)
                        @if ($fundedChild->nutritionalStatus->first() === null)
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @else
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->actual_weighing_date : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->weight : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->height : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->age_in_months : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->age_in_years : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->weight_for_age : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->weight_for_height : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->height_for_age : 'N/A' }}</td>
                            <td>
                                @if ($fundedChild->nutritionalStatus->isNotEmpty() && $fundedChild->nutritionalStatus->first()->is_undernourish)
                                    1
                                @elseif ($fundedChild->nutritionalStatus->isNotEmpty())
                                    0
                                @endif
                            </td>
                        @endif
                        @if (isset($fundedChild->nutritionalStatus[1]))
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->actual_weighing_date : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->weight : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->height : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->age_in_months : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->age_in_years : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->weight_for_age : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->weight_for_height : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->height_for_age : 'N/A' }}</td>
                            <td>
                                @if ($fundedChild->nutritionalStatus->isNotEmpty() && $fundedChild->nutritionalStatus[1]->is_undernourish)
                                    1
                                @elseif ($fundedChild->nutritionalStatus->isNotEmpty())
                                    0
                                @endif
                            </td>
                        @else
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td class="text-center" colspan="22">NO DATA FOUND</td>
                </tr>
            @endforelse

        </tbody>
    </table>


    <div class="footer-section">
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
    </div>

    <div class="footer">
        SFP Forms 2 (c/o CDW/CDT)
    </div>

</body>

</html>
{{-- <script type="text/php">
    if (isset($pdf)) {
        $pdf->page_script('
            $font = $fontMetrics->get_font("Arial", "normal");
            $pdf->text(100, 100, "TEST PAGE {PAGE_NUM}", $font, 20);
        ');
    }
</script> --}}
