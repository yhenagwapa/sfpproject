<h5 class="card-title">Upon Entry</h5>
<table id="ns-table" class="table t-3 text-sm text-center w-full">
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
            <tr>
                <td>{{ $entryDetails->weighing_date }}</td>
                <td>{{ $entryDetails->weight }}</td>
                <td>{{ $entryDetails->height }}</td>
                <td>{{ $entryDetails->age_in_months }}</td>
                <td>{{ $entryDetails->age_in_years }}</td>
                <td class="{{ $entryDetails->weight_for_age !== 'Normal' ? 'text-red-500' : '' }}">{{ $entryDetails->weight_for_age }}</td>
                <td class="{{ $entryDetails->weight_for_height !== 'Normal' ? 'text-red-500' : '' }}">{{ $entryDetails->weight_for_height }}</td>
                <td class="{{ $entryDetails->height_for_age !== 'Normal' ? 'text-red-500' : '' }}">{{ $entryDetails->height_for_age }}</td>
                <td class="{{ $entryDetails->is_malnourish ? 'text-red-500' : '' }}">{{ $entryDetails->is_malnourish ? 'Yes' : 'No' }}</td>
                <td class="{{ $entryDetails->is_undernourish ? 'text-red-500' : '' }}">{{ $entryDetails->is_undernourish ? 'Yes' : 'No' }}</td>
            </tr>
        @endif
    </tbody>
</table>
<div></div>

<h5 class="card-title">After 120 Feeding Days</h5>
<table class="table mt-3 text-sm text-center w-full">
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
            <td>{{ $exitDetails->weighing_date }}</td>
            <td>{{ $exitDetails->weight }}</td>
            <td>{{ $exitDetails->height }}</td>
            <td>{{ $exitDetails->age_in_months }}</td>
            <td>{{ $exitDetails->age_in_years }}</td>
            <td class="{{ $exitDetails->weight_for_age !== 'Normal' ? 'text-red-500' : '' }}">{{ $exitDetails->weight_for_age }}</td>
            <td class="{{ $exitDetails->weight_for_height !== 'Normal' ? 'text-red-500' : '' }}">{{ $exitDetails->weight_for_height }}</td>
            <td class="{{ $exitDetails->height_for_age !== 'Normal' ? 'text-red-500' : '' }}">{{ $exitDetails->height_for_age }}</td>
            <td class="{{ $exitDetails->is_malnourish ? 'text-red-500' : '' }}">{{ $exitDetails->is_malnourish ? 'Yes' : 'No' }}</td>
            <td class="{{ $exitDetails->is_undernourish ? 'text-red-500' : '' }}">{{ $exitDetails->is_undernourish ? 'Yes' : 'No' }}</td>
        @endif
    </tbody>
</table>
