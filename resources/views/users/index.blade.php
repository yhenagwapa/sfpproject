@extends('layouts.app')

@section('content')

    <!-- Page Title -->
    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>'; ">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item active">Accounts</li>
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

    <div class="wrapper">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" id="users-content">
                                <h5 class="card-title">Users</h5>
                                @include('users.partials.users-table', ['users' => $users])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
