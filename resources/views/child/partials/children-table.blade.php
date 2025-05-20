<table id='children-table' class="table datatable mt-3 text-sm">
    <thead>
        <tr>
            <th>#</th>
            <th>Child Name</th>
            <th>Sex</th>
            <th data-type="date" data-format="MM/DD/YYYY">Date of Birth</th>
            <th>Weight for Age</th>
            <th>Weight for Height</th>
            <th>Height for Age</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

        @php($i=0)
        @forelse ($children as $child)
            @php($i++)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $child->full_name }}</td>
                <td>{{ $child->sex->name }}</td>
                <td>{{ \Carbon\Carbon::parse($child->date_of_birth)->format('m-d-Y') }}</td>
                <td>{{ $child->nutritionalStatus->first()?->weight_for_age }}</td>
                <td>{{ $child->nutritionalStatus->first()?->weight_for_height }}</td>
                <td>{{ $child->nutritionalStatus->first()?->height_for_age }}</td>

                <td class="inline-flex">
                    <div class="flex space-x-3">
                        @if(session('temp_can_edit') || auth()->user()?->can('edit-child'))
                            @if($child->edit_counter != 2 || auth()->user()->hasRole('admin'))
                                <form id="editChild-{{ $child->id }}" action="{{ route('child.show') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="child_id" value="{{ $child->id }}">
                                    <a class="flex bg-white text-blue-600 rounded px-3 min-h-9 items-center edit-child-btn relative group" onclick="this.form('submit')" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="#3968d2" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                            Edit
                                        </div>
                                    </a>
                                </form>



                            @else
                                <button type="button" class="flex" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="#565657" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    <span class="font-semibold text-sm" style="color: #3c3c3d;"> Edit </span>
                                </button>
                            @endif
                        @endif

                        {{-- @canany(['create-nutritional-status', 'edit-nutritional-status']) --}}
                            <form id="childNS-{{ $child->id }}" action="{{ route('nutritionalstatus.create') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="child_id" value="{{ $child->id }}">
                                <button type="submit" class="flex child-ns-btn" onclick="editChild('{{ $child->id }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="#1e9730" class="w-5 h-5">
                                        <path
                                            d="M3 17L8 12L11 15L17 9M17 9H13M17 9V13M6 21H18C19.6569 21 21 19.6569 21 18V6C21 4.34315 19.6569 3 18 3H6C4.34315 3 3 4.34315 3 6V18C3 19.6569 4.34315 21 6 21Z">
                                        </path>
                                    </svg>
                                    <span class="font-semibold text-sm" style="color: #1e9730;">
                                        Nutritional Status
                                    </span>
                                </button>
                            </form>
                        {{-- @endcanany --}}
                        {{-- @can(['edit-nutritional-status'])
                            @if ($child->nutritionalStatus->isNotEmpty())
                                <a href="{{ route('nutritionalstatus.edit', $child->id) }}"
                                    class="relative inline-flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="#1e9730" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    <span class="font-semibold text-sm" style="color: #1e9730;">
                                        Edit NS
                                    </span>
                                </a>
                            @endif
                        @endcan --}}

                        {{-- @can('add-attendance')
                            <a href="{{ route('attendance.index', $child->id) }}"
                                class="relative inline-flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="#eead30" class="w-5 h-5">
                                    <path stroke="none" d="M0 0h24v24H0z" />
                                    <path
                                        d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2" />
                                    <rect x="9" y="3" width="6" height="4" rx="2" />
                                    <path d="M9 14l2 2l4 -4" />
                                </svg>
                                <span class="font-semibold text-sm" style="color: #eead30;">
                                    Attendance
                                </span>
                            </a>
                        @endcan --}}
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td class="text-center">No children found.</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
            </tr>
        @endforelse
    </tbody>
</table>

<div>
    {{ $children->links() }}
</div>
