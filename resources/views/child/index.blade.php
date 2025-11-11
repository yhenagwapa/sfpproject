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

    @if (session('error'))
        <div class="alert alert-danger alert-primary alert-dismissible fade show" id="danger-alert" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" id="warning-alert" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-primary alert-dismissible fade show" id="success-alert" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var alert1 = document.getElementById('success-alert');
            var alert2 = document.getElementById('danger-alert');
            var alert3 = document.getElementById('warning-alert');
            if (alert1) {
                setTimeout(function() {
                    var bsAlert1 = new bootstrap.Alert(alert1);
                    bsAlert1.close();
                }, 3000);
            }
            if (alert2) {
                setTimeout(function() {
                    var bsAlert2 = new bootstrap.Alert(alert2);
                    bsAlert2.close();
                }, 3000);
            }
            if (alert3) {
                setTimeout(function() {
                    var bsAlert3 = new bootstrap.Alert(alert3);
                    bsAlert3.close();
                }, 3000);
            }
        });
    </script>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Center Name: <b class="uppercase">{{ isset($center_name) ? (is_object($center_name) ? $center_name->center_name : $center_name) : 'No Data Available' }}
</b><br>Total No. of Children:
                            <b class="uppercase">{{ $childCount ?? 0 }}</b></b>
                        </h5>
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
                                        @csrf

                                        <div class="col-md-6 text-sm flex">
                                            <label for="center_name" class="text-base mt-2 mr-2">CDC/SNP:</label>
                                            <select class="form-control uppercase" name="center_name" id="center_name" onchange="this.form.submit()">
                                                <option value="" selected>Select CDC/SNP</option>
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
                                @include('child.partials.children-table', ['children' => $children])
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    {{-- @vite(['resources/js/app.js']) --}}
    <script>
        window.addEventListener('load', function () {
            $(document).ready(function () {
                $("#children-table").DataTable({
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
        });
    </script>
@endsection <!-- End section -->
