<table id='malnourished-table' class="table mt-3 text-xs text-center" style="min-width: 1800px;">
    <thead>
        <tr>
            <th class="border border-white w-40" rowspan="2">Name of Child</th>
            <th class="border border-white w-40" rowspan="2">Name of Child Development Center</th>
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
    <tbody class="malnourished-table text-xs">
        @foreach ($isFunded as $fundedChild)
            <tr>
                <td>{{ $fundedChild->full_name }}</td>
                <td>{{ $fundedChild->center->center_name }}</td>
                <td>{{ $fundedChild->sex->name }}</td>
                <td>{{ $fundedChild->date_of_birth }}</td>

                @if ($fundedChild->nutritionalStatus->first() === null)
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @else
                        <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->weighing_date : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->weight : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->height : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->age_in_months : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->age_in_years : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->weight_for_age : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->weight_for_height : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->first() ? $fundedChild->nutritionalStatus->first()->height_for_age : 'N/A' }}</td>
                    @endif
                    @if (isset($fundedChild->nutritionalStatus[1]))
                        <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->weighing_date : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->weight : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->height : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->age_in_months : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->age_in_years : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->weight_for_age : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->weight_for_height : 'N/A' }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->count() > 1 ? $fundedChild->nutritionalStatus[1]->height_for_age : 'N/A' }}</td>
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
            </tr>
        @endforeach
        @if (count($isFunded) <= 0)
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