@extends('layouts.app')

@section('content')

    <!-- Page Title -->
    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>'; ">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item active">Accounts</li>
            </ol>
        </nav>
    </div>

    <!-- Alerts -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" id="danger-alert" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="success-alert" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            ['success-alert', 'danger-alert'].forEach(id => {
                const alertElem = document.getElementById(id);
                if (alertElem) {
                    setTimeout(() => new bootstrap.Alert(alertElem).close(), 2000);
                }
            });
        });
    </script>

    <div class="wrapper">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Users</h5>

                            <div class="row">
                                <div class="col-md-8 mt-3 text-sm">
                                </div>
                  {{--              <div class="col-md-4 mt-3 justify-end">
                                    <form class="flex" id="search-form" method="GET" action="{{ route('users.index') }}">
                                        <label for="q-input" class="text-base mt-2 mr-2">Search:</label>
                                        <input
                                        type="text"
                                        name="search"
                                        id="q-input"
                                        value="{{ request('search') }}"
                                        placeholder="Search"
                                        class="form-control rounded border-gray-300"
                                        autocomplete="off">
                                    </form>
                                </div>--}}
                            </div>

                            <div class="table-responsive">
                                @include('users.partials.users-table', ['users' => $users])
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    @vite(['resources/js/app.js'])

    <script>
        window.addEventListener('load', function () {
            $(document).ready(function () {
                $("#users-table").DataTable({
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
                    info: false,
                    rowCallback: function(row, data, index) {
                        var table = $('#users-table').DataTable();
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
@endsection
