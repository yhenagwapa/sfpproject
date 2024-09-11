@extends('layouts.app')

<main id="main" class="main">

    @if (session('success'))
        <div class="alert alert-success alert-primary alert-dismissible fade show" id="success-alert" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var alert = document.getElementById('success-alert');
            if (alert) {
                // Automatically close the alert after 3 seconds (3000 milliseconds)
                setTimeout(function() {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 2000);
            }
        });
    </script>

    <nav style="--bs-breadcrumb-divider: '>';">
        <ol class="breadcrumb mb-3 p-0">
            <li class="breadcrumb-item active"><a href="#">Child Development Centers</a></li>
        </ol>
    </nav>

    <div class="wrapper">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mt-4 d-flex align-items-center">
                                    @can(['create-child'])
                                        <a href="{{ route('centers.create') }}"><button type="button"
                                                class="bg-blue-600 text-white rounded px-3 min-h-9"><i
                                                    class="bi bi-plus-circle"></i>Add Child Development Center</button></a>
                                    @endcan
                                </div>
                            </div>
                            @php
                                $children = App\Models\ChildDevelopmentCenter::all(); // or use a method to fetch the data
                            @endphp
                            <div class="col-md-12" id="children-table">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite(['resources/js/app.js'])

</main><!-- End #main -->
