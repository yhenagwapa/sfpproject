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
        document.addEventListener('DOMContentLoaded', function () {
            var alert1 = document.getElementById('success-alert');
            var alert2 = document.getElementById('danger-alert');
            if (alert1) {
                // Automatically close the alert after 3 seconds (3000 milliseconds)
                setTimeout(function () {
                    var bsAlert1 = new bootstrap.Alert(alert1);
                    bsAlert1.close();
                }, 2000);
            }
            if (alert2) {
                // Automatically close the alert after 3 seconds (3000 milliseconds)
                setTimeout(function () {
                    var bsAlert2 = new bootstrap.Alert(alert2);
                    bsAlert2.close();
                }, 2000);
            }
        });
    </script>
    <div class="wrapper">
        <section class="section">
            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h5 class='col-md-12'>Attendance</h5>
                            </div>

                            @can(['add-attendance'])
                                <form method="post" action="{{ route('attendance.store', $child->id) }}"
                                    class="flex flex-wrap items-center gap-4">
                                    @csrf
                                    <input type="hidden" name="child_id" value="{{ $child->id }}">

                                    <!-- Feeding Date -->
                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="feeding_date" class="col-form-label">Feeding date:<b
                                                class="text-red-600">*</b></label>
                                        <input type="date" class="form-control rounded border-gray-300" id="feeding_date"
                                            name='feeding_date' value="{{ old('feeding_date') }}">
                                        @error('feeding_date')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- <!-- With Milk Checkbox -->
                                    <div class="col-md-4 mt-2 text-sm">
                                        <label class="form-check-label" for="with_milk">
                                            With milk?
                                        </label>
                                    </div>
                                    <div class="col-md-2 mt-2 text-sm">
                                        <input type="radio" name="with_milk" id="with_milk_yes" value="1" {{ old('with_milk', '0') == '1' ? 'checked' : '' }}>
                                        <label for="with_milk_yes">Yes</label>
                                    </div>
                                    <div class="col-md-2 mt-2 text-sm">
                                        <input type="radio" name="with_milk" id="with_milk_no" value="0" {{ old('with_milk', '0') == '0' ? 'checked' : '' }}>
                                        <label for="with_milk_no">No</label>
                                    </div> --}}

                                    <!-- Save Button -->
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="text-white bg-blue-600 rounded px-3 min-h-9"
                                            data-bs-toggle="modal" data-bs-target="#verticalycentered">Submit</button>
                                        <button type="reset"
                                            class="text-white bg-gray-600 rounded px-3 min-h-9">Cancel</button>
                                    </div>

                                    <div class="modal fade" id="verticalycentered" tabindex="-1">
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
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h5 class='col-md-6'>{{ $child->full_name }}</h5>
                            </div>
                            <div class="col-md-12 table-responsive" id="attendance-table">
                                @include('attendance.partials.attendance-table', ['attendances' => $attendances])
                            </div>
                            <div class="mt-3">
                                {{ $attendances->links() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection