@extends('layouts.app')

@section('content')

    <div class="pagetitle">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item active">Implementations</li>
            </ol>
        </nav>
    </div>

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
        document.addEventListener('DOMContentLoaded', function () {
            ['success-alert', 'danger-alert'].forEach(id => {
                const alert = document.getElementById(id);
                if (alert) {
                    setTimeout(() => {
                        new bootstrap.Alert(alert).close();
                    }, 5000);
                }
            });
        });
    </script>

    <!-- Content Wrapper -->

        <section class="section">
            <div class="row">
                <!-- Cycle Implementations Card -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Implementations</h5>
                            <div class="row">
                                @can('add-cycle-implementation')
                                    <div class="col-6 mb-5 d-flex align-items-center">
                                        <a href="{{ route('cycle.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-2"></i>New Implementation
                                        </a>
                                    </div>
                                @endcan
                            </div>
                            <div class="table-responsive">
                                @include('cycle.partials.cycle-table', ['allCycles' => $allCycles])
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Milk Feeding Card -->
                {{--<div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Milk Feeding</h5>
                            <div class="table-responsive" id="milk-feeding-table">
                                @include('milkfeedings.partials.milk-feeding-table', ['milkFeedings' => $milkFeedings])
                            </div>
                        </div>
                    </div>
                </div>--}}
            </div>
        </section>

    {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}
    {{-- @vite(['resources/js/app.js']) --}}
    <script>
        window.addEventListener('load', function () {
            $(document).ready(function () {
                $("#cycle-table").DataTable({
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
                        var table = $('#cycle-table').DataTable();
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
