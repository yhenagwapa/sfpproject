@extends('layouts.app')

@section('content')

    <!-- Breadcrumb -->
    <div class="pagetitle">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item active">Child Development Centers</li>
            </ol>
        </nav>
    </div>

    <!-- Success Alert -->
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
                setTimeout(() => new bootstrap.Alert(alert).close(), 2000);
            }
        });
    </script>



    {{-- <div class="wrapper"> --}}
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Child Development Centers and Supervised Neighborhood Play</h5>

                            <!-- Add Center Button -->
                            <div class="row">
                                <div class="col-6 mt-4 d-flex align-items-center">
                                    @can('create-child-development-center')
                                        <a href="{{ route('centers.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-2"></i>Add Center
                                        </a>
                                    @endcan
                                </div>
                            </div>

                            <!-- Centers Table -->
                            <div class="table-responsive">
                                @include('centers.partials.table', ['centersWithRoles' => $centersWithRoles])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    {{-- </div> --}}

    <!-- Scripts -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    {{-- @vite(['resources/js/app.js']) --}}

    <script>
        (function(){
          let timer;
          $('#q-input').on('keyup', function(){
            clearTimeout(timer);
            timer = setTimeout(function(){
              $('#search-form').submit();
            }, 300);
          });
        })();
    </script>

    <script>
        window.addEventListener('load', function () {
            $(document).ready(function () {
                $("#centers-table").DataTable({
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
                        var table = $('#centers-table').DataTable();
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
