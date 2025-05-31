@extends('layouts.app')

@section('content')
    <!-- Begin section -->

    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>'; margin-bottom: 1rem;">
            <ol class="breadcrumb p-0">
                <li class="breadcrumb-item active">Children</li>
            </ol>
        </nav>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-primary alert-dismissible fade show" id="success-alert" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 2000);
            }
        });
    </script>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-blue-600">Children</h5>
                            <div class="col-md-6">
                                @can('create-child')
                                    <a href="{{ route('child.create') }}">
                                        <button type="button" class="bg-blue-600 text-white rounded px-3 min-h-9">
                                            <i class="bi bi-plus-circle"></i> Add Child Profile
                                        </button>
                                    </a>
                                    {{-- <a href="{{ route('child.create') }}">
                                                <button type="button" class="bg-yellow-600 text-white rounded px-3 min-h-9">
                                                    <i class="bi bi-plus-circle"></i> Import
                                                </button>
                                            </a> --}}
                                @endcan
                            </div>
                                <div class="col-md-12 mt-4">
                                    <form class="row" id="search-form" action="{{ route('child.index') }}" method="GET">
                                        <div class="col-md-6 text-sm flex">
                                            <label for="center_name" class="text-base mt-2 mr-2">CDC/SNP:</label>
                                            <select class="form-control uppercase" name="center_name" id="center_name" onchange="clearSearchAndSubmit(this)">
                                                <option value="all_center" {{ request('cdcId') == 'all_center' ? 'selected' : '' }}>All Child Development Center</option>
                                                @foreach ($centerNames as $center)
                                                    <option value="{{ $center->id }}"
                                                        {{ old('center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                                                        {{ $center->center_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </form>
                                </div>


                            <div class="table-responsive">
                                <table id='children-table' class="table datatable mt-3 text-sm">
                                    <thead class="text-sm">
                                        <tr>
                                            <th>No.</th>
                                            <th>Child Name</th>
                                            <th>Sex</th>
                                            <th>Date of Birth</th>
                                            <th>CDC/SNP</th>
                                            <th>Funded</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($children)
                                            @foreach ($children as $child)
                                                <tr>
                                                    <td class="text-center"></td>
                                                    <td>{{ $child->full_name }}</td>
                                                    <td>{{ $child->sex->name }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($child->date_of_birth)->format('m-d-Y') }}</td>
                                                    <td>
                                                        {{ $child->records->first()->center->center_name}}
                                                    </td>
                                                    <td>
                                                        {{ $child->records->first()->funded ? 'Yes' : 'No'}}
                                                    </td>
                                                    <td>
                                                        {{ $child->records->first()->status}}
                                                    </td>
                                                    <td>
                                                        <div class="flex space-x-3">
                                                            <form action="{{ route('child.view') }}" method="POST" class="inline">
                                                                @csrf
                                                                <input type="hidden" name="child_id" value="{{ $child->id }}">
                                                                <button class="flex child-ns-btn relative group">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#3968d2" class="w-5 h-5">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                                    </svg>

                                                                    <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                                                        View
                                                                    </div>
                                                                </button>
                                                            </form>

                                                            @if(session('temp_can_edit') || auth()->user()?->can('edit-child'))
                                                                @if($child->edit_counter != 2)
                                                                    <form id="editChild-{{ $child->id }}" action="{{ route('child.show') }}" method="POST" class="inline">
                                                                        @csrf
                                                                        <input type="hidden" name="child_id" value="{{ $child->id }}">
                                                                        <button type="submit"class="flex edit-child-btn relative group">
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
                                                                @else
                                                                    <button class="flex relative group" disabled>
                                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                            stroke-width="2" stroke="#D1D5DB80" class="w-5 h-5">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                                        </svg>

                                                                    </button>
                                                                @endif
                                                            @endif

                                                            {{-- @canany(['create-nutritional-status', 'edit-nutritional-status']) --}}
                                                            <form id="childNS-{{ $child->id }}" action="{{ route('nutritionalstatus.create') }}" method="POST" class="inline">
                                                                @csrf
                                                                <input type="hidden" name="child_id" value="{{ $child->id }}">
                                                                <button class="flex child-ns-btn relative group">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                        stroke-width="2" stroke="#1e9730" class="w-5 h-5">
                                                                        <path
                                                                            d="M3 17L8 12L11 15L17 9M17 9H13M17 9V13M6 21H18C19.6569 21 21 19.6569 21 18V6C21 4.34315 19.6569 3 18 3H6C4.34315 3 3 4.34315 3 6V18C3 19.6569 4.34315 21 6 21Z">
                                                                        </path>
                                                                    </svg>
                                                                    <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                                                        Nutritional Status
                                                                    </div>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite(['resources/js/app.js'])
    <script>

        jQuery(document).ready(function () {
            jQuery("#children-table").DataTable({
                paging: true,             // Enable paging
                pageLength: 10,           // Show 10 entries per page
                lengthChange: false,      // Hide the dropdown to change entry count
                searching: true,
                order: [[4, 'asc']],
                columnDefs: [
                    {
                        orderSequence: ["desc", "asc"]
                    },
                ],
                info: false,
                rowCallback: function(row, data, index) {
                    var table = $('#children-table').DataTable();
                    if (data && Object.keys(data).length !== 0) {
                        $('td:eq(0)', row).html(table.page.info().start + index + 1);
                    } else {
                        $('td:eq(0)', row).html('');
                    }
                }
            });
        });

        function clearSearchAndSubmit(selectElement) {
            const form = selectElement.form;
            const searchInput = form.querySelector('input[name="search"]');
            if (searchInput) searchInput.value = '';
            form.submit();
        }
    </script>
@endsection <!-- End section -->
