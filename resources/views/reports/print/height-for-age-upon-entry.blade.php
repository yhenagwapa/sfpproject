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

        .height-for-age-upon-entry-table {
            font-size: 10px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-collapse: collapse;
        }

        .height-for-age-upon-entry-table th,
        .height-for-age-upon-entry-table td {
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
    </style>
</head>

<body>
    {{-- <input type="hidden" id="funded_center_name" name="funded_center_name" value="{{ $center->id}}"> --}}
    <div class="header">
        <p>Department of Social Welfare and Development, Field Office XI<br>
            Supplementary Feeding Program<br>
            {{ $cycle->name }} ( SY {{ $cycle->school_year_from }} - {{ $cycle->school_year_to }} )<br>
            <b>CONSOLIDATED NUTRITIONAL STATUS REPORT</b><br>
            <i>(Height-for-Age)<br>Upon Entry</i>
        </p>
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
    @php
        $chunks = $centers->chunk(2);
        $count = 0;

        $totalServed = 0;
        $totalMale = 0;
        $totalFemale = 0;

        $normalAged2Male = 0;
        $stuntedAged2Male = 0;
        $severelyStuntedAged2Male = 0;
        $tallAged2Male = 0;

        $normalAged2Female = 0;
        $stuntedAged2Female = 0;
        $severelyStuntedAged2Female = 0;
        $tallAged2Female = 0;

        $normalAged3Male = 0;
        $stuntedAged3Male = 0;
        $severelyStuntedAged3Male = 0;
        $tallAged3Male = 0;

        $normalAged3Female = 0;
        $stuntedAged3Female = 0;
        $severelyStuntedAged3Female = 0;
        $tallAged3Female = 0;

        $normalAged4Male = 0;
        $stuntedAged4Male = 0;
        $severelyStuntedAged4Male = 0;
        $tallAged4Male = 0;

        $normalAged4Female = 0;
        $stuntedAged4Female = 0;
        $severelyStuntedAged4Female = 0;
        $tallAged4Female = 0;

        $normalAged5Male = 0;
        $stuntedAged5Male = 0;
        $severelyStuntedAged5Male = 0;
        $tallAged5Male = 0;

        $normalAged5Female = 0;
        $stuntedAged5Female = 0;
        $severelyStuntedAged5Female = 0;
        $tallAged5Female = 0;

        $totalAged2Male = 0;
        $totalAged3Male = 0;
        $totalAged4Male = 0;
        $totalAged5Male = 0;

        $totalAged2Female = 0;
        $totalAged3Female = 0;
        $totalAged4Female = 0;
        $totalAged5Female = 0;
    @endphp

    @foreach ($chunks as $chunk)
        <table id='height-for-age-upon-entry-table' class="table datatable height-for-age-upon-entry-table w-full">
            <thead class="border bg-gray-200">
                <tr>
                    <th rowspan="3">No.</th>
                    <th class="border-bg" rowspan="3">Name of Child Development Center</th>
                    <th class="border-bg" rowspan="3">Name of Child Development Worker</th>
                    <th class="border-bg" rowspan="3">Total Number Served</th>
                    <th class="border-bg" rowspan="2" colspan="2">Total No of CDC/SNP Served</th>
                    <th class="table-header" colspan="8">Normal</th>
                    <th class="table-header" colspan="8">Stunted</th>
                    <th class="table-header" colspan="8">Severly Stunted</th>
                    <th class="table-header" colspan="8">Tall</th>
                    <th class="table-header" colspan="8">Total</th>

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

            <tbody class="height-for-age-upon-entry-table text-xs">


                @foreach ($chunk as $center)
                    @php
                        $count += 1;
                        $totalServed += $totals[$center->id]['total_served'] ?? 0;
                        $totalMale += $totals[$center->id]['total_male'] ?? 0;
                        $totalFemale += $totals[$center->id]['total_female'] ?? 0;

                        $normalAged2Male +=
                            $ageGroupsPerCenter[$center->id]['2']['height_for_age']['normal']['male'] ?? 0;
                        $normalAged3Male +=
                            $ageGroupsPerCenter[$center->id]['3']['height_for_age']['normal']['male'] ?? 0;
                        $normalAged4Male +=
                            $ageGroupsPerCenter[$center->id]['4']['height_for_age']['normal']['male'] ?? 0;
                        $normalAged5Male +=
                            $ageGroupsPerCenter[$center->id]['5']['height_for_age']['normal']['male'] ?? 0;

                        $normalAged2Female +=
                            $ageGroupsPerCenter[$center->id]['2']['height_for_age']['normal']['female'] ?? 0;
                        $normalAged3Female +=
                            $ageGroupsPerCenter[$center->id]['3']['height_for_age']['normal']['female'] ?? 0;
                        $normalAged4Female +=
                            $ageGroupsPerCenter[$center->id]['4']['height_for_age']['normal']['female'] ?? 0;
                        $normalAged5Female +=
                            $ageGroupsPerCenter[$center->id]['5']['height_for_age']['normal']['female'] ?? 0;

                        $stuntedAged2Male +=
                            $ageGroupsPerCenter[$center->id]['2']['height_for_age']['stunted']['male'] ?? 0;
                        $stuntedAged3Male +=
                            $ageGroupsPerCenter[$center->id]['3']['height_for_age']['stunted']['male'] ?? 0;
                        $stuntedAged4Male +=
                            $ageGroupsPerCenter[$center->id]['4']['height_for_age']['stunted']['male'] ?? 0;
                        $stuntedAged5Male +=
                            $ageGroupsPerCenter[$center->id]['5']['height_for_age']['stunted']['male'] ?? 0;

                        $stuntedAged2Female +=
                            $ageGroupsPerCenter[$center->id]['2']['height_for_age']['stunted']['female'] ?? 0;
                        $stuntedAged3Female +=
                            $ageGroupsPerCenter[$center->id]['3']['height_for_age']['stunted']['female'] ?? 0;
                        $stuntedAged4Female +=
                            $ageGroupsPerCenter[$center->id]['4']['height_for_age']['stunted']['female'] ?? 0;
                        $stuntedAged5Female +=
                            $ageGroupsPerCenter[$center->id]['5']['height_for_age']['stunted']['female'] ?? 0;

                        $severelyStuntedAged2Male +=
                            $ageGroupsPerCenter[$center->id]['2']['height_for_age']['severely_stunted']['male'] ?? 0;
                        $severelyStuntedAged3Male +=
                            $ageGroupsPerCenter[$center->id]['3']['height_for_age']['severely_stunted']['male'] ?? 0;
                        $severelyStuntedAged4Male +=
                            $ageGroupsPerCenter[$center->id]['4']['height_for_age']['severely_stunted']['male'] ?? 0;
                        $severelyStuntedAged5Male +=
                            $ageGroupsPerCenter[$center->id]['5']['height_for_age']['severely_stunted']['male'] ?? 0;

                        $severelyStuntedAged2Female +=
                            $ageGroupsPerCenter[$center->id]['2']['height_for_age']['severely_stunted']['female'] ?? 0;
                        $severelyStuntedAged3Female +=
                            $ageGroupsPerCenter[$center->id]['3']['height_for_age']['severely_stunted']['female'] ?? 0;
                        $severelyStuntedAged4Female +=
                            $ageGroupsPerCenter[$center->id]['4']['height_for_age']['severely_stunted']['female'] ?? 0;
                        $severelyStuntedAged5Female +=
                            $ageGroupsPerCenter[$center->id]['5']['height_for_age']['severely_stunted']['female'] ?? 0;

                        $tallAged2Male += $ageGroupsPerCenter[$center->id]['2']['height_for_age']['tall']['male'] ?? 0;
                        $tallAged3Male += $ageGroupsPerCenter[$center->id]['3']['height_for_age']['tall']['male'] ?? 0;
                        $tallAged4Male += $ageGroupsPerCenter[$center->id]['4']['height_for_age']['tall']['male'] ?? 0;
                        $tallAged5Male += $ageGroupsPerCenter[$center->id]['5']['height_for_age']['tall']['male'] ?? 0;

                        $tallAged2Female +=
                            $ageGroupsPerCenter[$center->id]['2']['height_for_age']['tall']['female'] ?? 0;
                        $tallAged3Female +=
                            $ageGroupsPerCenter[$center->id]['3']['height_for_age']['tall']['female'] ?? 0;
                        $tallAged4Female +=
                            $ageGroupsPerCenter[$center->id]['4']['height_for_age']['tall']['female'] ?? 0;
                        $tallAged5Female +=
                            $ageGroupsPerCenter[$center->id]['5']['height_for_age']['tall']['female'] ?? 0;

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
                                    {{ $user->firstname }} {{ $user->middlename }} {{ $user->lastname }}
                                    {{ $user->extension_name }}
                                @endforeach
                            @else
                                No Worker Assigned
                            @endif
                        </td>
                        <td>{{ $totals[$center->id]['total_served'] ?? 0 }}</td>
                        <td>{{ $totals[$center->id]['total_male'] ?? 0 }}</td>
                        <td>{{ $totals[$center->id]['total_female'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['2']['height_for_age']['normal']['male'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['3']['height_for_age']['normal']['male'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['4']['height_for_age']['normal']['male'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['5']['height_for_age']['normal']['male'] ?? 0 }}</td>

                        <td>{{ $ageGroupsPerCenter[$center->id]['2']['height_for_age']['normal']['female'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['3']['height_for_age']['normal']['female'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['4']['height_for_age']['normal']['female'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['5']['height_for_age']['normal']['female'] ?? 0 }}</td>

                        <td>{{ $ageGroupsPerCenter[$center->id]['2']['height_for_age']['stunted']['male'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['3']['height_for_age']['stunted']['male'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['4']['height_for_age']['stunted']['male'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['5']['height_for_age']['stunted']['male'] ?? 0 }}</td>

                        <td>{{ $ageGroupsPerCenter[$center->id]['2']['height_for_age']['stunted']['female'] ?? 0 }}
                        </td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['3']['height_for_age']['stunted']['female'] ?? 0 }}
                        </td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['4']['height_for_age']['stunted']['female'] ?? 0 }}
                        </td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['5']['height_for_age']['stunted']['female'] ?? 0 }}
                        </td>

                        <td>{{ $ageGroupsPerCenter[$center->id]['2']['height_for_age']['severely_stunted']['male'] ?? 0 }}
                        </td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['3']['height_for_age']['severely_stunted']['male'] ?? 0 }}
                        </td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['4']['height_for_age']['severely_stunted']['male'] ?? 0 }}
                        </td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['5']['height_for_age']['severely_stunted']['male'] ?? 0 }}
                        </td>

                        <td>{{ $ageGroupsPerCenter[$center->id]['2']['height_for_age']['severely_stunted']['female'] ?? 0 }}
                        </td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['3']['height_for_age']['severely_stunted']['female'] ?? 0 }}
                        </td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['4']['height_for_age']['severely_stunted']['female'] ?? 0 }}
                        </td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['5']['height_for_age']['severely_stunted']['female'] ?? 0 }}
                        </td>

                        <td>{{ $ageGroupsPerCenter[$center->id]['2']['height_for_age']['tall']['male'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['3']['height_for_age']['tall']['male'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['4']['height_for_age']['tall']['male'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['5']['height_for_age']['tall']['male'] ?? 0 }}</td>

                        <td>{{ $ageGroupsPerCenter[$center->id]['2']['height_for_age']['tall']['female'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['3']['height_for_age']['tall']['female'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['4']['height_for_age']['tall']['female'] ?? 0 }}</td>
                        <td>{{ $ageGroupsPerCenter[$center->id]['5']['height_for_age']['tall']['female'] ?? 0 }}</td>

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

                @if (!$loop->last)
            </tbody>
        </table>
        <div style="page-break-after: always;"></div>
        <table>
            <tbody>
    @endif
    @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td class="text-right" colspan="3">Total per Age Bracket ></td>
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

            <td>{{ $stuntedAged2Male }}</td>
            <td>{{ $stuntedAged3Male }}</td>
            <td>{{ $stuntedAged4Male }}</td>
            <td>{{ $stuntedAged5Male }}</td>
            <td>{{ $stuntedAged2Female }}</td>
            <td>{{ $stuntedAged3Female }}</td>
            <td>{{ $stuntedAged4Female }}</td>
            <td>{{ $stuntedAged5Female }}</td>

            <td>{{ $severelyStuntedAged2Male }}</td>
            <td>{{ $severelyStuntedAged3Male }}</td>
            <td>{{ $severelyStuntedAged4Male }}</td>
            <td>{{ $severelyStuntedAged5Male }}</td>
            <td>{{ $severelyStuntedAged2Female }}</td>
            <td>{{ $severelyStuntedAged3Female }}</td>
            <td>{{ $severelyStuntedAged4Female }}</td>
            <td>{{ $severelyStuntedAged5Female }}</td>

            <td>{{ $tallAged2Male }}</td>
            <td>{{ $tallAged3Male }}</td>
            <td>{{ $tallAged4Male }}</td>
            <td>{{ $tallAged5Male }}</td>
            <td>{{ $tallAged2Female }}</td>
            <td>{{ $tallAged3Female }}</td>
            <td>{{ $tallAged4Female }}</td>
            <td>{{ $tallAged5Female }}</td>

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

            $allStuntedMale = 0;
            $allStuntedeFemale = 0;

            $allSeverelyStuntedMale = 0;
            $allSeverelyStuntedFemale = 0;

            $allTallMale = 0;
            $allTallFemale = 0;

            $allMale = 0;
            $allFemale = 0;

            $allNormal = 0;
            $allStunted = 0;
            $allSeverelyStunted = 0;
            $allTall = 0;
            $allTotal = 0;

            $totalServedMaleFemale = $totalMale + $totalFemale;

            $allNormalMale = $normalAged2Male + $normalAged3Male + $normalAged4Male + $normalAged5Male;
            $allNormalFemale = $normalAged2Female + $normalAged3Female + $normalAged4Female + $normalAged5Female;

            $allStuntedMale = $stuntedAged2Male + $stuntedAged3Male + $stuntedAged4Male + $stuntedAged5Male;
            $allStuntedeFemale = $stuntedAged2Female + $stuntedAged3Female + $stuntedAged4Female + $stuntedAged5Female;

            $allSeverelyStuntedMale =
                $severelyStuntedAged2Male +
                $severelyStuntedAged3Male +
                $severelyStuntedAged4Male +
                $severelyStuntedAged5Male;
            $allSeverelyStuntedFemale =
                $severelyStuntedAged2Female +
                $severelyStuntedAged3Female +
                $severelyStuntedAged4Female +
                $severelyStuntedAged5Female;

            $allTallMale = $tallAged2Male + $tallAged3Male + $tallAged4Male + $tallAged5Male;
            $allTallFemale = $tallAged2Female + $tallAged3Female + $tallAged4Female + $tallAged5Female;

            $allMale = $totalAged2Male + $totalAged3Male + $totalAged4Male + $totalAged5Male;
            $allFemale = $totalAged2Female + $totalAged3Female + $totalAged4Female + $totalAged5Female;

            $allNormal = $allNormalMale + $allNormalFemale;
            $allStunted = $allStuntedMale + $allStuntedeFemale;
            $allSeverelyStunted = $allSeverelyStuntedMale + $allSeverelyStuntedFemale;
            $allTall = $allTallMale + $allTallFemale;
            $allTotal = $allMale + $allFemale;
        @endphp

        <tr>
            <td class="text-right" colspan="3">Total Male/Female ></td>
            <td rowspan="2" colspan="2">{{ $totalServedMaleFemale }}</td>
            <td colspan="4">{{ $allNormalMale }}</td>
            <td colspan="4">{{ $allNormalFemale }}</td>
            <td colspan="4">{{ $allStuntedMale }}</td>
            <td colspan="4">{{ $allStuntedeFemale }}</td>
            <td colspan="4">{{ $allSeverelyStuntedMale }}</td>
            <td colspan="4">{{ $allSeverelyStuntedFemale }}</td>
            <td colspan="4">{{ $allTallMale }}</td>
            <td colspan="4">{{ $allTallFemale }}</td>
            <td colspan="4">{{ $allMale }}</td>
            <td colspan="4">{{ $allFemale }}</td>
        </tr>

        <tr>
            <td class="text-right" colspan="3">Total Child Beneficiaries ></td>
            <td colspan="8">{{ $allNormal }}</td>
            <td colspan="8">{{ $allStunted }}</td>
            <td colspan="8">{{ $allSeverelyStunted }}</td>
            <td colspan="8">{{ $allTall }}</td>
            <td colspan="8">{{ $allTotal }}</td>
        </tr>
    </tfoot>
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
