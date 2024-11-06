@extends('layouts.app')
@section('content')

<main id="main" class="main">

    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>'; padding: 0;">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('child.index') }}">Children</a></li>
                <li class="breadcrumb-item active">{{ $child->full_name }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    {{-- Alert Messages --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Auto-close Alerts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const successAlert = document.getElementById('success-alert');
            const errorAlert = document.getElementById('danger-alert');
            
            if (successAlert) {
                setTimeout(() => {
                    bootstrap.Alert.getInstance(successAlert).close();
                }, 2000);
            }
            
            if (errorAlert) {
                setTimeout(() => {
                    bootstrap.Alert.getInstance(errorAlert).close();
                }, 2000);
            }
        });
    </script>

    <div class="wrapper">
        <section class="section">
            <div class="row">
                <div class="col-lg-3">
                    {{-- Form for Upon Entry Details --}}
                    @if (!$hasUponEntryData)
                        @include('partials.nutritional-status-entry', [
                            'action' => route('nutritionalstatus.storeUponEntryDetails'),
                            'buttonText' => 'Upon entry details',
                        ])
                    @endif

                    {{-- Form for After 120 Feedings Details --}}
                    @if ($hasUponEntryData && !$hasUponExitData)
                        @include('partials.nutritional-status-exit', [
                            'action' => route('nutritionalstatus.storeExitDetails'),
                            'buttonText' => 'After 120 Feedings',
                        ])
                    @endif
                </div>

                {{-- Nutritional Status Table --}}
                <div class="{{ $hasUponExitData ? 'col-lg-12' : 'col-lg-9' }}">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Nutritional Status</h5>
                            @include('nutritionalstatus.partials.ns-table')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    @vite(['resources/js/app.js'])
</main><!-- End #main -->

@endsection
