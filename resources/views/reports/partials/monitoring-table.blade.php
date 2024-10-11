<div class="row">
    <div class="col-md-6 mt-4 text-sm">
        <form action="{{ route('reports.filter-monitoring') }}" method="POST">
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
<table id='monitoring-table' class="table datatable mt-3 text-xs text-center" style="min-width: 1800px;">
    <thead>
        <tr>
            <th class="border border-white w-40" rowspan="2">Name of Child</th>
            <th class="border border-white" rowspan="2">Sex</th>
            <th class="border border-white w-24" rowspan="2">Date of Birth</th>
            <th class="border border-white w-24" rowspan="2">Actual Date of Weighing</th>
            <th class="border border-white" rowspan="2">Weight in kg.</th>
            <th class="border border-white" rowspan="2">Height in cm.</th>
            <th class="border border-white" colspan="2">Age in month/year</th>
            <th class="border border-white" colspan="3">Nutritional Status Upon Entry</th>
            <th class="border border-white w-10" rowspan="2">Summary of Undernourished Children</th>
            <th class="border border-white w-24" rowspan="2">Actual Date of Weighing</th>
            <th class="border border-white" rowspan="2">Weight in kg.</th>
            <th class="border border-white" rowspan="2">Height in cm.</th>
            <th class="border border-white" colspan="2">Age in month/year</th>
            <th class="border border-white" colspan="3">Nutritional Status After 120 Feedings</th>
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
    {{-- @php
    dd($isFunded); // This will stop execution and show the contents of $isFunded
@endphp --}}
    <tbody class="monitoring-table text-xs">
        @foreach ($isFunded as $fundedChild)
            <tr>
                <td>{{ $fundedChild->full_name }}</td>
                <td>{{ $fundedChild->sex->name }}</td>
                <td>{{ $fundedChild->date_of_birth }}</td>

                @if ($fundedChild->nutritionalStatus)
                    @if ($fundedChild->nutritionalStatus->first() === null)
                        <td></td>
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
                        <td>
                            @if ($fundedChild->nutritionalStatus->isNotEmpty() && $fundedChild->nutritionalStatus->first()->is_undernourish)
                                Yes
                            @elseif ($fundedChild->nutritionalStatus->isNotEmpty())
                                No
                            @endif
                        </td>
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
                        <td>
                            @if ($fundedChild->nutritionalStatus->isNotEmpty() && $fundedChild->nutritionalStatus[1]->is_undernourish)
                                Yes
                            @elseif ($fundedChild->nutritionalStatus->isNotEmpty())
                                No
                            @endif
                        </td>
                    @else
                        
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                @else
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
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
