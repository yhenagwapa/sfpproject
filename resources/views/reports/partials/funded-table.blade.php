@if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
    <div class="col-md-6 mt-4 text-sm">
        <form action="{{ route('reports.filterFundedByCdc') }}" method="POST">
            @csrf
            <label for="center_name">Filter per center:</label>
            <select class="form-control" name="funded_center_name" id="funded_center_name" onchange="this.form.submit()">
                <option value="all_center" selected>All Child Development Center
                </option>
                @foreach ($centers as $center)
                    <option value="{{ $center->id }}" {{ old('funded_center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                        {{ $center->center_name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
@endif

<table id='funded-table' class="table datatable text-xs text-center" style="min-width: 1800px;">
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
    <tbody class="funded-table text-xs">
        @foreach ($isFunded as $fundedChild)
            <tr>
                <td>{{ $fundedChild->full_name }}</td>
                <td>{{ $fundedChild->sex->name }}</td>
                <td>{{ $fundedChild->date_of_birth }}</td>


                @if ($fundedChild->nutritionalStatus)
                        @php
                            $dob = \Carbon\Carbon::parse($fundedChild->date_of_birth);
                            $actualDate = \Carbon\Carbon::parse(
                                $fundedChild->nutritionalStatus->entry_actual_date_of_weighing,
                            );
                            $ageInYears = $actualDate->diffInYears($dob);
                            $ageInMonths = $actualDate->diffInMonths($dob) % 12;
                        @endphp

                        <td>{{ $fundedChild->nutritionalStatus->entry_actual_date_of_weighing }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->entry_weight }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->entry_height }}</td>
                        <td>{{ $ageInMonths }}</td>
                        <td>{{ $ageInYears }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->entry_weight_for_age }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->entry_weight_for_height }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->entry_height_for_age }}</td>
                        <td>{{ $fundedChild->nutritionalStatus->entry_is_undernourish ? 'Yes' : 'No' }}</td>
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

                <td>{{ $fundedChild->deworming_date }}</td>
                <td>{{ $fundedChild->vitamin_a_date }}</td>
                <td>{{ $fundedChild->pantawid_details }}</td>
                <td>{{ $fundedChild->is_indigenous_people ? 'Yes' : 'No' }}</td>
                <td>{{ $fundedChild->person_with_disability_details }}</td>
                <td>{{ $fundedChild->is_child_of_soloparent ? 'Yes' : 'No' }}</td>
                <td>{{ $fundedChild->is_lactose_intolerant ? 'Yes' : 'No' }}</td>
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