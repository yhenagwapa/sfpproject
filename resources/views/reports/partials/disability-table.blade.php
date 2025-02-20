<table id='disability-table' class="table mt-3 text-xs text-center">
    <thead>
        <tr>
            <th class="border border-white">Name of Child</th>
            <th class="border border-white">Name of Child Development Center</th>
            <th class="border border-white">Sex</th>
            <th class="border border-white">Date of Birth</th>
            <th class="border border-white">Type of Disability</th>
        </tr>
        
    </thead>
    <tbody class="disability-table text-xs">
        @foreach ($isPwdChildren as $childrenWithDisability) 
            <tr>
                <td>{{ $childrenWithDisability->full_name }}</td>
                <td>
                    @if($childrenWithDisability->center)
                        {{ $childrenWithDisability->center->center_name }}
                    @else
                        No Center Assigned
                    @endif
                </td>
                <td>{{ $childrenWithDisability->sex->name }}</td>
                <td>{{ $childrenWithDisability->date_of_birth }}</td>
                <td>{{ $childrenWithDisability->person_with_disability_details }}</td>
            </tr>
        
        @endforeach
        @if (count($isPwdChildren) <= 0)
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