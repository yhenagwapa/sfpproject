<table id='children-table' class="table datatable mt-3">
    <thead class="text-base">
    <tr>
        <th>No.</th>
        <th>Child Name</th>
        <th>Sex</th>
        <th>Date of Birth</th>
        <th>CDC/SNP</th>
        <th>Funded</th>
        <th>Transferred</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody class="text-sm">
    </tbody>
</table>

<script>
    window.addEventListener('load', function () {
        $(document).ready(function () {
                @if($center_name)
            {
                const table = $("#children-table").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ url('/') }}/api/children',
                        type: 'GET',
                        data: {
                            user_id: {{ auth()->user()->id }},
                            center_name: '{{ is_object($center_name) ? $center_name->id : $center_name }}'
                        },
                        error: function(xhr, error, code) {
                            console.error('DataTables error:', error);
                        }
                    },
                    columns: [
                        {
                            data: 'no',
                            name: 'id',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'child_name',
                            name: 'lastname',
                            render: function(data, type, row) {
                                return data;
                            }
                        },
                        {
                            data: 'sex',
                            name: 'sex_id',
                            orderable: true
                        },
                        {
                            data: 'date_of_birth',
                            name: 'date_of_birth',
                            orderable: true
                        },
                        {
                            data: 'center_name',
                            name: 'center_name',
                            orderable: false
                        },
                        {
                            data: 'funded',
                            name: 'funded',
                            orderable: false
                        },
                        {
                            data: 'has_transferred',
                            name: 'transfer_count',
                            orderable: true,
                            render: function(data, type, row) {
                                if (data === 'Yes') {
                                    return '<span class="badge bg-warning">Yes</span>';
                                }
                                return '<span class="badge bg-secondary">No</span>';
                            }
                        },
                        {
                            data: 'status',
                            name: 'action_type',
                            orderable: true,
                            render: function(data, type, row) {
                                let badgeClass = 'bg-secondary';
                                if (data === 'Active') badgeClass = 'bg-success';
                                else if (data === 'Dropped') badgeClass = 'bg-danger';
                                else if (data === 'Transferred') badgeClass = 'bg-info';

                                return `<span class="badge ${badgeClass}">${data}</span>`;
                            }
                        },
                        {
                            data: null,
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                const isDropped = row.action_type === 'dropped';
                                const canEdit = {{ auth()->user()->can('edit-child') || session('temp_can_edit') ? 'true' : 'false' }};
                                const isAdmin = {{ auth()->user()->hasRole('admin') ? 'true' : 'false' }};
                                const editCounter = row.edit_counter || 0;

                                // View button (always visible)
                                let buttons = `
                                <div class="flex space-x-3">
                                    <form action="{{ route('child.view') }}" method="POST" class="inline">
                                        @csrf
                                <input type="hidden" name="child_id" value="${row.id}">
                                        <button type="submit" class="flex child-ns-btn relative group">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="#3968d2" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                                View
                                            </div>
                                        </button>
                                    </form>
                            `;

                                // Edit button (conditional)
                                if (canEdit) {
                                    if (isDropped && !isAdmin) {
                                        // Dropped child - disabled for non-admin
                                        buttons += `
                                        <button class="flex relative group" disabled>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="#D1D5DB80" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                    `;
                                    } else if (!isAdmin && editCounter >= 2) {
                                        // Edit counter reached limit - disabled for non-admin
                                        buttons += `
                                        <button class="flex relative group" disabled>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="#D1D5DB80" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                    `;
                                    } else {
                                        // Edit button enabled
                                        buttons += `
                                        <form id="editChild-${row.id}" action="{{ route('child.show') }}" method="POST" class="inline">
                                            @csrf
                                        <input type="hidden" name="child_id" value="${row.id}">
                                            <button type="submit" class="flex edit-child-btn relative group">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="#00000099"
                                                    class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                                    Edit
                                                </div>
                                            </button>
                                        </form>
                                    `;
                                    }
                                }

                                // Nutritional Status button (always visible)
                                buttons += `
                                    <form id="childNS-${row.id}" action="{{ route('nutritionalstatus.create') }}" method="POST" class="inline">
                                        @csrf
                                <input type="hidden" name="child_id" value="${row.id}">
                                        <button type="submit" class="flex child-ns-btn relative group">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="#1e9730" class="w-5 h-5">
                                                <path d="M3 17L8 12L11 15L17 9M17 9H13M17 9V13M6 21H18C19.6569 21 21 19.6569 21 18V6C21 4.34315 19.6569 3 18 3H6C4.34315 3 3 4.34315 3 6V18C3 19.6569 4.34315 21 6 21Z">
                                                </path>
                                            </svg>
                                            <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                                Nutritional Status
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            `;

                                return buttons;
                            }
                        }
                    ],
                    order: [[1, 'asc']], // Default sort by name
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    language: {
                        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ children",
                        infoEmpty: "No children to display",
                        infoFiltered: "(filtered from _MAX_ total children)",
                        zeroRecords: "No matching children found",
                        emptyTable: "No children available"
                    },
                    drawCallback: function(settings) {
                        // You can add custom logic after each draw here
                        console.log('Table redrawn');
                    }
                });

                // Optional: Refresh table function
                window.refreshChildrenTable = function() {
                    table.ajax.reload(null, false); // false = stay on current page
                };
            }
            @endif
        });
    });

    // Example delete function (customize based on your needs)
    function deleteChild(childId) {
        if (confirm('Are you sure you want to delete this child?')) {
            // Add your delete logic here
            console.log('Delete child:', childId);
        }
    }
</script>
