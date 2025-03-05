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
        }

        .footer-table td {
            padding: 10px;
            vertical-align: top;
        }

        .no-wrap {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    {{-- <input type="hidden" id="funded_center_name" name="funded_center_name" value="{{ $center->id}}"> --}}
    <div class="header">
        <p>Department of Social Welfare and Development, Field Office XI</p>
        <p>Supplementary Feeding Program</p>
        <p>{{ $cycle->name}} ( SY {{ $cycle->school_year }} )</p>
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
                    <td class="first">{{ $fundedChild->full_name }}</td>
                    <td class="first">{{ optional($fundedChild->records->first()->center)->center_name ?? 'N/A' }}</td>
                    <td>{{ $fundedChild->sex->name == 'Male' ? 'M' : 'F' }}</td>
                    <td class="no-wrap">{{ $fundedChild->date_of_birth }}</td>

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
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->weight : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->height : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->age_in_months : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->age_in_years : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->weight_for_age : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->weight_for_height : 'N/A' }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->height_for_age : 'N/A' }}</td>
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
                    <td class="text-center" colspan="6">
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
                    <p>______________________________________</p>
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

    <footer>

    </footer>

</body>
</html>
