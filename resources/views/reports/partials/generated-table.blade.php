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
            <th><b>Report</b></th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="centers-table">
        @foreach($pdfFiles as $index => $fileName)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $file['name'] }}</td>
                <td>
                    <a href="{{ route('reports.download', $fileName) }}" class="btn btn-sm btn-primary" target="_blank">
                        Download
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>




