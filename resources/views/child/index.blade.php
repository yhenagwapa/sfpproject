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
            <li class="breadcrumb-item active"><a href="#">Children</a></li>
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
                                        <a href="{{ route('child.create') }}"><button type="button"
                                                class="bg-blue-600 text-white rounded px-3 min-h-9"><i
                                                    class="bi bi-plus-circle"></i>Add Child Profile</button></a>
                                    @endcan
                                </div>
                                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                    <form action="{{ route('child.filterByCdc') }}" method="POST">
                                        @csrf
                                        <div class="col-6 mt-4 d-flex align-items-center justify-end">
                                            <select class="form-control" name="center_name" id="center_name" onchange="this.form.submit()">
                                                <option value="" disabled selected>Select a Child Development Center</option>
                                                @foreach ($centers as $center)
                                                    <option value="{{ $center->id }}" {{ (old('center_name') == $center->id || $cdcId == $center->id) ? 'selected' : '' }}>
                                                        {{ $center->center_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </form>
                                @endif
                            </div>
                            <h1 class="card-title mt-3 mb-0">Male Children<h1>
                                    <div class="col-md-12" id="maleChildren-table">
                                        @include('child.partials.malechild-table', [
                                            'maleChildren' => $maleChildren,
                                        ])
                                    </div>
                                    {{-- <div class="mt-3">
                                {{ $maleChildren->links() }}
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h1 class="card-title mt-3 mb-0">Female Children<h1>
                                    <div class="col-md-12" id="femaleChildren-table">
                                        @include('child.partials.femalechild-table', [
                                            'femaleChildren' => $femaleChildren,
                                        ])
                                    </div>
                                    {{-- <div class="mt-3">
                                {{ $femaleChildren->links() }}
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    
    
    {{-- @vite(['resources/js/app.js']) --}}


</main><!-- End #main -->
