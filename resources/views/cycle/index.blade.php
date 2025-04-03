@extends('layouts.app')

@section('content')

    <div class="pagetitle">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item active">Implementations</li>
            </ol>
        </nav>
    </div>


    <!-- Success Alert -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="success-alert" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(() => {
                    new bootstrap.Alert(alert).close();
                }, 2000);
            }
        });
    </script>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

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
                            <div class="table-responsive" id="centers-table">
                                @include('cycle.partials.cycle-table', ['allCycles' => $allCycles])
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Milk Feeding Card -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Milk Feeding</h5>
                            <div class="table-responsive" id="milk-feeding-table">
                                @include('milkfeedings.partials.milk-feeding-table', ['milkFeedings' => $milkFeedings])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/js/app.js'])
@endsection
