<h5 class="card-title">Upon Entry</h5>
<table class="table datatable table-auto mt-3 text-base text-center w-full">
    <thead>
        <tr>
            <th>Actual Date of Weighing</th>
            <th>Weight</th>
            <th>Height</th>
            <th>Age in Months</th>
            <th>Age in Years</th>
            <th>Weight for Age</th>
            <th>Weight for Height</th>
            <th>Height for Age</th>
        </tr>
        
    </thead>
    <tbody class="text-base">
        @if (!$hasUponEntryData)
            <tr>
                <td class="text-center text-red-600" colspan="8">
                    No data found.
                </td>
            </tr>
        @else
            @foreach ($results as $result)
                <tr>
                    <td>{{ $result->entry_actual_date_of_weighing }}</td>
                    <td>{{ $result->entry_weight }}</td>
                    <td>{{ $result->entry_height }}</td>
                    <td>{{ $entryAgeInMonths }}</td>
                    <td>{{ $entryAgeInYears }}</td>
                    <td>{{ $result->entry_weight_for_age }}</td>
                    <td>{{ $result->entry_weight_for_height }}</td>
                    <td>{{ $result->entry_height_for_age }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
<div></div>
<h5 class="card-title">After 120 Feeding Days</h5>
<table class="table datatable table-auto mt-3 text-base text-center w-full">
    <thead>
        <tr>
            <th>Actual Date of Weighing</th>
            <th>Weight</th>
            <th>Height</th>
            <th>Age in Months</th>
            <th>Age in Years</th>
            <th>Weight for Age</th>
            <th>Weight for Height</th>
            <th>Height for Age</th>
        </tr>
    </thead>
    <tbody class="text-base">
        @if (!$hasUponExitData)
            <tr>
                <td class="text-center text-red-600" colspan="8">
                    No data found.
                </td>
            </tr>
        @else
            @foreach ($results as $result)
                <tr>
                    <td>{{ $result->exit_actual_date_of_weighing }}</td>
                    <td>{{ $result->exit_weight }}</td>
                    <td>{{ $result->exit_height }}</td>
                    <td>{{ $exitAgeInMonths }}</td>
                    <td>{{ $exitAgeInYears }}</td>
                    <td>{{ $result->exit_weight_for_age }}</td>
                    <td>{{ $result->exit_weight_for_height }}</td>
                    <td>{{ $result->exit_height_for_age }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>