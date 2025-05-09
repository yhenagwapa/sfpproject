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

        .td-width {
            width: 30px;
        }

        .p {
            margin: 5px 0;
        }

        .undernourished-after-120-table {
            font-size: 9px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-collapse: collapse;
        }

        .undernourished-after-120-table th, .undernourished-after-120-table td {
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
        <p><b>Summary of Undernourished Children, Ethnicity, $Ps, Deworming & Vitamin A</b></p>
        <p><i>After 120 Feedings</i></p>
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

    <table id='undernourished-after-120-table' class="table datatable undernourished-after-120-table w-full">
        <thead class="border bg-gray-200">
            <tr>
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
        <tbody class="undernourished-after-120-table text-xs">
            @forelse ($centers as $center)
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
                                {{ $user->firstname }} {{ $user->middlename }} {{ $user->lastname }} {{ $user->extension_name }}
                            @endforeach
                        @else
                            No Worker Assigned
                        @endif
                    </td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['2_years_old']['male'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['2_years_old']['female'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['3_years_old']['male'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['3_years_old']['female'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['4_years_old']['male'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['4_years_old']['female'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['5_years_old']['male'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['5_years_old']['female'] ?? 0 }}</td>

                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['indigenous_people']['male'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['indigenous_people']['female'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['pantawid']['male'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['pantawid']['female'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['pwd']['male'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['pwd']['female'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['lactose_intolerant']['male'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['lactose_intolerant']['female'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['child_of_solo_parent']['male'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['child_of_solo_parent']['female'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['dewormed']['male'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['dewormed']['female'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['vitamin_a']['male'] ?? 0 }}</td>
                    <td>{{ $exitAgeGroupsPerCenter[$center->id]['vitamin_a ']['female'] ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center" colspan="20">
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

    <footer>

    </footer>

</body>
</html>
