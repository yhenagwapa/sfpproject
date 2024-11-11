<table id='attendance-table' class="table mt-3 text-xs text-center">
    <thead class="border bg-gray-200">
        <tr>
            <th class="border border-white" rowspan="3">Attendance table here</th>
            
        </tr>
        {{-- <tr>
            <th class="border border-white w-20" colspan="2">2 y/o</th>
            <th class="border border-white w-20" colspan="2">3 y/o</th>
            <th class="border border-white w-20" colspan="2">4 y/o</th>
            <th class="border border-white w-20" colspan="2">5 y/o</th>
            <th class="border border-white w-20" colspan="2">No. of Ethnic Children</th>
            <th class="border border-white w-20" colspan="2">No. of 4Ps Children</th>
            <th class="border border-white w-20" colspan="2">No. of PWD</th>
            <th class="border border-white w-20" colspan="2">No. of Children with Lactose Intolerance</th>
            <th class="border border-white w-20" colspan="2">No. of Children with Solo Parent</th>
            <th class="border border-white w-24" colspan="2">No. of Dewormed Children</th>
            <th class="border border-white w-24" colspan="2">No. of Children with Vit. A Supp.</th>
        </tr>
        <tr>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
            <th class="border border-white">Male</th>
            <th class="border border-white">Female</th>
        </tr> --}}
    </thead>
    <tbody class="attendance-table text-xs">
        <tr>
            
        </tr>
        {{-- @foreach ($centers as $center)
            <tr>
                <td>{{ $center->center_name }}</td>
                <td>{{ $center->user->full_name }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $countsPerCenter[$center->id]['indigenous_people']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['indigenous_people']['female'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['pantawid']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['pantawid']['female'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['pwd']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['pwd']['female'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['lactose_intolerant']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['lactose_intolerant']['female'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['child_of_solo_parent']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['child_of_solo_parent']['female'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['dewormed']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['dewormed']['female'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['vitamin_a']['male'] ?? 0 }}</td>
                <td>{{ $countsPerCenter[$center->id]['vitamin_a ']['female'] ?? 0 }}</td>
            </tr>
        @endforeach
        @if (count($centers) <= 0)
            <tr>
                <td class="text-center" colspan="6">
                    @if (empty($search))
                        No Data found
                    @endif
                </td>
            </tr>
        @endif --}}
        
    </tbody>
</table>