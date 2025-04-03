@extends('layouts.app')

@section('content')
    <!-- Begin section -->

    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>'; margin-bottom: 1rem;">
            <ol class="breadcrumb p-0">
                <li class="breadcrumb-item active">Children</li>
            </ol>
        </nav>
    </div>

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
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 2000);
            }
        });
    </script>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Children</h5>
                            <div class="col-md-6">
                                @can('create-child')
                                    <a href="{{ route('child.create') }}">
                                        <button type="button" class="bg-blue-600 text-white rounded px-3 min-h-9">
                                            <i class="bi bi-plus-circle"></i> Add Child Profile
                                        </button>
                                    </a>
                                    {{-- <a href="{{ route('child.create') }}">
                                                <button type="button" class="bg-yellow-600 text-white rounded px-3 min-h-9">
                                                    <i class="bi bi-plus-circle"></i> Import
                                                </button>
                                            </a> --}}
                                @endcan
                            </div>
                            <div class="row">
                                <div class="col-md-1 mt-4">
                                    <label for="center_name" class="form-label text-sm">Filter Children:</label>
                                </div>
                                <div class="col-md-5 mt-3">
                                    <form action="{{ route('child.index') }}" method="GET" class="ro">
                                        @csrf
                                        <select class="form-control" name="center_name" id="center_name"
                                            onchange="this.form.submit()">
                                            <option value="all_center" selected>Select a Child Development Center</option>
                                            @foreach ($centerNames as $center)
                                                <option value="{{ $center->id }}"
                                                    {{ old('center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                                                    {{ $center->center_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive" id='children-table'>
                                @include('child.partials.children-table', ['children' => $children])
                            </div>
                            <div class="pagination-links">
                                {{ $children->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite(['resources/js/app.js'])
@endsection <!-- End section -->
