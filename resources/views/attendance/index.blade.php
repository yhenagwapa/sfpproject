@extends('layouts.app')

@section('content')

    <main id="main" class="main">

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
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h5 class='col-md-6'>{{ $child->full_name }}</h5>
                                </div>
                                <div class="w-full">
                                    <form method="post" action="{{ route('attendance.store', $child->id) }}" class="flex flex-wrap items-center gap-4">
                                        @csrf
                                        <input type="hidden" name="child_id" value="{{ $child->id }}">
                                        
                                        <!-- Feeding Date -->
                                        <div class="flex items-center space-x-2 w-6/12">
                                            <label for="feeding_date" class="col-form-label">Feeding date:</label>
                                            <input type="date" class="form-control rounded border-gray-300"
                                                id="feeding_date" name='feeding_date' value="{{ old('feeding_date') }}">
                                            @error('feeding_date')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        
                                        <!-- With Milk Checkbox -->
                                        <div class="flex items-center space-x-2 w-4/12">
                                            <input class="form-check-input form-control" type="checkbox" id="with_milk"
                                                name='with_milk' value='1' {{ old('with_milk') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="with_milk">
                                                With milk
                                            </label>
                                        </div>
                                        
                                        <!-- Save Button -->
                                        <div>
                                            <button type="submit" class="btn btn-primary w-full">Save</button>
                                        </div>
                                    </form>
                                </div>
                                

                                <div>
                                    <hr>
                                </div>

                                <div class="col-md-12" id="attendance-table">
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
    </main>
        
