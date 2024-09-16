<table id='malnourished-table' class="table datatable mt-3 text-xs text-center" style="min-width: 1800px;">
    <thead>
        <thead>
            <tr>
                <th class="border border-white w-40" rowspan="2">Name of Child</th>
                <th class="border border-white" rowspan="2">Sex</th>
                <th class="border border-white w-24" rowspan="2">Date of Birth</th>
                <th class="border border-white w-24" rowspan="2">Actual Date of Weighing</th>
                <th class="border border-white" rowspan="2">Weight in kg.</th>
                <th class="border border-white" rowspan="2">Height in cm.</th>
                <th class="border border-white" colspan="2">Age in month/year</th>
                <th class="border border-white" colspan="3">NS UPON ENTRY</th>
                <th class="border border-white w-24" rowspan="2">Actual Date of Weighing</th>
                <th class="border border-white" rowspan="2">Weight in kg.</th>
                <th class="border border-white" rowspan="2">Height in cm.</th>
                <th class="border border-white" colspan="2">Age in month/year</th>
                <th class="border border-white" colspan="3">NS AFTER 120 FEEDINGS</th>
                
                
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
    </thead>
    <tbody class="malnourished-table text-xs">
        @foreach ($children as $child) 
            <tr>
                <td>{{ $child->full_name }}</td>
                <td>{{ $child->sex }}</td>
                <td>{{ $child->date_of_birth }}</td>

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