@extends('layouts.app')

@section('content')

    <div class="pagetitle">

        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('cycle.index') }}">Implementations</a></li>
                <li class="breadcrumb-item">Implementation Reports</li>
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

    <section class="section">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cycle: <b>{{ $cycle->name }}</b><br>Total No. of Children:
                            <b>{{ $childCount }}</b></b>
                        </h5>
                        <div id="report-content">
                            @if (auth()->user()->hasRole('admin') ||
                                    auth()->user()->hasRole('lgu focal') ||
                                    auth()->user()->hasRole('child development worker'))
                                <div id="funded-content">
                                    <div class="row">
                                        {{-- <div class="col-md-3 mt-3 text-sm">
                                            <form action="{{ route('reports.index') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="cycle_id" value="{{ $cycle->id }}">

                                                <label for="province">Select Province:</label>
                                                <select class="form-control" name="province" id="province"
                                                    onchange="this.form.submit()">
                                                    <option value="all_province">
                                                        All Province
                                                    </option>
                                                    @foreach ($centerNames as $center)
                                                            <option value="{{ $center->id }}"
                                                                {{ old('center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                                                                {{ $center->center_name }}
                                                            </option>
                                                        @endforeach
                                                </select>
                                            </form>
                                        </div>
                                        <div class="col-md-3 mt-3 text-sm">
                                            <form action="{{ route('reports.index') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="cycle_id" value="{{ $cycle->id }}">

                                                <label for="city">Select City/Municipality:</label>
                                                <select class="form-control" name="city" id="city"
                                                    onchange="this.form.submit()">
                                                    <option value="all_province">
                                                        All City/Municipality
                                                    </option>
                                                    @foreach ($centerNames as $center)
                                                            <option value="{{ $center->id }}"
                                                                {{ old('center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                                                                {{ $center->center_name }}
                                                            </option>
                                                        @endforeach
                                                </select>
                                            </form>
                                        </div>
                                        <div class="col-md-6 row">
                                        </div> --}}
                                        <div class="col-md-6 mt-3 text-sm">
                                            <form action="{{ route('reports.index') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="cycle_id" value="{{ $cycle->id }}">

                                                <label for="center_name">Select Center:</label>
                                                <select class="form-control" name="center_name" id="center_name"
                                                    onchange="this.form.submit()">
                                                    <option value="all_center"
                                                        {{ old('center_name', $cdcId) == 'all_center' ? 'selected' : '' }}>
                                                        All Child Development Center
                                                    </option>
                                                    @foreach ($centerNames as $center)
                                                        <option value="{{ $center->id }}"
                                                            {{ old('center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                                                            {{ $center->center_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </div>
                                        <div class="col-md-6 row">
                                        </div>
                                        <div class="col-md-6 text-base inline">
                                            <nav class="d-flex header-nav mt-4 mb-5">
                                                @if (auth()->user()->hasRole('admin') ||
                                                        auth()->user()->hasRole('lgu focal') ||
                                                        auth()->user()->hasRole('child development worker'))
                                                    <ul class="d-flex list-unstyled">
                                                        <li class="nav-item dropdown pe-3" x-data="{ open: false }">
                                                            <a class="nav-link nav-profile align-items-center pe-0"
                                                                @click.prevent="open = !open; $event.stopPropagation()"
                                                                data-bs-toggle="dropdown">
                                                                <button type="button"
                                                                    class="bg-blue-600 text-white rounded px-3 min-h-9">
                                                                    <span
                                                                        class="d-none d-md-block dropdown-toggle text-white">Worker
                                                                        Reports</span>
                                                                </button>
                                                            </a>
                                                            <ul id="dropdown-worker"
                                                                class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                                                                <form id="printCDWForm" action="" method="POST"
                                                                    target="_blank">
                                                                    @csrf
                                                                    <input type="hidden" name="cycle_id" id="cycle_id"
                                                                        value="{{ $cycle->id }}">
                                                                    <input type="hidden" name="center_name" id="center_id"
                                                                        value="">
                                                                    <li>
                                                                        <button
                                                                            class="dropdown-item d-flex align-items-center"
                                                                            onclick="workerReport('masterlist'); return false;">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor"
                                                                                class="size-5 mr-2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                            </svg>
                                                                            <span>Print Masterlist</span>
                                                                        </button>
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>

                                                                    <li class="nav-item">
                                                                        <label
                                                                            class="dropdown-item d-flex align-items-center">
                                                                            <svg class="h-2 w-2 mr-2" width="24"
                                                                                height="24" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor"
                                                                                fill="none" stroke-linecap="round"
                                                                                stroke-linejoin="round">
                                                                                <path stroke="none" d="M0 0h24v24H0z" />
                                                                                <circle cx="12" cy="12"
                                                                                    r="6" />
                                                                            </svg>
                                                                            Age Bracket
                                                                        </label>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <li>
                                                                        <button
                                                                            class="dropdown-item d-flex align-items-center ml-3"
                                                                            onclick="workerReport('age-bracket-upon-entry'); return false;">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor"
                                                                                class="size-5 mr-2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                            </svg>
                                                                            <span>Print Upon Entry</span>
                                                                        </button>
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <li>
                                                                        <button
                                                                            class="dropdown-item d-flex align-items-center ml-3"
                                                                            onclick="workerReport('age-bracket-after-120'); return false;">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor"
                                                                                class="size-5 mr-2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                            </svg>
                                                                            <span>Print After 120 Feedings</span>
                                                                        </button>
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <li>
                                                                        <button
                                                                            class="dropdown-item d-flex align-items-center"
                                                                            onclick="workerReport('monitoring'); return false;">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor"
                                                                                class="size-5 mr-2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                            </svg>
                                                                            <span>Print Monitoring</span>
                                                                        </button>
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <li>
                                                                        <button
                                                                            class="dropdown-item d-flex align-items-center"
                                                                            onclick="workerReport('unfunded'); return false;">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor"
                                                                                class="size-5 mr-2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                            </svg>
                                                                            <span>Print Unfunded</span>
                                                                        </button>
                                                                        </button>
                                                                    </li>
                                                                </form>
                                                            </ul>
                                                        </li>
                                                        
                                                    </ul>
                                                @endif
                                                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                                    <div id="focal-dropdown">
                                                        <ul class="d-flex list-unstyled">
                                                            <li class="nav-item dropdown pe-3" x-data="{ open: false }">
                                                                <a class="nav-link nav-profile align-items-center pe-0"
                                                                    @click.prevent="open = !open; $event.stopPropagation()"
                                                                    data-bs-toggle="dropdown">
                                                                    <button type="button"
                                                                        class="bg-blue-600 text-white rounded px-3 min-h-9">
                                                                        <span
                                                                            class="d-none d-md-block dropdown-toggle text-white">Focal
                                                                            Reports</span>
                                                                    </button>
                                                                </a>
                                                                <ul id="dropdown-focal"
                                                                    class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                                                                    <form id="printFocalForm" action=""
                                                                        method="POST" target="_blank">
                                                                        @csrf
                                                                        <input type="hidden" name="cycle_id2"
                                                                            id="cycle_id2" value="{{ $cycle->id }}">
                                                                        <input type="hidden" name="center_name2"
                                                                            id="center_id2" value="">
                                                                        @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                                                            <li>
                                                                                <button
                                                                                    class="dropdown-item d-flex align-items-center"
                                                                                    onclick="focalReport('malnourished'); return false;">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        fill="none" viewBox="0 0 24 24"
                                                                                        stroke-width="2"
                                                                                        stroke="currentColor"
                                                                                        class="size-5 mr-2">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                    </svg>
                                                                                    <span>Print Malnourished Children</span>
                                                                                </button>
                                                                                </button>
                                                                            </li>
                                                                            <li>
                                                                                <hr class="dropdown-divider">
                                                                            </li>
                                                                            <li>
                                                                                <button
                                                                                    class="dropdown-item d-flex align-items-center"
                                                                                    onclick="focalReport('disabilities'); return false;">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        fill="none" viewBox="0 0 24 24"
                                                                                        stroke-width="2"
                                                                                        stroke="currentColor"
                                                                                        class="size-5 mr-2">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                    </svg>
                                                                                    <span>Print Persons with
                                                                                        Disability</span>
                                                                                </button>
                                                                                </button>
                                                                            </li>
                                                                            <li>
                                                                                <hr class="dropdown-divider">
                                                                            </li>
                                                                            <li>
                                                                                <label
                                                                                    class="dropdown-item d-flex align-items-center">
                                                                                    <svg class="h-2 w-2 mr-2"
                                                                                        width="24" height="24"
                                                                                        viewBox="0 0 24 24"
                                                                                        stroke-width="2"
                                                                                        stroke="currentColor"
                                                                                        fill="none"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round">
                                                                                        <path stroke="none"
                                                                                            d="M0 0h24v24H0z" />
                                                                                        <circle cx="12"
                                                                                            cy="12" r="6" />
                                                                                    </svg>
                                                                                    Undernourish
                                                                                </label>
                                                                            </li>
                                                                            <li>
                                                                                <hr class="dropdown-divider">
                                                                            </li>
                                                                            <li>
                                                                                <button
                                                                                    class="dropdown-item d-flex align-items-center ml-3"
                                                                                    onclick="focalReport('undernourished-upon-entry'); return false;">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        fill="none" viewBox="0 0 24 24"
                                                                                        stroke-width="2"
                                                                                        stroke="currentColor"
                                                                                        class="size-5 mr-2">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                    </svg>
                                                                                    <span>Print Upon Entry</span>
                                                                                </button>
                                                                                </button>
                                                                            </li>
                                                                            <li>
                                                                                <hr class="dropdown-divider">
                                                                            </li>
                                                                            <li>
                                                                                <button
                                                                                    class="dropdown-item d-flex align-items-center ml-3"
                                                                                    onclick="focalReport('undernourished-after-120'); return false;">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        fill="none" viewBox="0 0 24 24"
                                                                                        stroke-width="2"
                                                                                        stroke="currentColor"
                                                                                        class="size-5 mr-2">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                    </svg>
                                                                                    <span>Print After 120 Feedings</span>
                                                                                </button>
                                                                                </button>
                                                                            </li>
                                                                            <li>
                                                                                <hr class="dropdown-divider">
                                                                            </li>
                                                                            <li>
                                                                                <label
                                                                                    class="dropdown-item d-flex align-items-center">
                                                                                    <svg class="h-2 w-2 mr-2"
                                                                                        width="24" height="24"
                                                                                        viewBox="0 0 24 24"
                                                                                        stroke-width="2"
                                                                                        stroke="currentColor"
                                                                                        fill="none"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round">
                                                                                        <path stroke="none"
                                                                                            d="M0 0h24v24H0z" />
                                                                                        <circle cx="12"
                                                                                            cy="12" r="6" />
                                                                                    </svg>
                                                                                    Nutritional Status
                                                                                </label>
                                                                            </li>
                                                                            <li>
                                                                                <hr class="dropdown-divider">
                                                                            </li>
                                                                            <li>
                                                                                <button
                                                                                    class="dropdown-item d-flex align-items-center ml-3"
                                                                                    onclick="focalReport('nutritional-status-upon-entry'); return false;">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        fill="none" viewBox="0 0 24 24"
                                                                                        stroke-width="2"
                                                                                        stroke="currentColor"
                                                                                        class="size-5 mr-2">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                    </svg>
                                                                                    <span>Print Upon Entry</span>
                                                                                </button>
                                                                                </button>
                                                                            </li>
                                                                            <li>
                                                                                <hr class="dropdown-divider">
                                                                            </li>
                                                                            <li>
                                                                                <button
                                                                                    class="dropdown-item d-flex align-items-center ml-3"
                                                                                    onclick="focalReport('nutritional-status-after-120'); return false;">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        fill="none" viewBox="0 0 24 24"
                                                                                        stroke-width="2"
                                                                                        stroke="currentColor"
                                                                                        class="size-5 mr-2">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                    </svg>
                                                                                    <span>Print After 120 Feedings</span>
                                                                                </button>
                                                                            </li>
                                                                            <li>
                                                                                <hr class="dropdown-divider">
                                                                            </li>
                                                                            <li>
                                                                                <button
                                                                                    class="dropdown-item d-flex align-items-center ml-3"
                                                                                    onclick="focalReport('height-for-age-upon-entry'); return false;">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        fill="none" viewBox="0 0 24 24"
                                                                                        stroke-width="2"
                                                                                        stroke="currentColor"
                                                                                        class="size-5 mr-2">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                    </svg>
                                                                                    <span>Print</span>
                                                                                </button>
                                                                            </li>
                                                                        @endif
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    </form>
                                                @endif
                                                {{-- @if (auth()->user()->hasRole('admin'))
                                                        <form id="printAdminForm" action="" method="POST"
                                                            target="_blank">
                                                            @csrf
                                                            <input type="hidden" name="cycle_id" id="cycle_id"
                                                                value="">
                                                            <input type="hidden" name="center_name" id="center_id"
                                                                value="">

                                                            <ul class="d-flex list-unstyled">
                                                                <li class="nav-item dropdown pe-3">
                                                                    <a class="nav-link nav-profile align-items-center pe-0"
                                                                        href="#" data-bs-toggle="dropdown">
                                                                        <button type="button"
                                                                            class="bg-blue-600 text-white rounded px-3 min-h-9">
                                                                            <span
                                                                                class="d-none d-md-block dropdown-toggle text-white">Admin
                                                                                Report</span>
                                                                        </button>
                                                                    </a>
                                                                    <ul
                                                                        class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                                                                        @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal') || auth()->user()->hasRole('child development worker'))
                                                                            <li>
                                                                                <a class="dropdown-item d-flex align-items-center"
                                                                                    id="printButtonAdminReport">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        fill="none" viewBox="0 0 24 24"
                                                                                        stroke-width="2"
                                                                                        stroke="currentColor"
                                                                                        class="size-5 mr-2">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                    </svg>
                                                                                    <span>Admin Report</span>
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <hr class="dropdown-divider">
                                                                            </li>
                                                                        @endif
                                                                    </ul>
                                                                </li>
                                                            </ul>
                                                        </form>
                                                    @endif --}}
                                            </nav>
                                        </div>

                                    </div>


                                    <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                                        @include('reports.partials.funded-table', [
                                            'isFunded' => $isFunded,
                                        ])
                                    </div>
                                    <div class="mt-3">
                                        {{ $isFunded->links() }}
                                    </div>

                                    <script>
                                        function workerReport(reportType) {
                                            let printCDWForm = document.getElementById('printCDWForm');
                                            let centerInput = document.getElementById('center_name');
                                            let centerHiddenInput = document.getElementById('center_id');
                                            let cycle_id = document.getElementById('cycle_id');

                                            let center_name = centerInput.value;

                                            centerHiddenInput.value = center_name;

                                            printCDWForm.action = `{{ url('') }}/reports/print/${reportType}`;
                                            printCDWForm.target = "_blank";
                                            printCDWForm.submit();
                                        }

                                        function focalReport(reportType, cycleId) {
                                            let printFocalForm = document.getElementById('printFocalForm');
                                            let centerInput = document.getElementById('center_name');
                                            let centerHiddenInput = document.getElementById('center_id');
                                            let cycle_id = document.getElementById('cycle_id2');

                                            let center_name = centerInput.value;

                                            printFocalForm.action = `{{ url('') }}/reports/print/${reportType}`;
                                            printFocalForm.target = "_blank";
                                            printFocalForm.submit();
                                        }

                                        // function adminReport(reportType, cycleId) {
                                        //     let printAdminForm = document.getElementById('printAdminForm');
                                        //     let centerInput = document.getElementById('center_name');
                                        //     let centerHiddenInput = document.getElementById('center_id');
                                        //     let cycleHiddenInput = document.getElementById('cycle_id');
                                        // function adminReport(reportType, cycleId) {
                                        //     let printAdminForm = document.getElementById('printAdminForm');
                                        //     let centerInput = document.getElementById('center_name');
                                        //     let centerHiddenInput = document.getElementById('center_id');
                                        //     let cycleHiddenInput = document.getElementById('cycle_id');

                                        //     centerHiddenInput.value = centerInput.value;
                                        //     centerHiddenInput.value = centerInput.value;

                                        //     printAdminForm.action = `/reports/${cycleId}/print/${reportType}`;
                                        //     printAdminForm.action = `/reports/${cycleId}/print/${reportType}`;

                                        //     // printAdminForm.target = "_blank";
                                        //     printAdminForm.submit();
                                        // }
                                        //     // printAdminForm.target = "_blank";
                                        //     printAdminForm.submit();
                                        // }
                                    </script>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>


    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite(['resources/js/app.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownToggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function () {
                    // Close all open dropdowns first
                    document.querySelectorAll('.dropdown-menu.show').forEach(openMenu => {
                        if (!toggle.nextElementSibling.contains(openMenu)) {
                            const dropdown = bootstrap.Dropdown.getInstance(openMenu.previousElementSibling);
                            if (dropdown) dropdown.hide();
                        }
                    });
                });
            });
        });
    </script>
    

@endsection
