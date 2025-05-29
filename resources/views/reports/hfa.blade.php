<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        .header {
            font-family: 'Arial', sans-serif;
            text-align: center;
            font-size: 12px;
            margin: 0;
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
            padding: 5px;
            vertical-align: top;
        }

        .td-width {
            width: 30px;
        }

        .table-header {
            font-size: 12px;
            background-color: #8bbbdb;
        }

        .border-bg {
            background-color: #8bbbdb;
        }

        .border-bg-subhead {
            background-color: #edc5c9;
        }

        .columns {
            width: 170px;
            font-size: 10px;
            text-align: left;
        }

        .weight-for-age-upon-entry-table {
            font-size: 10px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-collapse: collapse;
        }

        .weight-for-age-upon-entry-table th,
        .weight-for-age-upon-entry-table td {
            border: 1px solid rgba(0, 0, 0, 0.5);
            text-transform: uppercase;
        }

        .totals {
            text-align: right;
            font-size: 10px;
            font-style: italic;
        }

        .footer-table {
            width: 100%;
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            border: 5px
        }

        .footer-table p {
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

        .pagenum:before {
            content: "Page " counter(page) " of ";
            text-align: center;
            display: flex;
        }
    </style>
</head>

<body>

<body>
    <table id='weight-for-age-upon-entry-table' class="table datatable weight-for-age-upon-entry-table w-full">
        <thead>
            <tr>
                <th rowspan="3" style="text-align: center;">Center Name</th>
                <th rowspan="3" style="text-align: center;">Center Worker</th>
                <th rowspan="3" style="text-align: center;">Total Number Served</th>
                <th rowspan="2" colspan="2" style="text-align: center;">Total No of CDC/SNP Served</th>
                @foreach ($categories as $category)
                    <th colspan="8" style="text-align: center;">{{ $category }}</th>
                @endforeach
                <th colspan="8" style="text-align: center;">Total</th>
            </tr>
            <tr>
                @foreach ($categories as $category)
                    @foreach ($sexLabels as $sex)
                        <th colspan="4" style="text-align: center;">{{ $sex }}</th>
                    @endforeach
                @endforeach
                <th colspan="4" style="text-align: center;">M</th>
                <th colspan="4" style="text-align: center;">F</th>
            </tr>
            <tr>
                @foreach ($sexLabels as $sex)
                    <th>{{ $sex }}</th>
                @endforeach
                @foreach ($categories as $category)
                    @foreach ($sexLabels as $sex)
                        @foreach ([2, 3, 4, 5] as $age)
                            <th>{{ $age }}</th>
                        @endforeach
                    @endforeach
                @endforeach
                @foreach ([2, 3, 4, 5] as $age)
                    <th>{{ $age }}</th>
                @endforeach
                @foreach ([2, 3, 4, 5] as $age)
                    <th>{{ $age }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $totalM = 0;
                $totalF = 0;
                $overallTotal = 0;

                $totals = [
                    'M' => [2 => 0, 3 => 0, 4 => 0, 5 => 0],
                    'F' => [2 => 0, 3 => 0, 4 => 0, 5 => 0],
                ];
            @endphp

            @foreach ($centers as $center)
                <tr>
                    <td>{{ $center->center_name }}</td>
                    <td>
                        @php
                            $users = $center->users->filter(function ($user) {
                                return $user->roles->contains('name', 'child development worker');
                            });
                        @endphp

                        @if ($users->isNotEmpty())
                            @foreach ($users as $user)
                                {{ $user->firstname }} {{ $user->middlename }} {{ $user->lastname }}
                                {{ $user->extension_name }}
                            @endforeach
                        @else
                            No Worker Assigned
                        @endif
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    @foreach ($categories as $category)
                        @foreach ($sexLabels as $sex)
                            @foreach ($ages as $age)
                                @php
                                    $count = $wfaCounts[$center->id]['data'][$category][$sex][$age] ?? 0;
                                    $totals[$sex][$age] += $count;
                                @endphp
                                <td>{{ $count }}</td>
                            @endforeach
                        @endforeach
                    @endforeach

                    @foreach ($sexLabels as $sex)
                        @foreach ($ages as $age)
                            <td>{{ $totals[$sex][$age] }}</td>
                        @endforeach
                    @endforeach
                </tr>
                
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td rowspan="3"></td>
                <td></td>
                <td></td>
                @foreach ($categories as $category)
                    @foreach ($sexLabels as $sex)
                        @foreach ($ages as $age)
                            <td>{{ $agetotals[$category][$sex][$age] ?? 0 }}</td>
                        @endforeach
                    @endforeach
                @endforeach
                @foreach ($sexLabels as $sex)
                    @foreach ($ages as $age)
                        <td>{{ $totals[$sex][$age] }}</td>
                    @endforeach
                @endforeach
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td rowspan="2" colspan="2"></td>
                @foreach ($categories as $category)
                    @foreach ($sexLabels as $sex)
                        <td colspan="{{ count($ages) }}" >
                            {{ $ageTotalsPerCategory[$category][$sex] ?? 0 }}
                        </td>
                    @endforeach
                @endforeach
                @foreach ($sexLabels as $sex)
                    <td colspan="{{ count($ages) }}" >
                        {{ array_sum($totals[$sex]) }}
                    </td>
                @endforeach
            </tr>
            <tr>
                <td></td>
                <td></td>
                
                @php
                    $colspan = count($ages) * 2;
                    $totalAgesAndGender = 0;
                    foreach ($sexLabels as $sex) {
                        $totalAgesAndGender += array_sum($totals[$sex] ?? []);
                    }
                @endphp
                @foreach ($categories as $category)
                    <td colspan="{{ $colspan }}" >
                        {{ $totalsPerCategory[$category] ?? 0 }}
                    </td>
                @endforeach
                <td colspan="{{ $colspan }}" >
                    {{ $totalAgesAndGender }}
                </td>
            </tr>

        </tbody>
    </table>

</body>

</html>
