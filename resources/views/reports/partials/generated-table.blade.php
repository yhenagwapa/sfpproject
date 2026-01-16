

<table id='generated-table' class="table datatable mt-3 text-sm">
    <thead>
        <tr>
            <th>No.</th>
            <th><b>Report</b></th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="generated-table">
        @forelse($pdfFiles as $index => $file)
            <tr id="file-row-{{ $file['name'] }}">
                <td>{{ $index + 1 }}</td>
                <td>{{ $file['name'] }}</td>
                <td>
                    <form method="POST" action="{{ route('reports.download', $file['name']) }}">
                        @csrf
                        <input type="hidden" name="fileName" value="{{ $file['name'] }}">
                        <button type="submit" class="btn btn-sm btn-primary">
                            Download
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-gray-500">
                    No generated reports available.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<script>
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            setTimeout(() => location.reload(), 500); // reload after download starts
        });
    });
</script>



