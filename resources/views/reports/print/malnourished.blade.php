<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('https://172.31.176.49/sfpproject/public') }}">

    <title>List of Malnourish Children</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('img/SFP-LOGO-2024.png') }}" rel="icon">
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
            border: 5px;
            text-transform: uppercase;
        }

        .table td {
            padding: 10px;
            vertical-align: top;
        }

        .first {
            width: 20%;
        }

        .p {
            margin: 5px 0;
        }

        .malnourished-table {
            font-size: 9px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-collapse: collapse;
        }

        .malnourished-table th, .malnourished-table td {
            border: 1px solid rgba(0, 0, 0, 0.5);
            text-transform: uppercase;
        }

        .footer-table {
            width: 100%;
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            border: 5px
        }

        .footer-table p{
            margin: 0;
            text-transform: uppercase;
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
    </style>
</head>
<body>
    {{-- <input type="hidden" id="funded_center_name" name="funded_center_name" value="{{ $center->id}}"> --}}
    <div class="header">
        <p>Department of Social Welfare and Development, Field Office XI</p>
        <p>Supplementary Feeding Program</p>
        <p>{{ $cycle->name}} ( CY {{ $cycle->school_year_from }} )</p>
        <p><b>LIST OF MALNOURISHED CHILDREN</b></p>
        <br>
    </div>

    <div class="header-section">
        <table class="table">
            <tr>
                <td>
                    <p>Province: <u>{{ $province ? $province->implode(', ') : 'All Provinces' }}</u></p>
                    <p>City / Municipality: <u>{{ $city ? $city->implode(', ') : 'All Cities' }}</u></p>
                </td>
            </tr>
        </table>
    </div>

    <table id='malnourished-table' class="table datatable malnourished-table w-full">
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Name of Child</th>
                <th rowspan="2">Name of Child Development Center</th>
                <th rowspan="2">Sex</th>
                <th rowspan="2">Date of Birth</th>
                <th rowspan="2">Actual Date of Weighing</th>
                <th rowspan="2">Weight in kg.</th>
                <th rowspan="2">Height in cm.</th>
                <th colspan="2">Age in month/year</th>
                <th colspan="3">NS UPON ENTRY</th>
                <th rowspan="2">Actual Date of Weighing</th>
                <th rowspan="2">Weight in kg.</th>
                <th rowspan="2">Height in cm.</th>
                <th colspan="2">Age in month/year</th>
                <th colspan="3">NS AFTER 120 FEEDINGS</th>
            </tr>
            <tr>
                <th>Month</th>
                <th>Year</th>
                <th>Weight for Age</th>
                <th>Weight for Height</th>
                <th>Height for Age</th>
                <th>Month</th>
                <th>Year</th>
                <th>Weight for Age</th>
                <th>Weight for Height</th>
                <th>Height for Age</th>
            </tr>
        </thead>
        <tbody class="malnourished-table text-xs ">
            @foreach ($isFunded as $fundedChild)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="first">{{ $fundedChild->full_name }}</td>
                    <td class="first">{{ optional($fundedChild->records->first()->center)->center_name ?? 'N/A' }}</td>
                    <td>{{ $fundedChild->sex->name == 'Male' ? 'M' : 'F' }}</td>
                    <td class="no-wrap">{{ $fundedChild->date_of_birth->format('m-d-Y') }}</td>

                    @if ($fundedChild->nutritionalStatus->first() === null)
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @else
                            <td class="no-wrap">{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->actual_weighing_date : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? number_format($fundedChild->nutritionalStatus->first()->weight, 1) : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? number_format($fundedChild->nutritionalStatus->first()->height, 1) : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->age_in_months : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->age_in_years : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->weight_for_age : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->weight_for_height : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->height_for_age : 'N/A' }}</td>
                        @endif
                        @if (isset($fundedChild->nutritionalStatus[1]))
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->actual_weighing_date : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? number_format($fundedChild->nutritionalStatus[1]->weight, 1) : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? number_format($fundedChild->nutritionalStatus[1]->height, 1) : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->age_in_months : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->age_in_years : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->weight_for_age : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->weight_for_height : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->height_for_age : 'N/A' }}</td>
                        @else
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
            @endforeach
            @if (count($isFunded) <= 0)
                <tr>
                    <td class="text-center" colspan="21">
                        @if (empty($search))
                            No Data found
                        @endif
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer-section">
        <table class="footer-table">
            <tr></tr>
            <tr></tr>
            <tr>
                <td>
                    <br>
                    <br>
                    <p>Noted by:</p>
                    <br>
                    <br>
                    <p>
                        @if (auth()->user()->hasRole('lgu focal'))
                            <u>{{ auth()->user()->full_name }}</u>
                        @else
                            ______________________________________
                        @endif
                    </p>
                    <p>SFP Focal Person</p>
                </td>
                <td>
                    <br>
                    <br>
                    <p>Approved by:</p>
                    <br>
                    <br>
                    <p>______________________________________</p>
                    <p>C/MSWDO/District Head</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        SFP Forms 6 (c/o SFP Focal Person)
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial", "normal");
                $fontSize = 8;
                $text = "Page $PAGE_NUM of $PAGE_COUNT";
                $width = $fontMetrics->get_text_width($text, $font, $fontSize);
                $x = (936 / 2) - ($width / 2);
                $y = 580;
                $pdf->text($x, $y, $text, $font, $fontSize);
            ');
        }
    </script>

</body>
</html>
