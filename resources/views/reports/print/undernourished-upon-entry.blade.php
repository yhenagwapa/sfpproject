<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('https://172.31.176.49/sfpproject/public') }}">

    <title>Summary of Undernourished Children</title>

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

        .td-width {
            width: 30px;
        }

        .p {
            margin: 5px 0;
        }

        .undernourished-upon-entry-table {
            font-size: 9px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-collapse: collapse;
        }

        .undernourished-upon-entry-table th, .undernourished-upon-entry-table td {
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
        <p><b>Summary of Undernourished Children, Ethnicity, 4Ps, Deworming & Vitamin A</b></p>
        <p><i>Upon Entry</i></p>
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

    <table id='undernourished-upon-entry-table' class="table datatable undernourished-upon-entry-table w-full">
        <thead class="border bg-gray-200">
            <tr>
                <th rowspan="3">No.</th>
                <th rowspan="3">Name of Child Development Center</th>
                <th rowspan="3">Name of Child Development Worker</th>
                <th colspan="8">Summary of Undernourished Children</th>
                <th colspan="10">Beneficiaries Profile</th>
                <th colspan="4">Deworming & Vitamin A Record</th>
            </tr>
            <tr>
                <th colspan="2">2 y/o</th>
                <th colspan="2">3 y/o</th>
                <th colspan="2">4 y/o</th>
                <th colspan="2">5 y/o</th>
                <th class="td-width" colspan="2">No. of Ethnic Children</th>
                <th class="td-width" colspan="2">No. of 4Ps Children</th>
                <th class="td-width" colspan="2">No. of PWD</th>
                <th class="td-width" colspan="2">No. of Children with Lactose Intolerance</th>
                <th class="td-width" colspan="2">No. of Children with Solo Parent</th>
                <th class="td-width" colspan="2">No. of Dewormed Children</th>
                <th class="td-width" colspan="2">No. of Children with Vit. A Supp.</th>
            </tr>
            <tr>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
            </tr>
        </thead>
        <tbody class="undernourished-upon-entry-table text-xs">
            @forelse ($centerNames as $center)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $center->center_name }}</td>
                    <td>
                        @php
                            $users = $center->users->filter(function ($user) {
                                return $user->roles->contains('name', 'child development worker');
                            });
                        @endphp

                        @if ($users->isNotEmpty())
                            @foreach ($users as $user)
                                {{ $user->firstname }} {{ $user->middlename }} {{ $user->lastname }} {{ $user->extension_name }}
                            @endforeach
                        @else
                            No Worker Assigned
                        @endif
                    </td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['2_years_old']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['2_years_old']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['3_years_old']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['3_years_old']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['4_years_old']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['4_years_old']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['5_years_old']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['5_years_old']['female'] ?? 0 }}</td>

                    <td>{{ $ageGroupsPerCenter[$center->id]['indigenous_people']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['indigenous_people']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['pantawid']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['pantawid']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['pwd']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['pwd']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['lactose_intolerant']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['lactose_intolerant']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['child_of_solo_parent']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['child_of_solo_parent']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['dewormed']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['dewormed']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['vitamin_a']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['vitamin_a']['female'] ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center" colspan="25">
                        No Data found
                    </td>
                </tr>
            @endforelse
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
                    <p>Prepared by:</p>
                    <br>
                    <br>
                    <p>______________________________________</p>
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
        SFP Forms 5 (c/o SFP Focal Person)
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
