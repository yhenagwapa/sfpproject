@extends('layouts.app')

@section('content')
    <!-- Page Title -->
    <div class="pagetitle">

        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('centers.index') }}">Child Development Centers</a></li>
                <li class="breadcrumb-item active uppercase">{{ $center->center_name }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    @if (session('error'))
        <div class="alert alert-danger alert-primary alert-dismissible fade show" id="danger-alert" role="alert">
            {{ session('error') }}
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
            if (alert1) {
                // Automatically close the alert after 3 seconds (3000 milliseconds)
                setTimeout(function() {
                    var bsAlert1 = new bootstrap.Alert(alert1);
                    bsAlert1.close();
                }, 2000);
            }
            if (alert2) {
                // Automatically close the alert after 3 seconds (3000 milliseconds)
                setTimeout(function() {
                    var bsAlert2 = new bootstrap.Alert(alert2);
                    bsAlert2.close();
                }, 2000);
            }
        });
    </script>

    <!-- Center Details Form -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <div class="col-md-12 flex text-right">
                            <a href={{ route('centers.index') }} class="flex italic" style="text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1e9730" class="mr-1 mt-1 size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                </svg>
                                <span class="text-green-600">
                                    Back
                                </span>
                            </a>
                        </div>

                        <h5 class="card-title uppercase">{{ $center->center_name }}</h5>
                        <div class="row">
                            <div class='col-md-12 mt-2 text-gray-400 text-xs'>Child Development Center Information
                                <hr>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="center_name">Center Name<b class="text-red-600">*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="center_name"
                                    name="center_name" value="{{ $center->center_name }}" readonly>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="center_type">Center Type<b
                                        class='text-red-600'>*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="center_type"
                                    name="center_type" value="{{ $center->center_type }}" readonly>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="assigned_focal_user_id">Assigned LGU Focal<b class='text-red-600'>*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="assigned_focal_user_id"
                                    name="assigned_focal_user_id" value="{{ $assignedFocal?->full_name ?? 'N/A'  }}" readonly>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="assigned_coordinator_user_id">Assigned SFP Coordinator<b class='text-red-600'>*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="assigned_coordinator_user_id"
                                    name="assigned_coordinator_user_id" value="{{ $assignedCoordinator?->full_name ?? 'N/A' }}" readonly>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="assigned_worker_user_id">Assigned Worker<b class='text-red-600'>*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="assigned_worker_user_id"
                                    name="assigned_worker_user_id" value="{{ $assignedWorker?->full_name ?? 'N/A' }}" readonly>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="assigned_encoder_user_id">Assigned Encoder<b class='text-red-600'>*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="assigned_encoder_user_id"
                                    name="assigned_encoder_user_id" value="{{ $assignedEncoder?->full_name ?? 'N/A' }}" readonly>
                            </div>



                            <div class='col-md-12 mt-4 text-gray-400 text-xs'>Address<hr>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="region">Region<b class="text-red-600">*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="region"
                                    name="region" value="{{ $psgcRecord->region_name }}" readonly>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="province">Province<b class="text-red-600">*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="province"
                                    name="province" value="{{ $psgcRecord->province_name }}" readonly>
                            </div>

                            <div class="col-md-6 mt-2 text-sm">
                                <label for="city">City/Municipality<b class="text-red-600">*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="city"
                                    name="city" value="{{ $psgcRecord->city_name }}" readonly>
                            </div>

                            <div class="col-md-6 mt-2 text-sm">
                                <label for="barangay">Barangay<b class="text-red-600">*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="barangay"
                                    name="barangay" value="{{ $psgcRecord->brgy_name }}" readonly>
                            </div>

                            <div class="col-12 mt-2 text-sm">
                                <label for="address">Address<b class="text-red-600">*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="address"
                                    name="address" value="{{ $center->address }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection
