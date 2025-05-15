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
                            {{-- <div class="row"> --}}
                                <div class="col-md-12 mt-4">
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
                                    </form>
                                </div>

                            {{-- </div> --}}

                            <div class="table-responsive">
                                <table id='children-table' class="table datatable mt-3 text-left text-sm">
                                    <thead class="text-sm">
                                    <tr>
                                        <th>ID</th>
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
                                            <td>{{ $child->id }}</td>
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
                                                            <span class="font-semibold text-sm" style="color: #1e9730;">Nutritional Status</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <script>
                                    function editChild(childID) {
                                        localStorage.setItem('child_id', childID);

                                        document.getElementById('child_id_' + childID).value = childID;

                                        document.getElementById('editChild-' + childID).submit();
                                    }
                                </script>
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
                order: [[0, 'asc']],
                columnDefs: [
                    {
                        orderSequence: ["desc", "asc"]
                    },
                ],
                info: false
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
