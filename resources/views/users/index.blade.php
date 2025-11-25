@extends('layouts.app')

@section('content')
    <!-- Begin section -->

    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>'; margin-bottom: 1rem;">
            <ol class="breadcrumb p-0">
                <li class="breadcrumb-item active">Accounts</li>
            </ol>
        </nav>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-primary alert-dismissible fade show" id="danger-alert" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" id="warning-alert" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-primary alert-dismissible fade show" id="success-alert" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var alert1 = document.getElementById('success-alert');
            var alert2 = document.getElementById('danger-alert');
            var alert3 = document.getElementById('warning-alert');
            if (alert1) {
                setTimeout(function() {
                    var bsAlert1 = new bootstrap.Alert(alert1);
                    bsAlert1.close();
                }, 3000);
            }
            if (alert2) {
                setTimeout(function() {
                    var bsAlert2 = new bootstrap.Alert(alert2);
                    bsAlert2.close();
                }, 3000);
            }
            if (alert3) {
                setTimeout(function() {
                    var bsAlert3 = new bootstrap.Alert(alert3);
                    bsAlert3.close();
                }, 3000);
            }
        });
    </script>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Users<br>Total No. of Users: <b class="uppercase">{{ $userCount ?? 0 }}</b></h5>

                        <div class="col-md-6">
                            @can('create-user')
                                <a href="{{ route('users.create') }}">
                                    <button type="button" class="bg-blue-600 text-white rounded px-3 min-h-9">
                                        <i class="bi bi-plus-circle"></i> Add User
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="table-responsive">
                            @include('users.partials.users-table', ['roles' => $roles])
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection <!-- End section -->
