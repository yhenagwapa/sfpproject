@extends('layouts.app')

@section('content')

    <!-- Breadcrumb -->
    <div class="pagetitle">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item active">Generated Reports</li>
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
                            <h5 class="card-title">Generated Reports</h5>

                            <div class="flex space-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#FF0000" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                                <b class="text-red-600">After downloading the report/s, it will be deleted from the system.</b>
                            </div>

                            <!-- Centers Table -->
                            <div class="table-responsive">
                                @include('reports.partials.generated-table', ['pdfFiles' => $pdfFiles])
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
                    searching: false,
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
