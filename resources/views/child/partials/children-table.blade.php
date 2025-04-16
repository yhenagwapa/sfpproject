<div class="row">
    <form class="row" id="search-form" action="{{ route('child.index') }}" method="GET">
        <div class="col-md-6 text-sm flex">
            <label for="center_name" class="text-base mt-2 mr-2">CDC/SNP:</label>
            <select class="form-control" name="center_name" id="center_name" onchange="clearSearchAndSubmit(this)">
                <option value="all_center" {{ request('cdcId') == 'all_center' ? 'selected' : '' }}>Select a Child Development Center</option>
                @foreach ($centerNames as $center)
                    <option value="{{ $center->id }}"
                        {{ old('center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                        {{ $center->center_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
        </div>
        <div class="col-md-4 flex">
            <label for="q-input" class="text-base mt-2 mr-2">Search:</label>
            <input type="text" name="search" id="q-input" value="{{ request('search') }}" placeholder="Search" class="form-control rounded border-gray-300"
            autocomplete="off">
        </div>
    </form>
</div>

<script>
    function clearSearchAndSubmit(selectElement) {
        const form = selectElement.form;
        const searchInput = form.querySelector('input[name="search"]');
        if (searchInput) searchInput.value = '';
        form.submit();
    }
</script>


<table id='children-table' class="table datatable mt-3 text-left">
    <thead class="text-base">
        <tr>
            <th>No.</th>
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

        @forelse ($children as $child)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $child->full_name }}</td>
                <td>{{ $child->sex->name }}</td>
                <td>{{ \Carbon\Carbon::parse($child->date_of_birth)->format('m-d-Y') }}</td>
                <td>{{ optional($child->nutritionalStatus->first())->weight_for_age }}</td>
                <td>{{ optional($child->nutritionalStatus->first())->weight_for_height }}</td>
                <td>{{ optional($child->nutritionalStatus->first())->height_for_age }}</td>

                <td class="inline-flex">
                    <div class="flex space-x-3">
                        {{-- @dd(session('temp_can_edit')) --}}
                        @if(session('temp_can_edit') || auth()->user()?->can('edit-child'))
                            @if($child->edit_counter != 2)
                                <form id="editChild-{{ $child->id }}" action="{{ route('child.show') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="child_id" value="{{ $child->id }}">
                                    <button type="submit" class="flex edit-child-btn" onclick="ediChild('{{ $child->id }}')" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="#3968d2" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        <span class="font-semibold text-sm" style="color: #3968d2;"> Edit </span>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="flex" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="#565657" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    <span class="font-semibold text-sm" style="color: #565657;"> Edit </span>
                                </button>
                            @endif
                        @endif

                        {{-- @canany(['create-nutritional-status', 'edit-nutritional-status']) --}}
                            <form id="childNS-{{ $child->id }}" action="{{ route('nutritionalstatus.index') }}" method="POST" class="inline">
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
        <script>
            function editChild(childID) {
                localStorage.setItem('child_id', childID);

                document.getElementById('child_id_' + childID).value = childID;

                document.getElementById('editChild-' + childID).submit();
            }
        </script>
    </tbody>
</table>

<div>
    {{ $children->links() }}
</div>
