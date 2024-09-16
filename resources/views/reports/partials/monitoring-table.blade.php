<table id='masterlist-table' class="table datatable mt-3 text-xs text-center" style="min-width: 1800px;">
    <thead class="border bg-gray-200">
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
            <th class="border border-white w-24" rowspan="2">Actual Date of Weighing</th>
            <th class="border border-white" rowspan="2">Weight in kg.</th>
            <th class="border border-white" rowspan="2">Height in cm.</th>
            <th class="border border-white" colspan="2">Age in month/year</th>
            <th class="border border-white" colspan="3">Nutritional Status</th>
            <th class="border border-white w-10" rowspan="2">Summary of Undernourished Children</th>
            
        </tr>
        <tr>
            <th class="border border-white">Month</th>
            <th class="border border-white">Year</th>
            <th class="border border-white">Weight for Age</th>
            <th class="border border-white">Weight for Height</th>
            <th class="border border-white">Height for Age</th>
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
                        $entryActualDate = \Carbon\Carbon::parse($child->nutritionalStatus->entry_actual_date_of_weighing);
                        $entryAgeInYears = $entryActualDate->diffInYears($dob);
                        $entyrAgeInMonths = $entryActualDate->diffInMonths($dob) % 12;
                        $exitActualDate = \Carbon\Carbon::parse($child->nutritionalStatus->exit_actual_date_of_weighing);
                        $exitAgeInYears = $exitActualDate->diffInYears($dob);
                        $exitAgeInMonths = $exitActualDate->diffInMonths($dob) % 12;
                    @endphp

                    <td>{{ $child->nutritionalStatus->entry_actual_date_of_weighing }}</td>  
                    <td>{{ $child->nutritionalStatus->entry_weight }}</td> 
                    <td>{{ $child->nutritionalStatus->entry_height }}</td>
                    <td>{{ $entyrAgeInMonths }}</td>
                    <td>{{ $entryAgeInYears }}</td>
                    <td></td>
                    <td>{{ $child->nutritionalStatus->entry_weight_for_age }}</td>
                    <td>{{ $child->nutritionalStatus->entry_weight_for_height }}</td>
                    <td>{{ $child->nutritionalStatus->entry_height_for_age }}</td>
                    <td>{{ $child->nutritionalStatus->exit_actual_date_of_weighing }}</td>  
                    <td>{{ $child->nutritionalStatus->exit_weight }}</td> 
                    <td>{{ $child->nutritionalStatus->exit_height }}</td>
                    <td>{{ $exitAgeInMonths }}</td>
                    <td>{{ $exitAgeInYears }}</td>
                    <td>{{ $child->nutritionalStatus->exit_weight_for_age }}</td>
                    <td>{{ $child->nutritionalStatus->entry_weight_for_height }}</td>
                    <td>{{ $child->nutritionalStatus->exit_height_for_age }}</td>
                    <td></td>
                @else
                    <td></td>
                @endif
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