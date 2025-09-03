<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('https://172.31.176.49/sfpproject/public') }}">

    <title>Height for Age</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('img/SFP-LOGO-2024.png') }}" rel="icon">
    @includeIf('reports.style')
</head>

<body>
    {{-- <input type="hidden" id="funded_center_name" name="funded_center_name" value="{{ $center->id}}"> --}}
    <div class="header">
        <p>Department of Social Welfare and Development, Field Office XI<br>
            Supplementary Feeding Program<br>
            {{ $cycle->name }} ( CY {{ $cycle->school_year_from }} )<br>
            <b>CONSOLIDATED NUTRITIONAL STATUS REPORT</b><br>
            @if($nsType == 'upon-entry')
                <i>(Height-for-Age)<br>Upon Entry</i>
            @else
                <i>(Height-for-Age)<br>After 120 Feedings</i>
            @endif
        </p>
        <br>
    </div>

    <div class="header-section">
        <table class="table">
            <tr>
                <td>
                    <p>Province: <u>{{ $province ? $province->implode(', ') : 'All Provinces' }}</u></p>
                    <p>
                        City / Municipality:
                        <u>
                            @if(auth()->user()->hasRole('sfp coordinator'))
                                {{ $adminDistrict ? $adminDistrict->implode(', ') : null }}
                            @endif
                            {{ $city ? $city->implode(', ') : 'All Cities' }}
                        </u>
                    </p>
                </td>
            </tr>
        </table>
    </div>
    @php
        $count = 0;
        $no = 0;
    @endphp
    <div style="margin-bottom: 50px;">
        <table id='nutritional-status-table' class="table datatable nutritional-status-table w-full">
            <thead>
                <tr>
                    <th class="border-bg" rowspan="3">No.</th>
                    <th class="border-bg" rowspan="3">Name of Child Development Center</th>
                    <th class="border-bg" rowspan="3">Name of Child Development Worker</th>
                    <th class="border-bg" rowspan="3">Total Number Served</th>
                    <th class="border-bg" rowspan="2" colspan="2">Total No of CDC/SNP Served</th>
                    @foreach ($categories as $category)
                        <th class="table-header" colspan="8">{{ $category }}</th>
                    @endforeach
                    <th class="border-bg" colspan="8">Total</th>
                </tr>
                <tr>
                    @foreach ($categories as $category)
                        @foreach ($sexLabels as $sex)
                            <th class="border-bg" colspan="4">{{ $sex }}</th>
                        @endforeach
                    @endforeach
                    <th class="border-bg" colspan="4">M</th>
                    <th class="border-bg" colspan="4">F</th>
                </tr>
                <tr>
                    @foreach ($sexLabels as $sex)
                        <th class="border-bg-subhead">{{ $sex }}</th>
                    @endforeach
                    @foreach ($categories as $category)
                        @foreach ($sexLabels as $sex)
                            @foreach ([2, 3, 4, 5] as $age)
                                <th class="border-bg-subhead">{{ $age }}</th>
                            @endforeach
                        @endforeach
                    @endforeach
                    @foreach ([2, 3, 4, 5] as $age)
                        <th class="border-bg-subhead">{{ $age }}</th>
                    @endforeach
                    @foreach ([2, 3, 4, 5] as $age)
                        <th class="border-bg-subhead">{{ $age }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody class="nutritional-status-table">
                @php
                    $totalMale = 0;
                    $totalFemale = 0;
                    $overallTotal = 0;
                    $maleAges = 0;
                    $femaleAges = 0;
                    $allAges = 0;
                    $overAll = 0;
                    $overAllM = 0;
                    $overAllF = 0;
                    $perAgeTotal = 0;
                    $perSexTotal = 0;
                    $perCategory = 0;
                @endphp

                @foreach ($centers as $center)
                    @php
                        $no += 1;
                        $totals = [
                            'M' => [2 => 0, 3 => 0, 4 => 0, 5 => 0],
                            'F' => [2 => 0, 3 => 0, 4 => 0, 5 => 0],
                        ];
                    @endphp
                    <tr>
                        <td>{{ $no }}</td>
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

                        @php
                            $overallTotal = $hfaCounts[$center->id]['total_children'] ?? 0;
                            $totalMale = $hfaCounts[$center->id]['total_male'] ?? 0;
                            $totalFemale = $hfaCounts[$center->id]['total_female'] ?? 0;
                        @endphp

                        <td>
                            {{ $overallTotal }}
                            @php
                                $overAll += $overallTotal;
                            @endphp
                        </td>
                        <td>
                            {{ $totalMale }}
                            @php
                                $overAllM += $totalMale;
                            @endphp
                        </td>
                        <td>
                            {{ $totalFemale }}
                            @php
                                $overAllF += $totalFemale;
                            @endphp
                        </td>

                        @foreach ($categories as $category)
                            @foreach ($sexLabels as $sex)
                                @foreach ($ages as $age)
                                    @php
                                        $count = $hfaCounts[$center->id]['data'][$category][$sex][$age] ?? 0;
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
                    <td colspan="3">TOTAL PER AGE BRACKET ></td>
                    <td class="centered" rowspan="3">{{ $overAll }}</td>
                    <td class="centered">{{ $overAllM }}</td>
                    <td>{{ $overAllF }}</td>

                    @foreach ($categories as $category)
                        @foreach ($sexLabels as $sex)
                            @foreach ($ages as $age)
                                <td>{{ $agetotals[$category][$sex][$age] }}</td>
                            @endforeach
                        @endforeach
                    @endforeach

                    @foreach ($sexLabels as $sex)
                        @foreach ($ages as $age)
                            <td>{{ $sexAgeTotals[$sex][$age] }}</td>
                        @endforeach
                    @endforeach
                </tr>
                <tr>
                    @php
                        $totalAllGender = $overAllM + $overAllF;
                    @endphp

                    <td colspan="3">TOTAL MALE/FEMALE ></td>
                    <td class="centered" rowspan="2" colspan="2">{{ $totalAllGender }}</td>

                    @foreach ($categories as $category)
                        @foreach ($sexLabels as $sex)
                            <td class="centered" colspan="{{ count($ages) }}">
                                {{ $ageTotalsPerCategory[$category][$sex] ?? 0 }}
                            </td>
                        @endforeach
                    @endforeach

                    @foreach ($sexLabels as $sex)
                        <td colspan="{{ count($ages) }}">{{ $totalPerSex[$sex] }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td colspan="3">TOTAL CHILD BENEFICIARIES > </td>
                    @php
                        $colspan = count($ages) * count($sexLabels);
                    @endphp

                    @foreach ($categories as $category)
                        <td class="centered" colspan="{{ $colspan }}">
                            {{ $totalsPerCategory[$category] ?? 0 }}
                        </td>
                    @endforeach

                    <td class="centered" colspan="{{ $colspan }}">
                        {{ $overAll }}
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
    <div class="footer-section">
        <table class="footer-table">
            <tr></tr>
            <tr></tr>
            <tr>
                <td>
                    <br>
                    <br>
                    <p>Prepared by:</p>
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
                    <p>Noted by:</p>
                    <br>
                    <br>
                    <p>______________________________________</p>
                    <p>C/MSWDO/District Head</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        SFP Forms 4.1 (c/o SFP Focal Person)
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
