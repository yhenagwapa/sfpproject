@extends('layouts.app')

@section('content')


    <!-- Page Title -->
    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>'; ">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item active">Activity Logs</li>
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

    <!-- Alert Auto-Close Script -->
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

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Activity Logs</h5>
                            <div id="activitylogs" class="table-responsive">
                                @include('activitylogs.partials.activitylogs-table', ['groupedActivities' => $groupedActivities])
                            </div>
{{--                            <div class="pagination-links">
                                {{ $activities->links() }}
                            </div>--}}
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
                $("#activitylogs-table").DataTable({
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
        });
    </script>

@endsection
