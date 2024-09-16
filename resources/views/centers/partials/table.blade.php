
<table id='centers-table' class="table datatable mt-3 text-sm">
    <thead>
        <tr>
            <th><b>Child Development Centers</b></th>
            <th>Child Development Worker</th>
            <th>Address</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="centers-table">
        @foreach($centers as $center)
            <tr>
                <td>{{ $center->center_name }}</td>
                <td>{{ $center->user->full_name }}</td>
                <td>{{ $center->getFullAddress() }}</td>
                <td>
                    <div class="flex space-x-2">
                        @can(['edit-child-development-center'])
                            <a href="{{ route('centers.edit', $center->id)}}" class="relative group">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="#3968d2" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1">
                                    Edit
                                </div>
                            </a>
                        @endcan
                    </div>
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

