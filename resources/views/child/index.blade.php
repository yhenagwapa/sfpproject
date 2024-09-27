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
                            <div class="col-md-6 mt-4 d-flex align-items-center">
                                {{-- @can(['create-child']) --}}
                                <a href="{{ route('child.create') }}"><button type="button"
                                        class="bg-blue-600 text-white rounded px-3 min-h-9"><i
                                            class="bi bi-plus-circle"></i>Add Child Profile</button></a>
                                {{-- @endcan --}}
                            </div>
                            {{-- <div class="col-md-6 mt-4 d-flex align-items-center">
                                    <form id="searchForm" method="GET" action="{{ route('child.search') }}">
                                        <input type="search" name="search" id='search'
                                            class="form-control me-2 rounded" placeholder="Search"
                                            value="{{ request()->input('search') }}">
                                        <button type="submit"
                                            class="text-white bg-blue-600 rounded px-3 min-h-9">Search</button>
                                    </form>
                                </div> --}}
                            @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                <div class="col-md-6 mt-4 text-sm">
                                    <form action="{{ route('child.filterByCdc') }}" method="POST">
                                        @csrf
                                        <label for="center_name">Filter Children:</label>
                                        <select class="form-control" name="center_name" id="center_name"
                                            onchange="this.form.submit()">
                                            <option value="all_center" selected>Select a Child Development Center
                                            </option>
                                            @foreach ($centers as $center)
                                                <option value="{{ $center->id }}"
                                                    {{ old('center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                                                    {{ $center->center_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            @endif

                            <h1 class="card-title mt-3 mb-0">Male Children<h1>
                                    <div class="col-md-12" id="maleChildren-table">
                                        @include('child.partials.malechild-table', [
                                            'maleChildren' => $maleChildren,
                                        ])
                                    </div>
                                    <div class="mt-3">
                                        {{ $maleChildren->links() }}
                                    </div>
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
                                    <div class="mt-3">
                                        {{ $femaleChildren->links() }}
                                    </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- <script>
        $(document).ready(function() {
            $('#searchButton').on('click', function() {
                let searchUrl = "{{ route('child.search') }}";
                let query = $('#search').val();

                $.ajax({
                    url: searchUrl,
                    method: 'GET',
                    data: { search: query },
                    success: function(response) {
                        $('#maleChildren-table').html(response.maleChildrenTable);
                        $('#femaleChildren-table').html(response.femaleChildrenTable);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });

        });
    </script> --}}


    {{-- @vite(['resources/js/app.js']) --}}


</main><!-- End #main -->
