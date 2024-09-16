<table id='masterlist-table' class="table datatable mt-3 text-xs text-center" style="min-width: 1800px;">
    <thead>
        <tr>
            <th class="border border-white w-40" rowspan="2">Name of Child</th>
            <th class="border border-white" rowspan="2">Sex</th>
            <th class="border border-white w-24" rowspan="2">Date of Birth</th>
            <th class="border border-white w-24" rowspan="2">Actual Date of Weighing</th>
            <th class="border border-white" rowspan="2">Weight in kg.</th>
            <th class="border border-white" rowspan="2">Height in cm.</th>
            <th class="border border-white" colspan="2">Age in month/year</th>
            <th class="border border-white" colspan="3">Nutritional Status</th>
            <th class="border border-white w-10" rowspan="2">Summary of Undernourished Children</th>
            <th class="border border-white" rowspan="2">Deworming</th>
            <th class="border border-white" rowspan="2">Vitamin A</th>
            <th class="border border-white w-10" rowspan="2">Pantawid Member</th>
            <th class="border border-white" rowspan="2">IPs</th>
            <th class="border border-white" rowspan="2">PWD</th>
            <th class="border border-white  w-10" rowspan="2">Child of Solo Parent</th>
            <th class="border border-white  w-10" rowspan="2">Lactose Intolerant</th>
        </tr>
        <tr>
            <th class="border border-white">Month</th>
            <th class="border border-white">Year</th>
            <th class="border border-white">Weight for Age</th>
            <th class="border border-white">Weight for Height</th>
            <th class="border border-white">Height for Age</th>
        </tr>
    </thead>
    <tbody class="masterlist-table text-xs">
        @foreach ($children as $child) 
            <tr>
                <td>{{ $child->full_name }}</td>
                <td>{{ $child->sex }}</td>
                <td>{{ $child->date_of_birth }}</td>

                
                @if ($child->nutritionalStatus)
                
                    @php
                        $dob = \Carbon\Carbon::parse($child->date_of_birth);
                        $actualDate = \Carbon\Carbon::parse($child->nutritionalStatus->actual_date_of_weighing);
                        $ageInYears = $actualDate->diffInYears($dob);
                        $ageInMonths = $actualDate->diffInMonths($dob) % 12;
                    @endphp

                    <td>{{ $child->nutritionalStatus->actual_date_of_weighing }}</td>  
                    <td>{{ $child->nutritionalStatus->weight }}</td> 
                    <td>{{ $child->nutritionalStatus->height }}</td>
                    <td>{{ $ageInMonths }}</td>
                    <td>{{ $ageInYears }}</td>
                    <td>{{ $child->nutritionalStatus->weight_for_age }}</td>
                    <td>{{ $child->nutritionalStatus->weight_for_height }}</td>
                    <td>{{ $child->nutritionalStatus->height_for_weight }}</td>
                @else
                    {{-- Leave the cells blank if no nutritional status --}}
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                @endif

                
                
                <td></td>
                <td>{{ $child->deworming_date }}</td>
                <td>{{ $child->vitamin_a_date }}</td>
                <td>{{ $child->pantawid_details }}</td>
                <td>{{ $child->is_indigenous_people }}</td>
                <td>{{ $child->person_with_disability_details }}</td>
                <td>{{ $child->is_child_of_soloparent }}</td>
                <td>{{ $child->is_lactose_intolerant }}</td>
            </tr>
        
        @endforeach
        @if (count($children) <= 0)
            <tr>
                <td class="text-center" colspan="6">
                    @if (empty($search))
                        No Data found
                    @endif
                </td>
            </tr>
        @endif
    </tbody>
</table>