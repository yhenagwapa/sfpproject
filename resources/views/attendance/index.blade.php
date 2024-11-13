@extends('layouts.app')

@section('content')
    <div class="pagetitle">

        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('child.index') }}">Children</a></li>
                <li class="breadcrumb-item active">{{ $child->full_name }}</li>
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
    <div class="wrapper">
        <section class="section">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h5 class='col-md-12'>Attendance</h5>
                            </div>

                            @can(['add-attendance'])
                                <form method="post" action="{{ route('attendance.storeCycleAttendance', $child->id) }}"
                                    class="flex flex-wrap items-center gap-4">
                                    @csrf
                                    <input type="hidden" name="child_id" value="{{ $child->id }}">

                                    <!-- Feeding Date -->
                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="attendance_date" class="col-form-label">Feeding date:<b
                                                class="text-red-600">*</b></label>
                                        <input type="date" class="form-control rounded border-gray-300" id="attendance_date"
                                            name='attendance_date' value="{{ old('attendance_date') }}" max="{{ date('Y-m-d') }}">
                                        @error('attendance_date')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Save Button -->
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="text-white bg-blue-600 rounded px-3 min-h-9"
                                            data-bs-toggle="modal" data-bs-target="#verticalycentered1">Submit</button>
                                        <button type="reset"
                                            class="text-white bg-gray-600 rounded px-3 min-h-9">Cancel</button>
                                    </div>

                                    <div class="modal fade" id="verticalycentered1" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-red-600">Confirmation</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to save these details?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit"
                                                        class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                                    <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endcan

                            @if (!is_null($child->milk_feeding_id))
                                <hr class='mt-5 mb-3'>

                                @can(['add-attendance'])
                                    <form method="post" action="{{ route('attendance.storeMilkAttendance', $child->id) }}"
                                        class="flex flex-wrap items-center gap-4">
                                        @csrf
                                        <input type="hidden" name="child_id" value="{{ $child->id }}">

                                        <!-- Feeding Date -->
                                        <div class="col-md-12 mt-2 text-sm">
                                            <label for="milk_attendance_date" class="col-form-label">Milk feeding date:<b
                                                    class="text-red-600">*</b></label>
                                            <input type="date" class="form-control rounded border-gray-300"
                                                id="milk_attendance_date" name='milk_attendance_date'
                                                value="{{ old('milk_attendance_date') }}" max="{{ date('Y-m-d') }}">
                                            @error('milk_attendance_date')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Save Button -->
                                        <div class="col-md-12 text-right">
                                            <button type="button" class="text-white bg-blue-600 rounded px-3 min-h-9"
                                                data-bs-toggle="modal" data-bs-target="#verticalycentered2">Submit</button>
                                            <button type="reset"
                                                class="text-white bg-gray-600 rounded px-3 min-h-9">Cancel</button>
                                        </div>

                                        <div class="modal fade" id="verticalycentered2" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-red-600">Confirmation</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to save these details?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit"
                                                            class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                                        <button type="button"
                                                            class="text-white bg-gray-600 rounded px-3 min-h-9"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>

                <div class="{{ is_null($child->milk_feeding_id) ? 'col-lg-8' : 'col-lg-4' }}">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h5 class='col-md-6'>Feeding Attendance</h5>
                            </div>
                            <div class="col-md-12 table-responsive" id="cycle-table">
                                @include('attendance.partials.cycle-table', [
                                    'cycleAttendances' => $cycleAttendances,
                                ])
                            </div>
                            <div class="mt-3">
                                {{ $cycleAttendances->withQueryString()->links('pagination::simple-bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>

                @if (!is_null($child->milk_feeding_id))
                    <div class="{{ is_null($child->milk_feeding_id) ? 'col-lg-8' : 'col-lg-4' }}">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h5 class='col-md-6'>Milk Feeding Attendance</h5>
                                </div>
                                <div class="col-md-12 table-responsive" id="milk-table">
                                    @include('attendance.partials.milk-table', [
                                        'milkAttendances' => $milkAttendances,
                                    ])
                                </div>
                                <div class="mt-3">
                                    {{ $milkAttendances->withQueryString()->links('pagination::simple-bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
