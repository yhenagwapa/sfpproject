<table id='cycle-table' class="table datatable mt-3 text-sm">
    <thead>
        <tr>
            <th><b>Cycle Implementation</b></th>
            @if(auth()->user()->hasRole('admin'))
                <th>Target</th>
                <th>Allocation</th>
            @endif
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="cycle-table">
        @foreach ($allCycles as $cycle)
            <tr>
                <td>{{ $cycle->cycle_name }}</td>
                @if(auth()->user()->hasRole('admin'))
                    <td>{{ $cycle->cycle_target }}</td>
                    <td>{{ $cycle->cycle_allocation }}</td>
                @endif
                <td>{{ $cycle->cycle_status }}</td>
                <td>
                    <div class="flex space-x-2">
                        @can('edit-cycle-implementation')
                            <a href="{{ route('cycle.edit', $cycle->id) }}" class="relative group">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="#3968d2" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                                </button>
                                <div
                                    class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1">
                                    Edit
                                </div>
                            </a>
                        @endcan
                        @can('view-cycle-implementation')
                            <a href="{{ route('reports.index') }}" class="relative group">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="#3968d2" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                </button>
                                <div
                                    class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1">
                                    View
                                </div>
                            </a>
                        @endcan
                    </div>
                </td>
            </tr>
        @endforeach
        @if (count($allCycles) <= 0)
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