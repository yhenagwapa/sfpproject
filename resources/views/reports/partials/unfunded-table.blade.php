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
    <tbody class="unfunded-table text-xs">
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
