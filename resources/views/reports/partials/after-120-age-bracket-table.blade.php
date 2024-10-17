<div class="row">
    <div class="col-md-6 mt-4 text-sm">
        <form action="{{ route('reports.age-bracket-after-120') }}" method="POST">
            @csrf
            <label for="center_name">Filter per center:</label>
            <select class="form-control" name="center_name" id="center_name" onchange="this.form.submit()">
                <option value="all_center" {{ old('center_name', $cdcId) == 'all_center' ? 'selected' : '' }}>All Child Development Center
                </option>
                @foreach ($centers as $center)
                    <option value="{{ $center->id }}" {{ old('center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                        {{ $center->center_name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="col-md-6 mt-11 text-sm">
        <a href="{{ url('/reports/print-funded', ['center_name' => request()->center_name]) }}" class="text-white bg-blue-600 rounded px-3 min-h-9 align-items-right" target="_blank">Print</a>
    </div>
</div>
<table id='weight-for-age-table' class="table datatable mt-3 text-xs text-center">
    <thead class="border bg-gray-200">
        <tr>
            <th class="border border-white" rowspan="2">WEIGHT FOR AGE</th>
            <th class="border border-white" colspan="2">2 YEARS OLD</th>
            <th class="border border-white" colspan="2">3 YEARS OLD</th>
            <th class="border border-white" colspan="2">4 YEARS OLD</th>
            <th class="border border-white" colspan="2">5 YEARS OLD</th>
            <th class="border border-white" colspan="2">TOTAL:</th>
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
                $totalNormalMale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_normal']['male'] ?? 0;
                $totalNormalFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_normal']['female'] ?? 0;
                $totalUWMale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_underweight']['male'] ?? 0;
                $totalUWFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_underweight']['female'] ?? 0;
                $totalSUWMale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_severely_underweight']['male'] ?? 0;
                $totalSUWFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_severely_underweight']['female'] ?? 0;
                $totalOWMale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_overweight']['male'] ?? 0;
                $totalOWFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_age_overweight']['female'] ?? 0;
            }
        @endphp
        <tr>
            <td class="text-left">Normal (N)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $totalNormalMale }}</td>
            <td>{{ $totalNormalFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Underweight (UW)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_underweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_underweight']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_underweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_underweight']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_underweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_underweight']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_underweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_underweight']['female'] ?? 0 }}</td>
            <td>{{ $totalUWMale }}</td>
            <td>{{ $totalUWFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Severely Underweight (SUW)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
            <td>{{ $totalSUWMale }}</td>
            <td>{{ $totalSUWFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Overweight (OW)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_overweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_age_overweight']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_overweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_age_overweight']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_overweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_age_overweight']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_overweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_age_overweight']['female'] ?? 0 }}</td>
            <td>{{ $totalOWMale }}</td>
            <td>{{ $totalOWFemale }}</td>
        </tr>
    </tbody>

</table>

<table id='weight-for-height-table' class="table datatable mt-3 text-xs text-center">
    <thead class="border bg-gray-200">
        <tr>
            <th class="border border-white" rowspan="2">WEIGHT FOR HEIGHT</th>
            <th class="border border-white" colspan="2">2 YEARS OLD</th>
            <th class="border border-white" colspan="2">3 YEARS OLD</th>
            <th class="border border-white" colspan="2">4 YEARS OLD</th>
            <th class="border border-white" colspan="2">5 YEARS OLD</th>
            <th class="border border-white" colspan="2">TOTAL:</th>
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
                $totalNormalMale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_normal']['male'] ?? 0;
                $totalNormalFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_normal']['female'] ?? 0;
                $totalWMale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_wasted']['male'] ?? 0;
                $totalWFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_wasted']['female'] ?? 0;
                $totalSWMale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_severely_wasted']['male'] ?? 0;
                $totalSWFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_severely_wasted']['female'] ?? 0;
                $totalOWMale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_overweight']['male'] ?? 0;
                $totalOWFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_overweight']['female'] ?? 0;
                $totalObMale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_obese']['male'] ?? 0;
                $totalObFemale += $exitCountsPerNutritionalStatus[$age]['weight_for_height_obese']['female'] ?? 0;
            }
        @endphp
        <tr>
            <td class="text-left">Normal (N)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_normal']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_normal']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_normal']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_normal']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_normal']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_normal']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_normal']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_normal']['female'] ?? 0 }}</td>
            <td>{{ $totalNormalMale }}</td>
            <td>{{ $totalNormalFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Wasted (W)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_wasted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_wasted']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_wasted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_wasted']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_wasted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_wasted']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_wasted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_wasted']['female'] ?? 0 }}</td>
            <td>{{ $totalWMale }}</td>
            <td>{{ $totalWFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Severely Wasted (SW)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_severely_wasted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_severely_wasted']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_severely_wasted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_severely_wasted']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_severely_wasted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_severely_wasted']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_severely_wasted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_severely_wasted']['female'] ?? 0 }}</td>
            <td>{{ $totalSWMale }}</td>
            <td>{{ $totalSWFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Overweight (OW)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_overweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_overweight']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_overweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_overweight']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_overweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_overweight']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_overweight']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_overweight']['female'] ?? 0 }}</td>
            <td>{{ $totalOWMale }}</td>
            <td>{{ $totalOWFemale }}</td>
        </tr>
        <tr>
            <td class="text-left">Obese (Ob)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_obese']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['weight_for_height_obese']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_obese']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['weight_for_height_obese']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_obese']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['weight_for_height_obese']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_obese']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['weight_for_height_obese']['female'] ?? 0 }}</td>
            <td>{{ $totalObMale }}</td>
            <td>{{ $totalObFemale }}</td>
        </tr>
    </tbody>
</table>

<table id='height-for-age-table' class="table datatable mt-3 text-xs text-center">
    <thead class="border bg-gray-200">
        <tr>
            <th class="border border-white" rowspan="3">HEIGHT FOR AGE</th>
            <th class="border border-white" colspan="2">2 YEARS OLD</th>
            <th class="border border-white" colspan="2">3 YEARS OLD</th>
            <th class="border border-white" colspan="2">4 YEARS OLD</th>
            <th class="border border-white" colspan="2">5 YEARS OLD</th>
            <th class="border border-white" colspan="2">TOTAL:</th>
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
    <tbody class="height-for-age-table text-xs">
        @php
            $totalNormalMale = 0;
            $totalNormalFemale = 0;
            $totalSMale = 0;
            $totalSFemale = 0;
            $totalSSMale = 0;
            $totalSSFemale = 0;
            $totalTMale = 0;
            $totalTFemale = 0;

            // Loop through each age group to calculate the total
            foreach ([2, 3, 4, 5] as $age) {
                $totalNormalMale += $exitCountsPerNutritionalStatus[$age]['height_for_age_normal']['male'] ?? 0;
                $totalNormalFemale += $exitCountsPerNutritionalStatus[$age]['height_for_age_normal']['female'] ?? 0;
                $totalSMale += $exitCountsPerNutritionalStatus[$age]['height_for_age_stunted']['male'] ?? 0;
                $totalSFemale += $exitCountsPerNutritionalStatus[$age]['height_for_age_stunted']['female'] ?? 0;
                $totalSSMale += $exitCountsPerNutritionalStatus[$age]['height_for_age_severely_stunted']['male'] ?? 0;
                $totalSSFemale += $exitCountsPerNutritionalStatus[$age]['height_for_age_severely_stunted']['female'] ?? 0;
                $totalTMale += $exitCountsPerNutritionalStatus[$age]['height_for_age_tall']['male'] ?? 0;
                $totalTFemale += $exitCountsPerNutritionalStatus[$age]['height_for_age_tall']['female'] ?? 0;
            }
        @endphp
        <tr>
            <td class="text-left">Normal (N)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_normal']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_normal']['female'] ?? 0 }}</td>
            <td>{{ $totalNormalMale }}</td>
            <td>{{ $totalNormalFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Stunted (S)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_stunted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_stunted']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_stunted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_stunted']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_stunted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_stunted']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_stunted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_stunted']['female'] ?? 0 }}</td>
            <td>{{ $totalSMale }}</td>
            <td>{{ $totalSFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Severely Stunted (SS)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_severely_stunted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_severely_stunted']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_severely_stunted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_severely_stunted']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_severely_stunted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_severely_stunted']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_severely_stunted']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_severely_stunted']['female'] ?? 0 }}</td>
            <td>{{ $totalSSMale }}</td>
            <td>{{ $totalSSFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Tall (T)</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_tall']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['height_for_age_tall']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_tall']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['height_for_age_tall']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_tall']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['height_for_age_tall']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_tall']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['height_for_age_tall']['female'] ?? 0 }}</td>
            <td>{{ $totalTMale }}</td>
            <td>{{ $totalTFemale }}</td>
        </tr>
    </tbody>
</table>

<table id='profile-table' class="table datatable mt-3 text-xs text-center">
    <tbody class="profile-table text-xs">
        <thead class="border bg-gray-200">
            <tr>
                <th class="border border-white" rowspan="3"></th>
                <th class="border border-white" colspan="2">2 YEARS OLD</th>
                <th class="border border-white" colspan="2">3 YEARS OLD</th>
                <th class="border border-white" colspan="2">4 YEARS OLD</th>
                <th class="border border-white" colspan="2">5 YEARS OLD</th>
                <th class="border border-white" colspan="2">TOTAL:</th>
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
                $totalUndernourishMale += $exitCountsPerNutritionalStatus[$age]['entry_is_undernourish']['male'] ?? 0;
                $totalUndernourishFemale += $exitCountsPerNutritionalStatus[$age]['entry_is_undernourish']['female'] ?? 0;
                $totalDewormedMale += $exitCountsPerCenter[$age]['dewormed']['male'] ?? 0;
                $totalDewormedFemale += $exitCountsPerCenter[$age]['dewormed']['female'] ?? 0;
                $totalVitAMale += $exitCountsPerCenter[$age]['vitamin_a']['male'] ?? 0;
                $totalVitAFemale += $exitCountsPerCenter[$age]['vitamin_a']['female'] ?? 0;
                $totalPantawidMale += $exitCountsPerCenter[$age]['pantawid']['male'] ?? 0;
                $totalPantawidFemale += $exitCountsPerCenter[$age]['pantawid']['female'] ?? 0;
                $totalIPMale += $exitCountsPerCenter[$age]['indigenous_people']['female'] ?? 0;
                $totalIPFemale += $exitCountsPerCenter[$age]['indigenous_people']['male'] ?? 0;
                $totalPWDMale += $exitCountsPerCenter[$age]['pwd']['female'] ?? 0;
                $totalPWDFemale += $exitCountsPerCenter[$age]['pwd']['male'] ?? 0;
                $totalSoloParentMale += $exitCountsPerCenter[$age]['child_of_solo_parent']['female'] ?? 0;
                $totalSoloParentFemale += $exitCountsPerCenter[$age]['child_of_solo_parent']['male'] ?? 0;
                $totalLactoseIntolerantMale += $exitCountsPerCenter[$age]['lactose_intolerant']['female'] ?? 0;
                $totalLactoseIntolerantFemale += $exitCountsPerCenter[$age]['lactose_intolerant']['male'] ?? 0;
            }
        @endphp
        <tr>
            <td class="text-left">Summary of Undernourished Children</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['entry_is_undernourish']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[2]['entry_is_undernourish']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['entry_is_undernourish']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[3]['entry_is_undernourish']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['entry_is_undernourish']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[4]['entry_is_undernourish']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['entry_is_undernourish']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerNutritionalStatus[5]['entry_is_undernourish']['female'] ?? 0 }}</td>
            <td>{{ $totalUndernourishMale }}</td>
            <td>{{ $totalUndernourishFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Deworming</td>
            <td>{{ $exitCountsPerCenter[2]['dewormed']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[2]['dewormed']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['dewormed']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['dewormed']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['dewormed']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['dewormed']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['dewormed']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['dewormed']['female'] ?? 0 }}</td>
            <td>{{ $totalDewormedMale }}</td>
            <td>{{ $totalDewormedFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Vitamin A Supplementation</td>
            <td>{{ $exitCountsPerCenter[2]['vitamin_a']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[2]['vitamin_a']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['vitamin_a']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['vitamin_a']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['vitamin_a']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['vitamin_a']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['vitamin_a']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['vitamin_a']['female'] ?? 0 }}</td>
            <td>{{ $totalVitAMale }}</td>
            <td>{{ $totalVitAFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">4Ps Member</td>
            <td>{{ $exitCountsPerCenter[2]['pantawid']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[2]['pantawid']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['pantawid']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['pantawid']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['pantawid']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['pantawid']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['pantawid']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['pantawid']['female'] ?? 0 }}</td>
            <td>{{ $totalPantawidMale }}</td>
            <td>{{ $totalPantawidFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">IP Member</td>
            <td>{{ $exitCountsPerCenter[2]['indigenous_people']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[2]['indigenous_people']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['indigenous_people']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['indigenous_people']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['indigenous_people']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['indigenous_people']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['indigenous_people']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['indigenous_people']['female'] ?? 0 }}</td>
            <td>{{ $totalIPMale }}</td>
            <td>{{ $totalIPFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">PWD</td>
            <td>{{ $exitCountsPerCenter[2]['pwd']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[2]['pwd']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['pwd']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['pwd']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['pwd']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['pwd']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['pwd']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['pwd']['female'] ?? 0 }}</td>
            <td>{{ $totalPWDMale }}</td>
            <td>{{ $totalPWDFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Child of Solo Parent</td>
            <td>{{ $exitCountsPerCenter[2]['child_of_solo_parent']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[2]['child_of_solo_parent']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['child_of_solo_parent']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['child_of_solo_parent']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['child_of_solo_parent']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['child_of_solo_parent']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['child_of_solo_parent']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['child_of_solo_parent']['female'] ?? 0 }}</td>
            <td>{{ $totalSoloParentMale }}</td>
            <td>{{ $totalSoloParentFemale }}</td>
        </tr>

        <tr>
            <td class="text-left">Lactose Intolerant</td>
            <td>{{ $exitCountsPerCenter[2]['lactose_intolerant']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[2]['lactose_intolerant']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['lactose_intolerant']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[3]['lactose_intolerant']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['lactose_intolerant']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[4]['lactose_intolerant']['female'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['lactose_intolerant']['male'] ?? 0 }}</td>
            <td>{{ $exitCountsPerCenter[5]['lactose_intolerant']['female'] ?? 0 }}</td>
            <td>{{ $totalLactoseIntolerantMale }}</td>
            <td>{{ $totalLactoseIntolerantFemale }}</td>
        </tr>

    </tbody>
</table>
