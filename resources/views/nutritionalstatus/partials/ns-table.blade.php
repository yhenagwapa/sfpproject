<h5 class="card-title">Upon Entry</h5>
<table class="table datatable table-auto mt-3 text-sm text-center w-full">
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
            <th>Malnourish</th>
            <th>Undernourish</th>
        </tr>
        
    </thead>
    <tbody class="text-base">
        @if (!$hasUponEntryData)
            <tr>
                <td class="text-center text-red-600" colspan="10">
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
                    <td class="{{ $result->entry_weight_for_age !== 'Normal' ? 'text-red-500' : '' }}">{{ $result->entry_weight_for_age }}</td>
                    <td class="{{ $result->entry_weight_for_height !== 'Normal' ? 'text-red-500' : '' }}">{{ $result->entry_weight_for_height }}</td>
                    <td class="{{ $result->entry_height_for_age !== 'Normal' ? 'text-red-500' : '' }}">{{ $result->entry_height_for_age }}</td>
                    <td class="{{ $result->entry_is_malnourish ? 'text-red-500' : '' }}">{{ $result->entry_is_malnourish ? 'Yes' : 'No' }}</td>
                    <td class="{{ $result->entry_is_undernourish ? 'text-red-500' : '' }}">{{ $result->entry_is_undernourish ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
<div></div>
<h5 class="card-title">After 120 Feeding Days</h5>
<table class="table datatable table-auto mt-3 text-sm text-center w-full">
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
            <th>Malnourish</th>
            <th>Undernourish</th>
        </tr>
    </thead>
    <tbody class="text-base">
        @if (!$hasUponExitData)
            <tr>
                <td class="text-center text-red-600" colspan="10">
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
                    <td class="{{ $result->exit_weight_for_age !== 'Normal' ? 'text-red-500' : '' }}">{{ $result->exit_weight_for_age }}</td>
                    <td class="{{ $result->exit_weight_for_height !== 'Normal' ? 'text-red-500' : '' }}">{{ $result->exit_weight_for_height }}</td>
                    <td class="{{ $result->exit_height_for_age !== 'Normal' ? 'text-red-500' : '' }}">{{ $result->exit_height_for_age }}</td>
                    <td class="{{ $result->exit_is_malnourish ? 'text-red-500' : '' }}">{{ $result->exit_is_malnourish ? 'Yes' : 'No' }}</td>
                    <td class="{{ $result->exit_is_undernourish ? 'text-red-500' : '' }}">{{ $result->exit_is_undernourish ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>