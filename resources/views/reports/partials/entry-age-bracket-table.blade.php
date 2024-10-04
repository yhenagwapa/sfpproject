<table id='weight-for-age-table' class="table datatable mt-3 text-xs text-center">
    <thead class="border bg-gray-200">
        <tr>
            <th class="border border-white" rowspan="3">WEIGHT FOR AGE</th>

        </tr>
        <tr>
            <th class="border border-white w-20" colspan="2">2 YEARS OLD</th>
            <th class="border border-white w-20" colspan="2">3 YEARS OLD</th>
            <th class="border border-white w-20" colspan="2">4 YEARS OLD</th>
            <th class="border border-white w-20" colspan="2">5 YEARS OLD</th>
            <th class="border border-white w-20" colspan="2">TOTAL:</th>
        </tr>
        <tr>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
        </tr>
    </thead>
    <tbody class="weight-for-age-table text-xs">

        @php
            $totalNormalMale = 0;
            $totalNormalFemale = 0;
            $totalUWMale = 0;
            $totalUWFemale = 0;
            $totalSUWMale = 0;
            $totalSUWFemale = 0;
            $totalOWMale = 0;
            $totalOWFemale = 0;

            // Loop through each age group to calculate the total
            foreach ([2, 3, 4, 5] as $age) {
                $totalNormalMale += $countsPerNutritionalStatus[$age]['weight_for_age_normal']['male'] ?? 0;
                $totalNormalFemale += $countsPerNutritionalStatus[$age]['weight_for_age_normal']['female'] ?? 0;
                $totalUWMale += $countsPerNutritionalStatus[$age]['weight_for_age_underweight']['male'] ?? 0;
                $totalUWFemale += $countsPerNutritionalStatus[$age]['weight_for_age_underweight']['female'] ?? 0;
                $totalSUWMale += $countsPerNutritionalStatus[$age]['weight_for_age_severely_underweight']['male'] ?? 0;
                $totalSUWFemale += $countsPerNutritionalStatus[$age]['weight_for_age_severely_underweight']['female'] ?? 0;
                $totalOWMale += $countsPerNutritionalStatus[$age]['weight_for_age_overweight']['male'] ?? 0;
                $totalOWFemale += $countsPerNutritionalStatus[$age]['weight_for_age_overweight']['female'] ?? 0;
            }
        @endphp
        <tr>
            <td class="text-left">Normal (N)</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $totalNormalMale }}</td>
            <td>{{ $totalNormalFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Underweight (UW)</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_underweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_underweight']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_underweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_underweight']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_underweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_underweight']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_underweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_underweight']['female'] ?? 0 }}</td>
            <td>{{ $totalUWMale }}</td>
            <td>{{ $totalUWFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Severely Underweight (SUW)</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
            <td>{{ $totalSUWMale }}</td>
            <td>{{ $totalSUWFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Overweight (OW)</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_overweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_overweight']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_overweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_overweight']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_overweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_overweight']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_overweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_overweight']['female'] ?? 0 }}</td>
            <td>{{ $totalOWMale }}</td>
            <td>{{ $totalOWFemale }}</td>
        </tr>
    </tbody>

</table>

<table id='weight-for-height-table' class="table datatable mt-3 text-xs text-center">
    <thead class="border bg-gray-200">
        <tr>
            <th class="border border-white" rowspan="3">WEIGHT FOR HEIGHT</th>
        </tr>
        <tr>
            <th class="border border-white w-20" colspan="2">2 YEARS OLD</th>
            <th class="border border-white w-20" colspan="2">3 YEARS OLD</th>
            <th class="border border-white w-20" colspan="2">4 YEARS OLD</th>
            <th class="border border-white w-20" colspan="2">5 YEARS OLD</th>
            <th class="border border-white w-20" colspan="2">TOTAL:</th>
        </tr>
        <tr>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
        </tr>
    </thead>
    <tbody class="weight-for-height-table text-xs">
        @php
            $totalNormalMale = 0;
            $totalNormalFemale = 0;
            $totalWMale = 0;
            $totalWFemale = 0;
            $totalSWMale = 0;
            $totalSWFemale = 0;
            $totalOWMale = 0;
            $totalOWFemale = 0;
            $totalObMale = 0;
            $totalObFemale = 0;

            // Loop through each age group to calculate the total
            foreach ([2, 3, 4, 5] as $age) {
                $totalNormalMale += $countsPerNutritionalStatus[$age]['weight_for_height_normal']['male'] ?? 0;
                $totalNormalFemale += $countsPerNutritionalStatus[$age]['weight_for_height_normal']['female'] ?? 0;
                $totalWMale += $countsPerNutritionalStatus[$age]['weight_for_height_wasted']['male'] ?? 0;
                $totalWFemale += $countsPerNutritionalStatus[$age]['weight_for_height_wasted']['female'] ?? 0;
                $totalSWMale += $countsPerNutritionalStatus[$age]['weight_for_height_severely_wasted']['male'] ?? 0;
                $totalSWFemale += $countsPerNutritionalStatus[$age]['weight_for_height_severely_wasted']['female'] ?? 0;
                $totalOWMale += $countsPerNutritionalStatus[$age]['weight_for_height_overweight']['male'] ?? 0;
                $totalOWFemale += $countsPerNutritionalStatus[$age]['weight_for_height_overweight']['female'] ?? 0;
                $totalObMale += $countsPerNutritionalStatus[$age]['weight_for_height_obese']['male'] ?? 0;
                $totalObFemale += $countsPerNutritionalStatus[$age]['weight_for_height_obese']['female'] ?? 0;
            }
        @endphp
        <tr>
            <td class="text-left">Normal (N)</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_height_normal']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_height_normal']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_height_normal']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_height_normal']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_height_normal']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_height_normal']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_height_normal']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_height_normal']['female'] ?? 0 }}</td>
            <td>{{ $totalNormalMale }}</td>
            <td>{{ $totalNormalFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Wasted (W)</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_height_wasted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_height_wasted']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_height_wasted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_height_wasted']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_height_wasted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_height_wasted']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_height_wasted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_height_wasted']['female'] ?? 0 }}</td>
            <td>{{ $totalWMale }}</td>
            <td>{{ $totalWFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Severely Wasted (SW)</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_height_severely_wasted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_height_severely_wasted']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_height_severely_wasted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_height_severely_wasted']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_height_severely_wasted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_height_severely_wasted']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_height_severely_wasted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_height_severely_wasted']['female'] ?? 0 }}</td>
            <td>{{ $totalSWMale }}</td>
            <td>{{ $totalSWFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Overweight (OW)</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_height_overweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_height_overweight']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_height_overweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_height_overweight']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_height_overweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_height_overweight']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_height_overweight']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_height_overweight']['female'] ?? 0 }}</td>
            <td>{{ $totalOWMale }}</td>
            <td>{{ $totalOWFemale }}</td>
        </tr>
        <tr>
            <td class="text-left">Obese (Ob)</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_height_obese']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['weight_for_height_obese']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_height_obese']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['weight_for_height_obese']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_height_obese']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['weight_for_height_obese']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_height_obese']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['weight_for_height_obese']['female'] ?? 0 }}</td>
            <td>{{ $totalObMale }}</td>
            <td>{{ $totalObFemale }}</td>
        </tr>
    </tbody>
</table>

<table id='height-for-age-table' class="table datatable mt-3 text-xs text-center">
    <thead class="border bg-gray-200">
        <tr>
            <th class="border border-white" rowspan="3">HEIGHT FOR AGE</th>

        </tr>
        <tr>
            <th class="border border-white w-20" colspan="2">2 YEARS OLD</th>
            <th class="border border-white w-20" colspan="2">3 YEARS OLD</th>
            <th class="border border-white w-20" colspan="2">4 YEARS OLD</th>
            <th class="border border-white w-20" colspan="2">5 YEARS OLD</th>
            <th class="border border-white w-20" colspan="2">TOTAL:</th>
        </tr>
        <tr>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
        </tr>
    </thead>
    <tbody class="weight-for-age-table text-xs">
        @php
            $totalNormalMale = 0;
            $totalNormalFemale = 0;
            $totalSMale = 0;
            $totalSFemale = 0;
            $totalSSMale = 0;
            $totalSSFemale = 0;
            $totalTMale = 0;
            $totalTFemale = 0;

            foreach ([2, 3, 4, 5] as $age) {
                $totalNormalMale += $countsPerNutritionalStatus[$age]['height_for_age_normal']['male'] ?? 0;
                $totalNormalFemale += $countsPerNutritionalStatus[$age]['height_for_age_normal']['female'] ?? 0;
                $totalSMale += $countsPerNutritionalStatus[$age]['height_for_age_stunted']['male'] ?? 0;
                $totalSFemale += $countsPerNutritionalStatus[$age]['height_for_age_stunted']['female'] ?? 0;
                $totalSSMale += $countsPerNutritionalStatus[$age]['height_for_age_severely_stunted']['male'] ?? 0;
                $totalSSFemale += $countsPerNutritionalStatus[$age]['height_for_age_severely_stunted']['female'] ?? 0;
                $totalTMale += $countsPerNutritionalStatus[$age]['height_for_age_tall']['male'] ?? 0;
                $totalTFemale += $countsPerNutritionalStatus[$age]['height_for_age_tall']['female'] ?? 0;
            }
        @endphp
        <tr>
            <td class="text-left">Normal (N)</td>
            <td>{{ $countsPerNutritionalStatus[2]['height_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['height_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['height_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['height_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['height_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['height_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['height_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['height_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $totalNormalMale }}</td>
            <td>{{ $totalNormalFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Stunted (S)</td>
            <td>{{ $countsPerNutritionalStatus[2]['height_for_age_stunted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['height_for_age_stunted']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['height_for_age_stunted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['height_for_age_stunted']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['height_for_age_stunted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['height_for_age_stunted']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['height_for_age_stunted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['height_for_age_stunted']['female'] ?? 0 }}</td>
            <td>{{ $totalSMale }}</td>
            <td>{{ $totalSFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Severely Stunted (SS)</td>
            <td>{{ $countsPerNutritionalStatus[2]['height_for_age_severely_stunted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['height_for_age_severely_stunted']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['height_for_age_severely_stunted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['height_for_age_severely_stunted']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['height_for_age_severely_stunted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['height_for_age_severely_stunted']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['height_for_age_severely_stunted']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['height_for_age_severely_stunted']['female'] ?? 0 }}</td>
            <td>{{ $totalSSMale }}</td>
            <td>{{ $totalSSFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Tall (T)</td>
            <td>{{ $countsPerNutritionalStatus[2]['height_for_age_tall']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['height_for_age_tall']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['height_for_age_tall']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['height_for_age_tall']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['height_for_age_tall']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['height_for_age_tall']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['height_for_age_tall']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['height_for_age_tall']['female'] ?? 0 }}</td>
            <td>{{ $totalTMale }}</td>
            <td>{{ $totalTFemale }}</td>
        </tr>
    </tbody>
</table>

<table id='profile-table' class="table datatable mt-3 text-xs text-center">
    <tbody class="weight-for-age-table text-xs">
        <thead class="border bg-gray-200">
            <tr>
                <th class="border border-white" rowspan="3"></th>

            </tr>
            <tr>
                <th class="border border-white w-20" colspan="2">2 YEARS OLD</th>
                <th class="border border-white w-20" colspan="2">3 YEARS OLD</th>
                <th class="border border-white w-20" colspan="2">4 YEARS OLD</th>
                <th class="border border-white w-20" colspan="2">5 YEARS OLD</th>
                <th class="border border-white w-20" colspan="2">TOTAL:</th>
            </tr>
            <tr>
                <th class="border border-white">Male</th>
                <th class="border border-white">Female</th>
                <th class="border border-white">Male</th>
                <th class="border border-white">Female</th>
                <th class="border border-white">Male</th>
                <th class="border border-white">Female</th>
                <th class="border border-white">Male</th>
                <th class="border border-white">Female</th>
                <th class="border border-white">Male</th>
                <th class="border border-white">Female</th>
            </tr>
        </thead>
    <tbody>
        @php
            $totalUndernourishMale = 0;
            $totalUndernourishFemale = 0;
            $totalDewormedMale = 0;
            $totalDewormedFemale = 0;
            $totalVitAMale = 0;
            $totalVitAFemale = 0;
            $totalPantawidMale = 0;
            $totalPantawidFemale = 0;
            $totalIPMale = 0;
            $totalIPFemale = 0;
            $totalPWDMale = 0;
            $totalPWDFemale = 0;
            $totalSoloParentMale = 0;
            $totalSoloParentFemale = 0;
            $totalLactoseIntolerantMale = 0;
            $totalLactoseIntolerantFemale = 0;

            // Loop through each age group to calculate the total
            foreach ([2, 3, 4, 5] as $age) {
                $totalUndernourishMale += $countsPerNutritionalStatus[$age]['entry_is_undernourish']['male'] ?? 0;
                $totalUndernourishFemale += $countsPerNutritionalStatus[$age]['entry_is_undernourish']['female'] ?? 0;
                $totalDewormedMale += $countsPerCenter[$age]['dewormed']['male'] ?? 0;
                $totalDewormedFemale += $countsPerCenter[$age]['dewormed']['female'] ?? 0;
                $totalVitAMale += $countsPerCenter[$age]['vitamin_a']['male'] ?? 0;
                $totalVitAFemale += $countsPerCenter[$age]['vitamin_a']['female'] ?? 0;
                $totalPantawidMale += $countsPerCenter[$age]['pantawid']['male'] ?? 0;
                $totalPantawidFemale += $countsPerCenter[$age]['pantawid']['female'] ?? 0;
                $totalIPMale += $countsPerCenter[$age]['indigenous_people']['female'] ?? 0;
                $totalIPFemale += $countsPerCenter[$age]['indigenous_people']['male'] ?? 0;
                $totalPWDMale += $countsPerCenter[$age]['pwd']['female'] ?? 0;
                $totalPWDFemale += $countsPerCenter[$age]['pwd']['male'] ?? 0;
                $totalSoloParentMale += $countsPerCenter[$age]['child_of_solo_parent']['female'] ?? 0;
                $totalSoloParentFemale += $countsPerCenter[$age]['child_of_solo_parent']['male'] ?? 0;
                $totalLactoseIntolerantMale += $countsPerCenter[$age]['lactose_intolerant']['female'] ?? 0;
                $totalLactoseIntolerantFemale += $countsPerCenter[$age]['lactose_intolerant']['male'] ?? 0;
            }
        @endphp
        <tr>
            <td class="text-left">Summary of Undernourished Children</td>
            <td>{{ $countsPerNutritionalStatus[2]['entry_is_undernourish']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[2]['entry_is_undernourish']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['entry_is_undernourish']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[3]['entry_is_undernourish']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['entry_is_undernourish']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[4]['entry_is_undernourish']['female'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['entry_is_undernourish']['male'] ?? 0 }}</td>
            <td>{{ $countsPerNutritionalStatus[5]['entry_is_undernourish']['female'] ?? 0 }}</td>
            <td>{{ $totalUndernourishMale }}</td>
            <td>{{ $totalUndernourishFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Deworming</td>
            <td>{{ $countsPerCenter[2]['dewormed']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[2]['dewormed']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['dewormed']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['dewormed']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['dewormed']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['dewormed']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['dewormed']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['dewormed']['female'] ?? 0 }}</td>
            <td>{{ $totalDewormedMale }}</td>
            <td>{{ $totalDewormedFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Vitamin A Supplementation</td>
            <td>{{ $countsPerCenter[2]['vitamin_a']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[2]['vitamin_a']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['vitamin_a']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['vitamin_a']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['vitamin_a']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['vitamin_a']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['vitamin_a']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['vitamin_a']['female'] ?? 0 }}</td>
            <td>{{ $totalVitAMale }}</td>
            <td>{{ $totalVitAFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">4Ps Member</td>
            <td>{{ $countsPerCenter[2]['pantawid']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[2]['pantawid']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['pantawid']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['pantawid']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['pantawid']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['pantawid']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['pantawid']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['pantawid']['female'] ?? 0 }}</td>
            <td>{{ $totalPantawidMale }}</td>
            <td>{{ $totalPantawidFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">IP Member</td>
            <td>{{ $countsPerCenter[2]['indigenous_people']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[2]['indigenous_people']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['indigenous_people']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['indigenous_people']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['indigenous_people']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['indigenous_people']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['indigenous_people']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['indigenous_people']['female'] ?? 0 }}</td>
            <td>{{ $totalIPMale }}</td>
            <td>{{ $totalIPFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">PWD</td>
            <td>{{ $countsPerCenter[2]['pwd']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[2]['pwd']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['pwd']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['pwd']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['pwd']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['pwd']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['pwd']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['pwd']['female'] ?? 0 }}</td>
            <td>{{ $totalPWDMale }}</td>
            <td>{{ $totalPWDFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Child of Solo Parent</td>
            <td>{{ $countsPerCenter[2]['child_of_solo_parent']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[2]['child_of_solo_parent']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['child_of_solo_parent']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['child_of_solo_parent']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['child_of_solo_parent']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['child_of_solo_parent']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['child_of_solo_parent']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['child_of_solo_parent']['female'] ?? 0 }}</td>
            <td>{{ $totalSoloParentMale }}</td>
            <td>{{ $totalSoloParentFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Lactose Intolerant</td>
            <td>{{ $countsPerCenter[2]['lactose_intolerant']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[2]['lactose_intolerant']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['lactose_intolerant']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[3]['lactose_intolerant']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['lactose_intolerant']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[4]['lactose_intolerant']['female'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['lactose_intolerant']['male'] ?? 0 }}</td>
            <td>{{ $countsPerCenter[5]['lactose_intolerant']['female'] ?? 0 }}</td>
            <td>{{ $totalLactoseIntolerantMale }}</td>
            <td>{{ $totalLactoseIntolerantFemale }}</td>
        </tr>

    </tbody>
</table>
