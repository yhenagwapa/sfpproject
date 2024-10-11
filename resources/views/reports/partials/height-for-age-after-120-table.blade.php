<table id='height-for-age-after-120-table' class="table datatable mt-3 text-xs text-center">
    <thead class="border bg-gray-200">
        <tr>
            <th class="border border-white w-30" rowspan="3">No.</th>
            <th class="border border-white w-30" rowspan="3">Name of Child Development Center</th>
            <th class="border border-white w-30" rowspan="3">Name of Child Development Worker</th>
            <th class="border border-white w-10" rowspan="3">Total Number Served</th>
            <th class="border border-white w-10" rowspan="2" colspan="2">Total No of CDC/SNP Served</th>
            <th class="border border-white" colspan="8">Normal</th>
            <th class="border border-white" colspan="8">Underweight</th>
            <th class="border border-white" colspan="8">Severly Underweight</th>
            <th class="border border-white" colspan="8">Overweight</th>
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
        </tr>
    </thead>
    <tbody class="height-for-age-after-120-table text-xs">
        @php
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

        @foreach ($centers as $center)
            @php
                $count += 1;

                $totalServed += $totals[$center->id]['total_served'] ?? 0;
                $totalMale += $totals[$center->id]['total_male'] ?? 0;
                $totalFemale += $totals[$center->id]['total_female'] ?? 0;

                $normalAged2Male += $ageGroupsPerCenter[$center->id]['2']['height_for_age']['normal']['male'] ?? 0;
                $normalAged3Male += $ageGroupsPerCenter[$center->id]['3']['height_for_age']['normal']['male'] ?? 0;
                $normalAged4Male += $ageGroupsPerCenter[$center->id]['4']['height_for_age']['normal']['male'] ?? 0;
                $normalAged5Male += $ageGroupsPerCenter[$center->id]['5']['height_for_age']['normal']['male'] ?? 0;

                $normalAged2Female += $ageGroupsPerCenter[$center->id]['2']['height_for_age']['normal']['female'] ?? 0;
                $normalAged3Female += $ageGroupsPerCenter[$center->id]['3']['height_for_age']['normal']['female'] ?? 0;
                $normalAged4Female += $ageGroupsPerCenter[$center->id]['4']['height_for_age']['normal']['female'] ?? 0;
                $normalAged5Female += $ageGroupsPerCenter[$center->id]['5']['height_for_age']['normal']['female'] ?? 0;

                $stuntedAged2Male += $ageGroupsPerCenter[$center->id]['2']['height_for_age']['stunted']['male'] ?? 0;
                $stuntedAged3Male += $ageGroupsPerCenter[$center->id]['3']['height_for_age']['stunted']['male'] ?? 0;
                $stuntedAged4Male += $ageGroupsPerCenter[$center->id]['4']['height_for_age']['stunted']['male'] ?? 0;
                $stuntedAged5Male += $ageGroupsPerCenter[$center->id]['5']['height_for_age']['stunted']['male'] ?? 0;

                $stuntedAged2Female += $ageGroupsPerCenter[$center->id]['2']['height_for_age']['stunted']['female'] ?? 0;
                $stuntedAged3Female += $ageGroupsPerCenter[$center->id]['3']['height_for_age']['stunted']['female'] ?? 0;
                $stuntedAged4Female += $ageGroupsPerCenter[$center->id]['4']['height_for_age']['stunted']['female'] ?? 0;
                $stuntedAged5Female += $ageGroupsPerCenter[$center->id]['5']['height_for_age']['stunted']['female'] ?? 0;

                $severelyStuntedAged2Male += $ageGroupsPerCenter[$center->id]['2']['height_for_age']['severely_stunted']['male'] ?? 0;
                $severelyStuntedAged3Male += $ageGroupsPerCenter[$center->id]['3']['height_for_age']['severely_stunted']['male'] ?? 0;
                $severelyStuntedAged4Male += $ageGroupsPerCenter[$center->id]['4']['height_for_age']['severely_stunted']['male'] ?? 0;
                $severelyStuntedAged5Male += $ageGroupsPerCenter[$center->id]['5']['height_for_age']['severely_stunted']['male'] ?? 0;

                $severelyStuntedAged2Female += $ageGroupsPerCenter[$center->id]['2']['height_for_age']['severely_stunted']['female'] ?? 0;
                $severelyStuntedAged3Female += $ageGroupsPerCenter[$center->id]['3']['height_for_age']['severely_stunted']['female'] ?? 0;
                $severelyStuntedAged4Female += $ageGroupsPerCenter[$center->id]['4']['height_for_age']['severely_stunted']['female'] ?? 0;
                $severelyStuntedAged5Female += $ageGroupsPerCenter[$center->id]['5']['height_for_age']['severely_stunted']['female'] ?? 0;

                $tallAged2Male += $ageGroupsPerCenter[$center->id]['2']['height_for_age']['tall']['male'] ?? 0;
                $tallAged3Male += $ageGroupsPerCenter[$center->id]['3']['height_for_age']['tall']['male'] ?? 0;
                $tallAged4Male += $ageGroupsPerCenter[$center->id]['4']['height_for_age']['tall']['male'] ?? 0;
                $tallAged5Male += $ageGroupsPerCenter[$center->id]['5']['height_for_age']['tall']['male'] ?? 0;

                $tallAged2Female += $ageGroupsPerCenter[$center->id]['2']['height_for_age']['tall']['female'] ?? 0;
                $tallAged3Female += $ageGroupsPerCenter[$center->id]['3']['height_for_age']['tall']['female'] ?? 0;
                $tallAged4Female += $ageGroupsPerCenter[$center->id]['4']['height_for_age']['tall']['female'] ?? 0;
                $tallAged5Female += $ageGroupsPerCenter[$center->id]['5']['height_for_age']['tall']['female'] ?? 0;

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

                <td>{{ $ageGroupsPerCenter[$center->id]['2']['height_for_age']['stunted']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['height_for_age']['stunted']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['height_for_age']['stunted']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['height_for_age']['stunted']['female'] ?? 0 }}</td>

                <td>{{ $ageGroupsPerCenter[$center->id]['2']['height_for_age']['severely_stunted']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['height_for_age']['severely_stunted']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['height_for_age']['severely_stunted']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['height_for_age']['severely_stunted']['male'] ?? 0 }}</td>

                <td>{{ $ageGroupsPerCenter[$center->id]['2']['height_for_age']['severely_stunted']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3']['height_for_age']['severely_stunted']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4']['height_for_age']['severely_stunted']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5']['height_for_age']['severely_stunted']['female'] ?? 0 }}</td>

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

            $allSeverelyStuntedMale = $severelyStuntedAged2Male + $severelyStuntedAged3Male + $severelyStuntedAged4Male + $severelyStuntedAged5Male;
            $allSeverelyStuntedFemale = $severelyStuntedAged2Female + $severelyStuntedAged3Female + $severelyStuntedAged4Female + $severelyStuntedAged5Female;

            $allTallMale = $tallAged2Male + $tallAged3Male + $tallAged4Male + $tallAged5Male;
            $allTallFemale = $tallAged2Female + $tallAged3Female + $tallAged4Female + $tallAged5Female;

            $allMale =  $totalAged2Male + $totalAged3Male + $totalAged4Male + $totalAged5Male;
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
    </tbody>
</table>
