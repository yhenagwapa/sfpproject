
<table id='centers-table' class="table datatable mt-3 text-sm">
    <thead>
        <tr>
            <th><b>Child Development Centers</b></th>
            <th>Child Development Worker</th>
            <th>Encoder</th>
            <th>LGU Focal</th>
            <th>PDO</th>
            <th>Address</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="centers-table">
        @foreach($centersWithRoles as $center)
            <tr>
                <td>{{ $center['center_name'] }}</td>
                <td>{{ $center['worker'] ? $center['worker']->getFullNameAttribute() : 'N/A' }}</td>
                <td>{{ $center['encoder'] ? $center['encoder']->getFullNameAttribute() : 'N/A' }}</td>
                <td>{{ $center['focal'] ? $center['focal']->getFullNameAttribute() : 'N/A' }}</td>
                <td>{{ $center['pdo'] ? $center['pdo']->getFullNameAttribute() : 'N/A' }}</td>
                <td>{{ $center['address'] }}</td>
                <td class="inline-flex items-center justify-center">
                    <div class="flex space-x-3">
                        @can(['edit-child-development-center'])
                            <a href="{{ route('centers.edit', $center['center_id'])}}" class="relative inline-flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="#3968d2" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>

                                <span class="font-semibold text-sm" style="color: #3968d2;">
                                    Edit
                                </span>
                            </a>
                        @endcan
                    </div>
                </td>
            </tr>
        @endforeach
        @if(count($centersWithRoles) <= 0)
            <tr>
                <td class="text-center" colspan="6">
                    @if(empty($search))
                        No Data found
                    @else
                        No search keyword match found.
                    @endif
                </td>
            </tr>
        @endif
    </tbody>
</table>





