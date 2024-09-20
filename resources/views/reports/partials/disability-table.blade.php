<table id='disability-table' class="table datatable mt-3 text-xs text-center">
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
        @foreach ($isPwdChidlren as $childrenWithDisability) 
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
        
    </tbody>
</table>