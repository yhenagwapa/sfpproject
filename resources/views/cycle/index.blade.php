@extends('layouts.app')

@section('content')

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
            <li class="breadcrumb-item active"><a href="#">Cycle Implementations</a></li>
        </ol>
    </nav>

    <div class="wrapper">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Cycle Implementations</h5>
                            <div class="row">
                                @canany('create-cycle-implementation')
                                    <div class="col-6 mt-4 d-flex align-items-center">
                                        <a href="{{ route('cycle.create')}}"><button type="button"
                                            class="bg-blue-600 text-white rounded px-3 min-h-9"><i
                                            class="bi bi-plus-circle mr-2"></i>New Cycle Implementation</button></a>
                                    </div>
                                @endcanany
                            </div>
                            @php
                                $cycles = App\Models\CycleImplementation::all(); // or use a method to fetch the data
                            @endphp
                            <div class="col-md-12" id="centers-table">
                                @include('cycle.partials.cycle-table', ['cycles' => $cycles])
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
