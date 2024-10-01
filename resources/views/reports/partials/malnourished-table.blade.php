<table id='malnourished-table' class="table datatable mt-3 text-xs text-center" style="min-width: 1800px;">
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
    <tbody class="malnourished-table text-xs">
        @foreach ($isFunded as $fundedChild)
            <tr>
                <td>{{ $fundedChild->full_name }}</td>
                <td>{{ $fundedChild->sex->name }}</td>
                <td>{{ $fundedChild->date_of_birth }}</td>

                @if ($fundedChild->nutritionalStatus)
                        @php
                            $birthDate = \Carbon\Carbon::parse($fundedChild->date_of_birth);
                            $entryWeighingDate = \Carbon\Carbon::parse(
                                $fundedChild->nutritionalStatus->entry_actual_date_of_weighing,
                            );
                            $entryAgeInYears = $entryWeighingDate->diffInYears($birthDate);
                            $entryAgeInMonths = $entryWeighingDate->diffInMonths($birthDate) % 12;

                            $exitWeighingDate = \Carbon\Carbon::parse(
                                $fundedChild->nutritionalStatus->exit_actual_date_of_weighing,
                            );
                            $exitAgeInYears = $exitWeighingDate->diffInYears($birthDate);
                            $exitAgeInMonths = $exitWeighingDate->diffInMonths($birthDate) % 12;
                        @endphp

                        <td>{{ $fundedChild->nutritionalStatus->entry_actual_date_of_weighing }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->entry_weight }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->entry_height }}</td>
                        <td>{{ $entryAgeInMonths }}</td>
                        <td>{{ $entryAgeInYears }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->entry_weight_for_age }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->entry_weight_for_height }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->entry_height_for_age }}</td>

                        <td>{{ $fundedChild->nutritionalStatus->exit_actual_date_of_weighing }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->exit_weight }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->exit_height }}</td>
                        <td>{{ $exitAgeInMonths }}</td>
                        <td>{{ $exitAgeInYears }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->exit_weight_for_age }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->exit_weight_for_height }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->exit_height_for_age }}</td>
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