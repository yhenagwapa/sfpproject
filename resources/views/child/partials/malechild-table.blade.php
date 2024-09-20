{{-- <div class="col-6 mt-4 d-flex justify-content-end align-items-center">
    <form method="GET" action="{{ route('child.search') }}" class="d-flex w-100">
        <input type="search" name="search" id='search' class="form-control me-2 rounded" placeholder="Search"
            value="{{ $search }}">
        <button type="submit" class="text-white bg-blue-600 rounded px-3 min-h-9">Search</button>
    </form>
</div> --}}
<table id='maleChildren-table' class="table datatable mt-3 text-center">
    <thead class="text-base">
        <tr>
            <th rowspan="2">
                <b>Child Name</b>
            </th>
            <th rowspan="2">Sex</th>
            <th rowspan="2" data-type="date" data-format="MM/DD/YYYY">Date of Birth</th>
            <th colspan="3"> Nutritional Status</th>
            @if (!auth()->user()->hasRole('lgu focal'))
                <th rowspan="2">Action</th>
            @endif
        </tr>
        <tr>
            <th>Weight for Age</th>
            <th>Weight for Height</th>
            <th>Height for Age</th>
        </tr>

    </thead>
    <tbody class="maleChildren-table text-sm">
        @foreach ($maleChildren as $maleChild)
            <tr>
                <td>{{ $maleChild->full_name }}</td>
                <td>{{ $maleChild->sex->name }}</td>
                <td>{{ $maleChild->date_of_birth }}</td>
                <td></td>
                <td></td>
                <td></td>
                @if (!auth()->user()->hasRole('lgu focal'))
                    <td>
                        <div class="flex space-x-2">
                            @canany(['edit-child'])
                                <a href="{{ route('child.show', $maleChild->id) }}" class="relative group">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="#3968d2" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        
                                    </button>
                                    <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1">
                                        Edit
                                    </div>
                                </a>
                            @endcanany

                            @can(['nutrition-status-entry'])
                                <a href="{{ route('nutritionalstatus.index', $maleChild->id) }}" class="relative group">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="#1e9730" class="w-6 h-6">
                                            <path d="M3 17L8 12L11 15L17 9M17 9H13M17 9V13M6 21H18C19.6569 21 21 19.6569 21 18V6C21 4.34315 19.6569 3 18 3H6C4.34315 3 3 4.34315 3 6V18C3 19.6569 4.34315 21 6 21Z"></path>
                                        </svg>
                                    </button>
                                    <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap">
                                        Nutritional Status
                                    </div>
                                </a>
                            @endcan
                        
                            @canany('add-attendance')
                                <a href="{{ route('attendance.index', $maleChild->id) }}" class="relative group">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="#eead30" class="w-6 h-6">
                                        <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2" />  <rect x="9" y="3" width="6" height="4" rx="2" />  <path d="M9 14l2 2l4 -4" />
                                    </svg>
                                    <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1">
                                        Attendance
                                    </div>
                                </a>
                            @endcanany
                        </div>
                    </td>
                @endif
            </tr>
        @endforeach
        @if (count($maleChildren) <= 0)
            <tr>
                <td class="text-center" colspan="6">
                    @if (empty($search))
                        No Data found
                    @else
                        No search keyword match found.
                    @endif
                </td>
            </tr>
        @endif
    </tbody>
</table>