<div class="row">
    <form class="row" id="search-form" action="{{ route('centers.index') }}" method="GET">
        <div class="col-md-6 text-sm flex">
            {{-- <label for="center_name" class="text-base mt-2 mr-2">CDC/SNP:</label>
            <select class="form-control" name="center_name" id="center_name" onchange="clearSearchAndSubmit(this)">
                <option value="all_center" {{ request('cdcId') == 'all_center' ? 'selected' : '' }}>Select a Child Development Center</option>
                @foreach ($centerNames as $center)
                    <option value="{{ $center->id }}"
                        {{ old('center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                        {{ $center->center_name }}
                    </option>
                @endforeach
            </select> --}}
        </div>
        <div class="col-md-2">
        </div>
{{--        <div class="col-md-4 flex">
            <label for="q-input" class="text-base mt-2 mr-2">Search:</label>
            <input type="text" name="search" id="q-input" value="{{ request('search') }}" placeholder="Search" class="form-control rounded border-gray-300"
            autocomplete="off">
        </div>--}}
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

<table id='centers-table' class="table datatable mt-3 text-sm">
    <thead>
        <tr>
            <th>No.</th>
            <th><b>Child Development Centers</b></th>
            <th>Address</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="centers-table">
        @foreach($centersWithRoles as $center)
            <tr>
                <td class="text-center"></td>
                <td>{{ $center['center_name'] }}</td>
                <td>{{ $center['address'] }}</td>
                <td>{{ $center['center_type'] }}</td>
                <td>
                    <div class="flex space-x-3">
                        <form action="{{ route('centers.view') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="center_id" value="{{ $center['center_id'] }}">
                            <button class="flex relative group">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#3968d2" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>

                                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                    View
                                </div>
                            </button>
                        </form>
                        @can(['edit-child-development-center'])
                            <form id="center_id-{{ $center['center_id'] }}" action="{{ route('centers.show') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="center_id" value="{{ $center['center_id'] }}">
                                <button type="submit" class="flex edit-child-btn relative group">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="#00000099" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                        Edit
                                    </div>
                                </button>
                            </form>
                        @endcan
                        @if (auth()->user()->hasRole('admin'))
                            <button type="submit" class="flex relative group" data-bs-toggle="modal"
                            data-bs-target="#deactivateCenterModal-{{ $center['center_id'] }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#AA4A44" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                    Deactivate
                                </div>
                            </button>
                        @endif
                        <!-- Modal to Deactivate Center -->
                        <div class="modal fade" id="deactivateCenterModal-{{ $center['center_id'] }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-red-600">Confirmation</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        This center currently has
                                        <b class="text-red-600">{{ $center['children_count'] }}</b>
                                        children enrolled. Are you sure you want to deactivate
                                        <b class="text-red-600">{{ $center['center_name'] }}</b>?
                                    </div>
                                    <div class="modal-footer">
                                        <button id="confirmCenterDeactivation-{{ $center['center_id'] }}" type="button"
                                            class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                        <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9"
                                            data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form id="deactivateCenterForm-{{ $center['center_id'] }}" method="POST"
                            action="{{ route('centers.deactivate-center') }}">
                            @csrf
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="center_id" value="{{ $center['center_id'] }}">
                        </form>

                        <script>
                            document.getElementById('confirmCenterDeactivation-{{ $center["center_id"] }}').addEventListener('click', function() {
                                document.getElementById('deactivateCenterForm-{{ $center["center_id"] }}').submit();
                            });
                        </script>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>




