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

        .table-header{
            font-size: 12px;
            background-color: #8bbbdb;
        }

        .border-bg{
            background-color: #8bbbdb;
        }

        .border-bg-subhead{
            background-color: #edc5c9;
        }

        .columns{
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

        .totals{
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
    {{-- <input type="hidden" id="funded_center_name" name="funded_center_name" value="{{ $center->id}}"> --}}
    <div class="header">
        <p>Department of Social Welfare and Development, Field Office XI<br>
            Supplementary Feeding Program<br>
            {{ $cycle->name }} ( SY {{ $cycle->school_year }} )<br>
            <b>CONSOLIDATED NUTRITIONAL STATUS REPORT</b><br>
            <i>(Weight-for-Age)<br>Upon Entry</i></p>
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

    <table id='weight-for-age-upon-entry-table' class="table datatable weight-for-age-upon-entry-table w-full">
        <thead>
            <tr>
                <th rowspan="3">No.</th>
                <th class="border-bg" rowspan="3">Name of Child Development Center<br><i>(CDCs)</i></th>
                <th class="border-bg" rowspan="3">Name of Child Development Worker<br><i>(Surname, First name, M. I.)</i></th>
                <th class="border-bg" rowspan="3">Total Number Served</th>
                <th class="border-bg" rowspan="2" colspan="2">Total No of CDC/SNP Served</th>
                <th class="table-header" colspan="8">Normal</th>
                <th class="table-header" colspan="8">UW</th>
                <th class="table-header" colspan="8">SUW</th>
                <th class="table-header" colspan="8">OW</th>
                <th class="table-header" colspan="8">Total</th>
            </tr>
            <tr>
                <th class="border-bg" colspan="4">Male</th>
                <th class="border-bg" colspan="4">Female</th>
                <th class="border-bg" colspan="4">Male</th>
                <th class="border-bg" colspan="4">Female</th>
                <th class="border-bg" colspan="4">Male</th>
                <th class="border-bg" colspan="4">Female</th>
                <th class="border-bg" colspan="4">Male</th>
                <th class="border-bg" colspan="4">Female</th>
                <th class="border-bg" colspan="4">Male</th>
                <th class="border-bg" colspan="4">Female</th>
            </tr>
            <tr>
                <th class="border-bg-subhead">M</th>
                <th class="border-bg-subhead">F</th>
                <th class="border-bg-subhead">2</th>
                <th class="border-bg-subhead">3</th>
                <th class="border-bg-subhead">4</th>
                <th class="border-bg-subhead">5</th>
                <th class="border-bg-subhead">2</th>
                <th class="border-bg-subhead">3</th>
                <th class="border-bg-subhead">4</th>
                <th class="border-bg-subhead">5</th>
                <th class="border-bg-subhead">2</th>
                <th class="border-bg-subhead">3</th>
                <th class="border-bg-subhead">4</th>
                <th class="border-bg-subhead">5</th>
                <th class="border-bg-subhead">2</th>
                <th class="border-bg-subhead">3</th>
                <th class="border-bg-subhead">4</th>
                <th class="border-bg-subhead">5</th>
                <th class="border-bg-subhead">2</th>
                <th class="border-bg-subhead">3</th>
                <th class="border-bg-subhead">4</th>
                <th class="border-bg-subhead">5</th>
                <th class="border-bg-subhead">2</th>
                <th class="border-bg-subhead">3</th>
                <th class="border-bg-subhead">4</th>
                <th class="border-bg-subhead">5</th>
                <th class="border-bg-subhead">2</th>
                <th class="border-bg-subhead">3</th>
                <th class="border-bg-subhead">4</th>
                <th class="border-bg-subhead">5</th>
                <th class="border-bg-subhead">2</th>
                <th class="border-bg-subhead">3</th>
                <th class="border-bg-subhead">4</th>
                <th class="border-bg-subhead">5</th>
                <th class="border-bg-subhead">2</th>
                <th class="border-bg-subhead">3</th>
                <th class="border-bg-subhead">4</th>
                <th class="border-bg-subhead">5</th>
                <th class="border-bg-subhead">2</th>
                <th class="border-bg-subhead">3</th>
                <th class="border-bg-subhead">4</th>
                <th class="border-bg-subhead">5</th>
            </tr>
        </thead>
        <tbody class="weight-for-age-upon-entry-table">
            @php
                $count = 0;

                $totalServed = 0;
                $totalMale = 0;
                $totalFemale = 0;

                $normalAged2Male = 0;
                $underweightAged2Male = 0;
                $severelyUnderweightAged2Male = 0;
                $overweightAged2Male = 0;

                $normalAged2Female = 0;
                $underweightAged2Female = 0;
                $severelyUnderweightAged2Female = 0;
                $overweightAged2Female = 0;

                $normalAged3Male = 0;
                $underweightAged3Male = 0;
                $severelyUnderweightAged3Male = 0;
                $overweightAged3Male = 0;

                $normalAged3Female = 0;
                $underweightAged3Female = 0;
                $severelyUnderweightAged3Female = 0;
                $overweightAged3Female = 0;

                $normalAged4Male = 0;
                $underweightAged4Male = 0;
                $severelyUnderweightAged4Male = 0;
                $overweightAged4Male = 0;

                $normalAged4Female = 0;
                $underweightAged4Female = 0;
                $severelyUnderweightAged4Female = 0;
                $overweightAged4Female = 0;

                $normalAged5Male = 0;
                $underweightAged5Male = 0;
                $severelyUnderweightAged5Male = 0;
                $overweightAged5Male = 0;

                $normalAged5Female = 0;
                $underweightAged5Female = 0;
                $severelyUnderweightAged5Female = 0;
                $overweightAged5Female = 0;

                $totalAged2Male = 0;
                $totalAged3Male = 0;
                $totalAged4Male = 0;
                $totalAged5Male = 0;

                $totalAged2Female = 0;
                $totalAged3Female = 0;
                $totalAged4Female = 0;
                $totalAged5Female = 0;
            @endphp

            @foreach ($centers as $center)
                @php
                    $count += 1;

                    $totalServed += $totals[$center->id]['total_served'] ?? 0;
                    $totalMale += $totals[$center->id]['total_male'] ?? 0;
                    $totalFemale += $totals[$center->id]['total_female'] ?? 0;

                    $normalAged2Male += $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['normal']['male'] ?? 0;
                    $normalAged3Male += $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['normal']['male'] ?? 0;
                    $normalAged4Male += $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['normal']['male'] ?? 0;
                    $normalAged5Male += $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['normal']['male'] ?? 0;

                    $normalAged2Female +=
                        $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['normal']['female'] ?? 0;
                    $normalAged3Female +=
                        $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['normal']['female'] ?? 0;
                    $normalAged4Female +=
                        $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['normal']['female'] ?? 0;
                    $normalAged5Female +=
                        $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['normal']['female'] ?? 0;

                    $underweightAged2Male +=
                        $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['underweight']['male'] ?? 0;
                    $underweightAged3Male +=
                        $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['underweight']['male'] ?? 0;
                    $underweightAged4Male +=
                        $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['underweight']['male'] ?? 0;
                    $underweightAged5Male +=
                        $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['underweight']['male'] ?? 0;

                    $underweightAged2Female +=
                        $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['underweight']['female'] ?? 0;
                    $underweightAged3Female +=
                        $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['underweight']['female'] ?? 0;
                    $underweightAged4Female +=
                        $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['underweight']['female'] ?? 0;
                    $underweightAged5Female +=
                        $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['underweight']['female'] ?? 0;

                    $severelyUnderweightAged2Male +=
                        $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['severely_underweight']['male'] ?? 0;
                    $severelyUnderweightAged3Male +=
                        $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['severely_underweight']['male'] ?? 0;
                    $severelyUnderweightAged4Male +=
                        $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['severely_underweight']['male'] ?? 0;
                    $severelyUnderweightAged5Male +=
                        $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['severely_underweight']['male'] ?? 0;

                    $severelyUnderweightAged2Female +=
                        $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['severely_underweight']['female'] ?? 0;
                    $severelyUnderweightAged3Female +=
                        $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['severely_underweight']['female'] ?? 0;
                    $severelyUnderweightAged4Female +=
                        $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['severely_underweight']['female'] ?? 0;
                    $severelyUnderweightAged5Female +=
                        $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['severely_underweight']['female'] ?? 0;

                    $overweightAged2Male +=
                        $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['overweight']['male'] ?? 0;
                    $overweightAged3Male +=
                        $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['overweight']['male'] ?? 0;
                    $overweightAged4Male +=
                        $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['overweight']['male'] ?? 0;
                    $overweightAged5Male +=
                        $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['overweight']['male'] ?? 0;

                    $overweightAged2Female +=
                        $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['overweight']['female'] ?? 0;
                    $overweightAged3Female +=
                        $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['overweight']['female'] ?? 0;
                    $overweightAged4Female +=
                        $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['overweight']['female'] ?? 0;
                    $overweightAged5Female +=
                        $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['overweight']['female'] ?? 0;

                    $totalAged2Male += $totals[$center->id]['2']['male'] ?? 0;
                    $totalAged3Male += $totals[$center->id]['3']['male'] ?? 0;
                    $totalAged4Male += $totals[$center->id]['4']['male'] ?? 0;
                    $totalAged5Male += $totals[$center->id]['5']['male'] ?? 0;

                    $totalAged2Male += $totals[$center->id]['2']['female'] ?? 0;
                    $totalAged3Female += $totals[$center->id]['3']['female'] ?? 0;
                    $totalAged4Female += $totals[$center->id]['4']['female'] ?? 0;
                    $totalAged5Female += $totals[$center->id]['5']['female'] ?? 0;

                @endphp
                <tr>
                    <td>{{ $count }}</td>
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
                    <td>{{ $totals[$center->id]['total_served'] ?? 0 }}</td>
                    <td>{{ $totals[$center->id]['total_male'] ?? 0 }}</td>
                    <td>{{ $totals[$center->id]['total_female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['normal']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['normal']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['normal']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['normal']['male'] ?? 0 }}</td>

                    <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['normal']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['normal']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['normal']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['normal']['female'] ?? 0 }}</td>

                    <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['underweight']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['underweight']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['underweight']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['underweight']['male'] ?? 0 }}</td>

                    <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['underweight']['female'] ?? 0 }}
                    </td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['underweight']['female'] ?? 0 }}
                    </td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['underweight']['female'] ?? 0 }}
                    </td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['underweight']['female'] ?? 0 }}
                    </td>

                    <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['severely_underweight']['male'] ?? 0 }}
                    </td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['severely_underweight']['male'] ?? 0 }}
                    </td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['severely_underweight']['male'] ?? 0 }}
                    </td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['severely_underweight']['male'] ?? 0 }}
                    </td>

                    <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['severely_underweight']['female'] ?? 0 }}
                    </td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['severely_underweight']['female'] ?? 0 }}
                    </td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['severely_underweight']['female'] ?? 0 }}
                    </td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['severely_underweight']['female'] ?? 0 }}
                    </td>

                    <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['overweight']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['overweight']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['overweight']['male'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['overweight']['male'] ?? 0 }}</td>

                    <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_age']['overweight']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_age']['overweight']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_age']['overweight']['female'] ?? 0 }}</td>
                    <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_age']['overweight']['female'] ?? 0 }}</td>

                    <td>{{ $totals[$center->id]['2']['male'] ?? 0 }}</td>
                    <td>{{ $totals[$center->id]['3']['male'] ?? 0 }}</td>
                    <td>{{ $totals[$center->id]['4']['male'] ?? 0 }}</td>
                    <td>{{ $totals[$center->id]['5']['male'] ?? 0 }}</td>

                    <td>{{ $totals[$center->id]['2']['female'] ?? 0 }}</td>
                    <td>{{ $totals[$center->id]['3']['female'] ?? 0 }}</td>
                    <td>{{ $totals[$center->id]['4']['female'] ?? 0 }}</td>
                    <td>{{ $totals[$center->id]['5']['female'] ?? 0 }}</td>
                </tr>
            @endforeach

            <tr>
                <td class="totals" colspan="3">Total per Age Bracket ></td>
                <td rowspan="3">{{ $totalServed }}</td>
                <td>{{ $totalMale }}</td>
                <td>{{ $totalFemale }}</td>

                <td>{{ $normalAged2Male }}</td>
                <td>{{ $normalAged3Male }}</td>
                <td>{{ $normalAged4Male }}</td>
                <td>{{ $normalAged5Male }}</td>
                <td>{{ $normalAged2Female }}</td>
                <td>{{ $normalAged3Female }}</td>
                <td>{{ $normalAged4Female }}</td>
                <td>{{ $normalAged5Female }}</td>

                <td>{{ $underweightAged2Male }}</td>
                <td>{{ $underweightAged3Male }}</td>
                <td>{{ $underweightAged4Male }}</td>
                <td>{{ $underweightAged5Male }}</td>
                <td>{{ $underweightAged2Female }}</td>
                <td>{{ $underweightAged3Female }}</td>
                <td>{{ $underweightAged4Female }}</td>
                <td>{{ $underweightAged5Female }}</td>

                <td>{{ $severelyUnderweightAged2Male }}</td>
                <td>{{ $severelyUnderweightAged3Male }}</td>
                <td>{{ $severelyUnderweightAged4Male }}</td>
                <td>{{ $severelyUnderweightAged5Male }}</td>
                <td>{{ $severelyUnderweightAged2Female }}</td>
                <td>{{ $severelyUnderweightAged3Female }}</td>
                <td>{{ $severelyUnderweightAged4Female }}</td>
                <td>{{ $severelyUnderweightAged5Female }}</td>

                <td>{{ $overweightAged2Male }}</td>
                <td>{{ $overweightAged3Male }}</td>
                <td>{{ $overweightAged4Male }}</td>
                <td>{{ $overweightAged5Male }}</td>
                <td>{{ $overweightAged2Female }}</td>
                <td>{{ $overweightAged3Female }}</td>
                <td>{{ $overweightAged4Female }}</td>
                <td>{{ $overweightAged5Female }}</td>

                <td>{{ $totalAged2Male }}</td>
                <td>{{ $totalAged3Male }}</td>
                <td>{{ $totalAged4Male }}</td>
                <td>{{ $totalAged5Male }}</td>
                <td>{{ $totalAged2Female }}</td>
                <td>{{ $totalAged3Female }}</td>
                <td>{{ $totalAged4Female }}</td>
                <td>{{ $totalAged5Female }}</td>

            </tr>

            @php
                $totalServedMaleFemale = 0;

                $allNormalMale = 0;
                $allNormalFemale = 0;

                $allUnderweightMale = 0;
                $allUnderweighteFemale = 0;

                $allSeverelyUnderweightMale = 0;
                $allSeverelyUnderweightFemale = 0;

                $allOverweightMale = 0;
                $allOverweightFemale = 0;

                $allMale = 0;
                $allFemale = 0;

                $allNormal = 0;
                $allUnderweight = 0;
                $allSeverelyUnderweight = 0;
                $allOverweight = 0;
                $allTotal = 0;

                $totalServedMaleFemale = $totalMale + $totalFemale;

                $allNormalMale = $normalAged2Male + $normalAged3Male + $normalAged4Male + $normalAged5Male;
                $allNormalFemale = $normalAged2Female + $normalAged3Female + $normalAged4Female + $normalAged5Female;

                $allUnderweightMale =
                    $underweightAged2Male + $underweightAged3Male + $underweightAged4Male + $underweightAged5Male;
                $allUnderweighteFemale =
                    $underweightAged2Female +
                    $underweightAged3Female +
                    $underweightAged4Female +
                    $underweightAged5Female;

                $allSeverelyUnderweightMale =
                    $severelyUnderweightAged2Male +
                    $severelyUnderweightAged3Male +
                    $severelyUnderweightAged4Male +
                    $severelyUnderweightAged5Male;
                $allSeverelyUnderweightFemale =
                    $severelyUnderweightAged2Female +
                    $severelyUnderweightAged3Female +
                    $severelyUnderweightAged4Female +
                    $severelyUnderweightAged5Female;

                $allOverweightMale =
                    $overweightAged2Male + $overweightAged3Male + $overweightAged4Male + $overweightAged5Male;
                $allOverweightFemale =
                    $overweightAged2Female + $overweightAged3Female + $overweightAged4Female + $overweightAged5Female;

                $allMale = $totalAged2Male + $totalAged3Male + $totalAged4Male + $totalAged5Male;
                $allFemale = $totalAged2Female + $totalAged3Female + $totalAged4Female + $totalAged5Female;

                $allNormal = $allNormalMale + $allNormalFemale;
                $allUnderweight = $allUnderweightMale + $allUnderweighteFemale;
                $allSeverelyUnderweight = $allSeverelyUnderweightMale + $allSeverelyUnderweightFemale;
                $allOverweight = $allOverweightMale + $allOverweightFemale;
                $allTotal = $allMale + $allFemale;
            @endphp

            <tr>
                <td class="totals" colspan="3">Total Male/Female ></td>
                <td rowspan="2" colspan="2">{{ $totalServedMaleFemale }}</td>
                <td colspan="4">{{ $allNormalMale }}</td>
                <td colspan="4">{{ $allNormalFemale }}</td>
                <td colspan="4">{{ $allUnderweightMale }}</td>
                <td colspan="4">{{ $allUnderweighteFemale }}</td>
                <td colspan="4">{{ $allSeverelyUnderweightMale }}</td>
                <td colspan="4">{{ $allSeverelyUnderweightFemale }}</td>
                <td colspan="4">{{ $allOverweightMale }}</td>
                <td colspan="4">{{ $allOverweightFemale }}</td>
                <td colspan="4">{{ $allMale }}</td>
                <td colspan="4">{{ $allFemale }}</td>
            </tr>

            <tr>
                <td class="totals" colspan="3">Total Child Beneficiaries ></td>
                <td colspan="8">{{ $allNormal }}</td>
                <td colspan="8">{{ $allUnderweight }}</td>
                <td colspan="8">{{ $allSeverelyUnderweight }}</td>
                <td colspan="8">{{ $allOverweight }}</td>
                <td colspan="8">{{ $allTotal }}</td>
            </tr>
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
                    <p>
                        <u>
                            {{ auth()->user()->full_name }}
                        </u>
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
