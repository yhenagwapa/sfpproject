<table id='weight-for-height-after-120-table' class="table datatable mt-3 text-xs text-center">
    <thead class="border bg-gray-200">
        <tr>
            <th class="border border-white w-30" rowspan="3">No.</th>
            <th class="border border-white w-30" rowspan="3">Name of Child Development Center</th>
            <th class="border border-white w-30" rowspan="3">Name of Child Development Worker</th>
            <th class="border border-white w-10" rowspan="3">Total Number Served</th>
            <th class="border border-white w-10" rowspan="2" colspan="2">Total No of CDC/SNP Served</th>
            <th class="border border-white" colspan="8">Normal</th>
            <th class="border border-white" colspan="8">Wasted</th>
            <th class="border border-white" colspan="8">Severly Wasted</th>
            <th class="border border-white" colspan="8">Overweight</th>
            <th class="border border-white" colspan="8">Obese</th>
            <th class="border border-white" colspan="8">Total</th>
        <tr>
            <th class="border border-white w-2" colspan="4">Male</th>
            <th class="border border-white w-2" colspan="4">Female</th>
            <th class="border border-white w-2" colspan="4">Male</th>
            <th class="border border-white w-2" colspan="4">Female</th>
            <th class="border border-white w-2" colspan="4">Male</th>
            <th class="border border-white w-2" colspan="4">Female</th>
            <th class="border border-white w-2" colspan="4">Male</th>
            <th class="border border-white w-2" colspan="4">Female</th>
            <th class="border border-white w-2" colspan="4">Male</th>
            <th class="border border-white w-2" colspan="4">Female</th>
            <th class="border border-white w-2" colspan="4">Male</th>
            <th class="border border-white w-2" colspan="4">Female</th>
        </tr>
        <tr>
            <th class="border border-white">M</th>
            <th class="border border-white">F</th>
            <th class="border border-white">2</th>
            <th class="border border-white">3</th>
            <th class="border border-white">4</th>
            <th class="border border-white">5</th>
            <th class="border border-white">2</th>
            <th class="border border-white">3</th>
            <th class="border border-white">4</th>
            <th class="border border-white">5</th>
            <th class="border border-white">2</th>
            <th class="border border-white">3</th>
            <th class="border border-white">4</th>
            <th class="border border-white">5</th>
            <th class="border border-white">2</th>
            <th class="border border-white">3</th>
            <th class="border border-white">4</th>
            <th class="border border-white">5</th>
            <th class="border border-white">2</th>
            <th class="border border-white">3</th>
            <th class="border border-white">4</th>
            <th class="border border-white">5</th>
            <th class="border border-white">2</th>
            <th class="border border-white">3</th>
            <th class="border border-white">4</th>
            <th class="border border-white">5</th>
            <th class="border border-white">2</th>
            <th class="border border-white">3</th>
            <th class="border border-white">4</th>
            <th class="border border-white">5</th>
            <th class="border border-white">2</th>
            <th class="border border-white">3</th>
            <th class="border border-white">4</th>
            <th class="border border-white">5</th>
            <th class="border border-white">2</th>
            <th class="border border-white">3</th>
            <th class="border border-white">4</th>
            <th class="border border-white">5</th>
            <th class="border border-white">2</th>
            <th class="border border-white">3</th>
            <th class="border border-white">4</th>
            <th class="border border-white">5</th>
            <th class="border border-white">2</th>
            <th class="border border-white">3</th>
            <th class="border border-white">4</th>
            <th class="border border-white">5</th>
            <th class="border border-white">2</th>
            <th class="border border-white">3</th>
            <th class="border border-white">4</th>
            <th class="border border-white">5</th>
        </tr>
    </thead>
    <tbody class="weight-for-height-after-120-table text-xs">
        @php
            $count = 0;

            $totalServed = 0;
            $totalMale = 0;
            $totalFemale = 0;

            $normalAged2Male = 0;
            $wastedAged2Male = 0;
            $severelyWastedAged2Male = 0;
            $overweightAged2Male = 0;
            $obeseAged2Male = 0;

            $normalAged2Female = 0;
            $wastedAged2Female = 0;
            $severelyWastedAged2Female = 0;
            $overweightAged2Female = 0;
            $obeseAged2Female = 0;

            $normalAged3Male = 0;
            $wastedAged3Male = 0;
            $severelyWastedAged3Male = 0;
            $overweightAged3Male = 0;
            $obeseAged3Male = 0;

            $normalAged3Female = 0;
            $wastedAged3Female = 0;
            $severelyWastedAged3Female = 0;
            $overweightAged3Female = 0;
            $obeseAged3Female = 0;

            $normalAged4Male = 0;
            $wastedAged4Male = 0;
            $severelyWastedAged4Male = 0;
            $overweightAged4Male = 0;
            $obeseAged4Male = 0;

            $normalAged4Female = 0;
            $wastedAged4Female = 0;
            $severelyWastedAged4Female = 0;
            $overweightAged4Female = 0;
            $obeseAged4Female = 0;
            
            $normalAged5Male = 0;
            $wastedAged5Male = 0;
            $severelyWastedAged5Male = 0;
            $overweightAged5Male = 0;
            $obeseAged5Male = 0;

            $normalAged5Female = 0;
            $wastedAged5Female = 0;
            $severelyWastedAged5Female = 0;
            $overweightAged5Female = 0;
            $obeseAged5Female = 0;

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

                $normalAged2Male += $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['normal']['male'] ?? 0;
                $normalAged3Male += $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['normal']['male'] ?? 0;
                $normalAged4Male += $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['normal']['male'] ?? 0;
                $normalAged5Male += $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['normal']['male'] ?? 0;

                $normalAged2Female += $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['normal']['female'] ?? 0;
                $normalAged3Female += $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['normal']['female'] ?? 0;
                $normalAged4Female += $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['normal']['female'] ?? 0;
                $normalAged5Female += $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['normal']['female'] ?? 0;

                $wastedAged2Male += $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['wasted']['male'] ?? 0;
                $wastedAged3Male += $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['wasted']['male'] ?? 0;
                $wastedAged4Male += $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['wasted']['male'] ?? 0;
                $wastedAged5Male += $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['wasted']['male'] ?? 0;

                $wastedAged2Female += $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['wasted']['female'] ?? 0;
                $wastedAged3Female += $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['wasted']['female'] ?? 0;
                $wastedAged4Female += $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['wasted']['female'] ?? 0;
                $wastedAged5Female += $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['wasted']['female'] ?? 0;

                $severelyWastedAged2Male += $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['severely_wasted']['male'] ?? 0;
                $severelyWastedAged3Male += $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['severely_wasted']['male'] ?? 0;
                $severelyWastedAged4Male += $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['severely_wasted']['male'] ?? 0;
                $severelyWastedAged5Male += $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['severely_wasted']['male'] ?? 0;

                $severelyWastedAged2Female += $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['severely_wasted']['female'] ?? 0;
                $severelyWastedAged3Female += $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['severely_wasted']['female'] ?? 0;
                $severelyWastedAged4Female += $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['severely_wasted']['female'] ?? 0;
                $severelyWastedAged5Female += $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['severely_wasted']['female'] ?? 0;

                $overweightAged2Male += $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['overweight']['male'] ?? 0;
                $overweightAged3Male += $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['overweight']['male'] ?? 0;
                $overweightAged4Male += $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['overweight']['male'] ?? 0;
                $overweightAged5Male += $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['overweight']['male'] ?? 0;

                $overweightAged2Female += $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['overweight']['female'] ?? 0;
                $overweightAged3Female += $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['overweight']['female'] ?? 0;
                $overweightAged4Female += $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['overweight']['female'] ?? 0;
                $overweightAged5Female += $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['overweight']['female'] ?? 0;

                $obeseAged2Male += $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['obese']['male'] ?? 0;
                $obeseAged3Male += $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['obese']['male'] ?? 0;
                $obeseAged4Male += $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['obese']['male'] ?? 0;
                $obeseAged5Male += $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['obese']['male'] ?? 0;

                $obeseAged2Female += $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['obese']['female'] ?? 0;
                $obeseAged3Female += $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['obese']['female'] ?? 0;
                $obeseAged4Female += $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['obese']['female'] ?? 0;
                $obeseAged5Female += $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['obese']['female'] ?? 0;

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
                <td>{{ $center->user->full_name }}</td>
                <td>{{ $totals[$center->id]['total_served'] ?? 0 }}</td>
                <td>{{ $totals[$center->id]['total_male'] ?? 0 }}</td>
                <td>{{ $totals[$center->id]['total_female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['normal']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['normal']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['normal']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['normal']['male'] ?? 0 }}</td>

                <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['normal']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['normal']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['normal']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['normal']['female'] ?? 0 }}</td>

                <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['wasted']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['wasted']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['wasted']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['wasted']['male'] ?? 0 }}</td>

                <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['wasted']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['wasted']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['wasted']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['wasted']['female'] ?? 0 }}</td>

                <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['severely_wasted']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['severely_wasted']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['severely_wasted']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['severely_wasted']['male'] ?? 0 }}</td>

                <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['severely_wasted']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['severely_wasted']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['severely_wasted']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['severely_wasted']['female'] ?? 0 }}</td>

                <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['overweight']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['overweight']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['overweight']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['overweight']['male'] ?? 0 }}</td>

                <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['overweight']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['overweight']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['overweight']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['overweight']['female'] ?? 0 }}</td>

                <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['obese']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['obese']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['obese']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['obese']['male'] ?? 0 }}</td>

                <td>{{ $ageGroupsPerCenter[$center->id]['2']['weight_for_height']['obese']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['weight_for_height']['obese']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['weight_for_height']['obese']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['weight_for_height']['obese']['female'] ?? 0 }}</td>

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

            <td>{{ $wastedAged2Male }}</td>
            <td>{{ $wastedAged3Male }}</td>
            <td>{{ $wastedAged4Male }}</td>
            <td>{{ $wastedAged5Male }}</td>
            <td>{{ $wastedAged2Female }}</td>
            <td>{{ $wastedAged3Female }}</td>
            <td>{{ $wastedAged4Female }}</td>
            <td>{{ $wastedAged5Female }}</td>

            <td>{{ $severelyWastedAged2Male }}</td>
            <td>{{ $severelyWastedAged3Male }}</td>
            <td>{{ $severelyWastedAged4Male }}</td>
            <td>{{ $severelyWastedAged5Male }}</td>
            <td>{{ $severelyWastedAged2Female }}</td>
            <td>{{ $severelyWastedAged3Female }}</td>
            <td>{{ $severelyWastedAged4Female }}</td>
            <td>{{ $severelyWastedAged5Female }}</td>

            <td>{{ $overweightAged2Male }}</td>
            <td>{{ $overweightAged3Male }}</td>
            <td>{{ $overweightAged4Male }}</td>
            <td>{{ $overweightAged5Male }}</td>
            <td>{{ $overweightAged2Female }}</td>
            <td>{{ $overweightAged3Female }}</td>
            <td>{{ $overweightAged4Female }}</td>
            <td>{{ $overweightAged5Female }}</td>

            <td>{{ $obeseAged2Male }}</td>
            <td>{{ $obeseAged3Male }}</td>
            <td>{{ $obeseAged4Male }}</td>
            <td>{{ $obeseAged5Male }}</td>
            <td>{{ $obeseAged2Female }}</td>
            <td>{{ $obeseAged3Female }}</td>
            <td>{{ $obeseAged4Female }}</td>
            <td>{{ $obeseAged5Female }}</td>

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

            $allWastedMale = 0;
            $allWastedeFemale = 0;

            $allSeverelyWastedMale = 0;
            $allSeverelyWastedFemale = 0;

            $allOverweightMale = 0;
            $allOverweightFemale = 0;

            $allObeseMale = 0;
            $allObeseFemale = 0;

            $allMale = 0;
            $allFemale = 0;

            $allNormal = 0;
            $allWasted = 0;
            $allSeverelyWasted = 0;
            $allOverweight = 0;
            $allObese = 0;
            $allTotal = 0;


            $totalServedMaleFemale = $totalMale + $totalFemale;

            $allNormalMale = $normalAged2Male + $normalAged3Male + $normalAged4Male + $normalAged5Male;
            $allNormalFemale = $normalAged2Female + $normalAged3Female + $normalAged4Female + $normalAged5Female;

            $allWastedMale = $wastedAged2Male + $wastedAged3Male + $wastedAged4Male + $wastedAged5Male;
            $allWastedeFemale = $wastedAged2Female + $wastedAged3Female + $wastedAged4Female + $wastedAged5Female;

            $allSeverelyWastedMale = $severelyWastedAged2Male + $severelyWastedAged3Male + $severelyWastedAged4Male + $severelyWastedAged5Male;
            $allSeverelyWastedFemale = $severelyWastedAged2Female + $severelyWastedAged3Female + $severelyWastedAged4Female + $severelyWastedAged5Female;

            $allOverweightMale = $overweightAged2Male + $overweightAged3Male + $overweightAged4Male + $overweightAged5Male;
            $allOverweightFemale = $overweightAged2Female + $overweightAged3Female + $overweightAged4Female + $overweightAged5Female;

            $allObeseMale = $obeseAged2Male + $obeseAged3Male + $obeseAged4Male + $obeseAged5Male;
            $allObeseFemale = $obeseAged2Female + $obeseAged3Female + $obeseAged4Female + $obeseAged5Female;

            $allMale =  $totalAged2Male + $totalAged3Male + $totalAged4Male + $totalAged5Male;
            $allFemale = $totalAged2Female + $totalAged3Female + $totalAged4Female + $totalAged5Female;

            $allNormal = $allNormalMale + $allNormalFemale;
            $allWasted = $allWastedMale + $allWastedeFemale;
            $allSeverelyWasted = $allSeverelyWastedMale + $allSeverelyWastedFemale;
            $allOverweight = $allOverweightMale + $allOverweightFemale;
            $allObese = $allObeseMale + $allObeseFemale;
            $allTotal = $allMale + $allFemale;
        @endphp
        
        <tr>
            <td class="text-right" colspan="3">Total Male/Female ></td>
            <td rowspan="2" colspan="2">{{ $totalServedMaleFemale }}</td>
            <td colspan="4">{{ $allNormalMale }}</td>
            <td colspan="4">{{ $allNormalFemale }}</td>
            <td colspan="4">{{ $allWastedMale }}</td>
            <td colspan="4">{{ $allWastedeFemale }}</td>
            <td colspan="4">{{ $allSeverelyWastedMale }}</td>
            <td colspan="4">{{ $allSeverelyWastedFemale }}</td>
            <td colspan="4">{{ $allOverweightMale }}</td>
            <td colspan="4">{{ $allOverweightFemale }}</td>
            <td colspan="4">{{ $allObeseMale }}</td>
            <td colspan="4">{{ $allObeseFemale }}</td>
            <td colspan="4">{{ $allMale }}</td>
            <td colspan="4">{{ $allFemale }}</td>
        </tr>

        <tr>
            <td class="text-right" colspan="3">Total Child Beneficiaries ></td>
            <td colspan="8">{{ $allNormal }}</td>
            <td colspan="8">{{ $allWasted }}</td>
            <td colspan="8">{{ $allSeverelyWasted }}</td>
            <td colspan="8">{{ $allOverweight }}</td>
            <td colspan="8">{{ $allObese }}</td>
            <td colspan="8">{{ $allTotal }}</td>
        </tr>
    </tbody>
</table>
