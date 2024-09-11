<div class="col-6 mt-4 d-flex justify-content-end align-items-center">
    <form method="GET" action="{{ route('child.search') }}" class="d-flex w-100">
        <input type="search" name="search" id='search' class="form-control me-2 rounded" placeholder="Search" value="{{$search}}">
        <button type="submit" class="text-white bg-blue-600 rounded px-3 min-h-9">Search</button>
    </form>
</div>
<table class="table">
    <thead>
        <tr>
            <th><b>Child Development Centers</b></th>
            <th>Province</th>
            <th>City/Municipality</th>
            <th>Barangay</th>
            <th>Address</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="centers-table">
        @foreach($centers as $center)
            <tr>
                <td>{{ $center->name }}</td>
                <td>{{ $center->name }}</td>
                <td>{{ $center->name }}</td>
                <td>{{ $center->name }}</td>
                <td>{{ $center->name }}</td>
                <td>
                    <a href="" >
                        <i class="bi bi-pencil text-white border-2 border-blue-600 bg-blue-600 rounded px-3"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        @if(count($centers) <= 0)
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

<div id="pagination-links">
    {{ $centers->links('vendor.pagination.bootstrap-4') }}
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    $(document).ready(function() {
        var search_ajax;
        $('#search').on('input change', function() {
            var searchTxt = $(this).val();

            // Update URL without reloading
            var newUrl = searchTxt ? `{{ route('child.search') }}?q=${encodeURIComponent(searchTxt)}` : `{{ route('child.search') }}`;
            history.pushState({}, "", newUrl);

            if (search_ajax) {
                search_ajax.abort();
            }

            search_ajax = $.ajax({
                url: newUrl,
                dataType: 'json',
                success: function(resp) {
                    var tblBody = $('.children-table');
                    var paginationLink = $('#pagination-links');
                    tblBody.html('');
                    paginationLink.html('');

                    if (resp.children.length > 0) {
                        resp.children.forEach(child => {
                            var tr = $('<tr>');
                            tr.append(`<td>${child.full_name}</td>`);
                            tr.append(`<td>${child.sex}</td>`);
                            tr.append(`<td>${child.dob}</td>`);
                            tr.append(`<td>${child.weight}</td>`);
                            tr.append(`<td>${child.height}</td>`);
                            tr.append(`<td><a href="#" class="editChildBtn" data-bs-toggle="modal" data-bs-target="#ExtralargeModal" data-id="${child.id}"><i class="bi bi-pencil text-white border-2 border-blue-600 bg-blue-600 rounded px-3"></i></a></td>`);
                            tblBody.append(tr);
                        });
                    } else {
                        tblBody.append('<tr><td class="text-center" colspan="6">No search keyword match found.</td></tr>');
                    }

                    if (resp.pagination_links) {
                        paginationLink.html(resp.pagination_links);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    alert('An error occurred: ' + (jqXHR.responseText || 'Unknown error'));
                }
            });
        });
    });

    // Function to handle modal form update
    function generateUpdateUrl(childId) {
        return `{{ url('child') }}/${childId}`;
    }
</script>

