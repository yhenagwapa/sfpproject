<table id='undernourished-upon-entry-table' class="table datatable mt-3 text-xs text-center" style="min-width: 1800px;">
    <thead>
        <tr>
            <th rowspan="3">Name of Child Development Center</th>
            <th rowspan="3">Name of Child Development Worker</th>
            <th colspan="8">Summary of Undernourished Children</th>
            <th colspan="10">Beneficiaries Profile</th>
            <th colspan="4">Deworming & Vitamin A Record</th>
        </tr>
        <tr>
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
            <th>M</th>
            <th>F</th>
            <th>M</th>
            <th>F</th>
            <th>M</th>
            <th>F</th>
            <th>M</th>
            <th>F</th>
            <th>M</th>
            <th>F</th>
            <th>M</th>
            <th>F</th>
            <th>M</th>
            <th>F</th>
            <th>M</th>
            <th>F</th>
            <th>M</th>
            <th>F</th>
            <th>M</th>
            <th>F</th>
            <th>M</th>
            <th>F</th>
        </tr>
    </thead>
    <tbody class="undernourished-upon-entry-table text-xs">
        @foreach ($centers as $center)
            <tr>
                <td>{{ $center->center_name }}</td>
                <td>{{ $center->user->full_name }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['2_years_old']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['2_years_old']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3_years_old']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['3_years_old']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4_years_old']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['4_years_old']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5_years_old']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['5_years_old']['female'] ?? 0 }}</td>
                
                <td>{{ $ageGroupsPerCenter[$center->id]['indigenous_people']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['indigenous_people']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['pantawid']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['pantawid']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['pwd']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['pwd']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['lactose_intolerant']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['lactose_intolerant']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['child_of_solo_parent']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['child_of_solo_parent']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['dewormed']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['dewormed']['female'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['vitamin_a']['male'] ?? 0 }}</td>
                <td>{{ $ageGroupsPerCenter[$center->id]['vitamin_a ']['female'] ?? 0 }}</td>
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
        @endif
    </tbody>
</table>