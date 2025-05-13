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
            <th>Child Development Worker</th>
            <th>Encoder</th>
            <th>LGU Focal</th>
{{--            <th>PDO</th>--}}
            <th>Address</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="centers-table">
        @forelse($centersWithRoles as $center)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $center['center_name'] }}</td>
                <td>{{ $center['worker'] ? $center['worker']->getFullNameAttribute() : 'N/A' }}</td>
                <td>{{ $center['encoder'] ? $center['encoder']->getFullNameAttribute() : 'N/A' }}</td>
                <td>{{ $center['focal'] ? $center['focal']->getFullNameAttribute() : 'N/A' }}</td>
{{--                <td>{{ $center['pdo'] ? $center['pdo']->getFullNameAttribute() : 'N/A' }}</td>--}}
                <td>{{ $center['address'] }}</td>
                <td class="">
                    <div class="flex space-x-3">
                        @can(['edit-child-development-center'])
                            <form id="center_id-{{ $center['center_id'] }}" action="{{ route('centers.show') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="center_id" value="{{ $center['center_id'] }}">
                                <button type="submit" class="flex edit-child-btn" onclick="editCenter('{{ $center['center_id'] }}')" >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="#3968d2" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    <span class="font-semibold text-sm" style="color: #3968d2;"> Edit </span>
                                </button>
                            </form>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td class="text-center"><b>No CDC/SNP found.</b></td>
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
            function editCenter(centerID) {
                localStorage.setItem('center_id', centerID);

                document.getElementById('center_id_' + centerID).value = centerID;

                document.getElementById('center_id-' + centerID).submit();
            }
        </script>
    </tbody>
</table>
{{--<div>
    {{ $centers->links() }}
</div>--}}





