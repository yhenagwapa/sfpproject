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
            
                <tr>
                    <td>Normal (N)</td>
                    <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_normal']['male'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_normal']['female'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_normal']['male'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_normal']['female'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_normal']['male'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_normal']['female'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_normal']['male'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_normal']['female'] ?? 0 }}</td>
                    <td></td>
                    <td></td>
                <tr>
                    <td>Underweight (UW)</td>
                    <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_underweight']['male'] ?? 0}}</td>
                    <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_underweight']['female'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_underweight']['male'] ?? 0}}</td>
                    <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_underweight']['female'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_underweight']['male'] ?? 0}}</td>
                    <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_underweight']['female'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_underweight']['male'] ?? 0}}</td>
                    <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_underweight']['female'] ?? 0 }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Severely Underweight (SUW)</td>
                    <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_severely_underweight']['male'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_severely_underweight']['female'] ?? 0 }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Overweight (OW)</td>
                    <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_overweight']['male'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[2]['weight_for_age_overweight']['female'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_overweight']['male'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[3]['weight_for_age_overweight']['female'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_overweight']['male'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[4]['weight_for_age_overweight']['female'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_overweight']['male'] ?? 0 }}</td>
                    <td>{{ $countsPerNutritionalStatus[5]['weight_for_age_overweight']['female'] ?? 0 }}</td>
                    <td></td>
                    <td></td>
                </tr>
            
        </tbody>
</table>

{{-- <table id='weight-for-age-table' class="table datatable mt-3 text-xs text-center">
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
    <tbody class="weight-for-age-table text-xs">
        
        
        <tr>
            <td>Normal (N)</td>

        </tr>
        <tr>
            <td>Wasted (W)</td>
            
        </tr>
        <tr>
            <td>Severely Wasted (SW)</td>
            
        </tr>
        <tr>
            <td>Overweight (OW)</td>
            
        </tr>
        <tr>
            <td>Obese (Ob)</td>
            
        </tr>
    </tbody>
</table>
<table id='weight-for-age-table' class="table datatable mt-3 text-xs text-center">
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
        
        <tr>
            <td>Normal (N)</td>

        </tr>
        <tr>
            <td>Stunted (S)</td>
            
        </tr>
        <tr>
            <td>Severely Stunted (SS)</td>
            
        </tr>
        <tr>
            <td>Tall (T)</td>
            
        </tr>
    </tbody>
</table>
<table id='weight-for-age-table' class="table datatable mt-3 text-xs text-center">
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
        <tr>
            <td>Summary of Undernourished Children</td>

        </tr>
        <tr>
            <td>Deworming</td>
            
        </tr>
        <tr>
            <td>Severely Stunted (SS)</td>
            
        </tr>
        <tr>
            <td>Vitamin A Supplementation</td>
            
        </tr>
        <tr>
            <td>4Ps Member</td>
            
        </tr>
        <tr>
            <td>IPs Member</td>
            
        </tr>
        <tr>
            <td>PWD</td>
            
        </tr>
        <tr>
            <td>Child of Solo Parent</td>
            
        </tr>
        <tr>
            <td>Lactose Intolerant</td>
            
        </tr>
        @foreach ($centers as $center)
            <tr>
                <td>{{ $center->center_name }}</td>
                <td>{{ $center->user->full_name }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $countsPerCenter[$center->id]['indigenous_people']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['indigenous_people']['female'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['pantawid']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['pantawid']['female'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['pwd']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['pwd']['female'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['lactose_intolerant']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['lactose_intolerant']['female'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['child_of_solo_parent']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['child_of_solo_parent']['female'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['dewormed']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['dewormed']['female'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['vitamin_a']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['vitamin_a ']['female'] ?? 0 }}</td>
            </tr>
        @endforeach
        @if (count($centers) <= 0)
            <tr>
                <td class="text-center" colspan="6">
                    @if (empty($search))
                        No Data found
                    @endif
                </td>
            </tr>
        @endif
        
    </tbody>
</table> --}}