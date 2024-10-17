@if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
    <div class="col-md-6 mt-4 text-sm">
        <form action="{{ route('reports.unfunded') }}" method="POST">
            @csrf
            <label for="center_name">Filter per center:</label>
            <select class="form-control" name="center_name" id="center_name" onchange="this.form.submit()">
                <option value="all_center" selected>All Child Development Center
                </option>
                @foreach ($centers as $center)
                    <option value="{{ $center->id }}"
                        {{ old('center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                        {{ $center->center_name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
@endif

<table id='unfunded-table' class="table datatable text-xs text-center">
    <thead>
        <tr>
            <th class="border border-white w-40" rowspan="2">Name of Child</th>
            <th class="border border-white" rowspan="2">Sex</th>
            <th class="border border-white w-24" rowspan="2">Date of Birth</th>
            <th class="border border-white" rowspan="2">Deworming</th>
            <th class="border border-white" rowspan="2">Vitamin A</th>
            <th class="border border-white w-10" rowspan="2">Pantawid Member</th>
            <th class="border border-white" rowspan="2">IPs</th>
            <th class="border border-white" rowspan="2">PWD</th>
            <th class="border border-white  w-10" rowspan="2">Child of Solo Parent</th>
            <th class="border border-white  w-10" rowspan="2">Lactose Intolerant</th>
        </tr>
        
    </thead>
    <tbody class="unfunded-table">
        @foreach ($isNotFunded as $unfundedChild)
            <tr>
                <td>{{ $unfundedChild->full_name }}</td>
                <td>{{ $unfundedChild->sex->name }}</td>
                <td>{{ $unfundedChild->date_of_birth }}</td>

                <td>{{ $unfundedChild->deworming_date }}</td>
                <td>{{ $unfundedChild->vitamin_a_date }}</td>
                <td>{{ $unfundedChild->pantawid_details ? $unfundedChild->pantawid_details : 'No' }}</td>
                <td>{{ $unfundedChild->is_indigenous_people ? 'Yes' : 'No' }}</td>
                <td>{{ $unfundedChild->person_with_disability_details ? $unfundedChild->person_with_disability_details : 'No' }}</td>
                <td>{{ $unfundedChild->is_child_of_soloparent ? 'Yes' : 'No' }}</td>
                <td>{{ $unfundedChild->is_lactose_intolerant ? 'Yes' : 'No' }}</td>
            </tr>
        @endforeach
        @if (count($isNotFunded) <= 0)
            <tr>
                <td class="text-center text-red-600" colspan="10">
                    @if (empty($search))
                        No Data found
                    @endif
                </td>
            </tr>
        @endif
    </tbody>
</table>
