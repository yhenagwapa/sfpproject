@if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
    <div class="col-md-6 mt-4 text-sm">
        <form action="{{ route('reports.filterUnfundedByCdc') }}" method="POST">
            @csrf
            <label for="center_name">Filter per center:</label>
            <select class="form-control" name="unfunded_center_name" id="unfunded_center_name" onchange="this.form.submit()">
                <option value="all_center" selected>All Child Development Center
                </option>
                @foreach ($centers as $center)
                    <option value="{{ $center->id }}"
                        {{ old('unfunded_center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                        {{ $center->center_name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
@endif

<table id='unfunded-table' class="table datatable text-xs text-center" style="min-width: 1800px;">
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
    <tbody class="unfunded-table">
        @foreach ($isNotFunded as $unfundedChild)
            <tr>
                <td>{{ $unfundedChild->full_name }}</td>
                <td>{{ $unfundedChild->sex->name }}</td>
                <td>{{ $unfundedChild->date_of_birth }}</td>


                @if ($unfundedChild->nutritionalStatus)
                    @php
                        $dob = \Carbon\Carbon::parse($unfundedChild->date_of_birth);
                        $actualDate = \Carbon\Carbon::parse(
                            $unfundedChild->nutritionalStatus->entry_actual_date_of_weighing,
                        );
                        $ageInYears = $actualDate->diffInYears($dob);
                        $ageInMonths = $actualDate->diffInMonths($dob) % 12;
                    @endphp

                    <td>{{ $unfundedChild->nutritionalStatus->entry_actual_date_of_weighing }}</td>
                    <td>{{ $unfundedChild->nutritionalStatus->entry_weight }}</td>
                    <td>{{ $unfundedChild->nutritionalStatus->entry_height }}</td>
                    <td>{{ $ageInMonths }}</td>
                    <td>{{ $ageInYears }}</td>
                    <td>{{ $unfundedChild->nutritionalStatus->entry_weight_for_age }}</td>
                    <td>{{ $unfundedChild->nutritionalStatus->entry_weight_for_height }}</td>
                    <td>{{ $unfundedChild->nutritionalStatus->entry_height_for_weight }}</td>
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
                <td>{{ $unfundedChild->deworming_date }}</td>
                <td>{{ $unfundedChild->vitamin_a_date }}</td>
                <td>{{ $unfundedChild->pantawid_details }}</td>
                <td>{{ $unfundedChild->is_indigenous_people ? 'Yes' : 'No' }}</td>
                <td>{{ $unfundedChild->person_with_disability_details }}</td>
                <td>{{ $unfundedChild->is_child_of_soloparent ? 'Yes' : 'No' }}</td>
                <td>{{ $unfundedChild->is_lactose_intolerant ? 'Yes' : 'No' }}</td>
            </tr>
        @endforeach
        @if (count($isNotFunded) <= 0)
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
