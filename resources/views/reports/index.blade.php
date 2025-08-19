@extends('layouts.app')

@section('content')

    <div class="pagetitle">

        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('cycle.index') }}" class="no-underline">Implementations</a></li>
                <li class="breadcrumb-item uppercase">Implementation Reports</li>
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
                            @if (!auth()->user()->hasRole('encoder'))
                                <div id="funded-content">
                                    <div class="row">
                                        <div class="col-md-6 mt-3 text-sm">
                                            <form action="{{ route('reports.show') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="cycle_id" value="{{ $cycle->id }}">

                                                <label for="center_name">Select Center:</label>
                                                <select class="form-control uppercase" name="center_name" id="center_name"
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
                                            <nav class="header-nav ml-auto flex gap-3 mt-2 items-center">
                                                @if (!auth()->user()->hasRole('encoder'))
                                                    <ul class="flex list-none">
                                                        <li class="nav-item relative" x-data="{ open: false }">
                                                            <button @click="open = !open" @click.away="open = false"
                                                                class="d-flex items-center focus:outline-none bg-blue-600 text-white rounded px-3 min-h-9">
                                                                <b>Worker Reports</b>
                                                                <span>
                                                                    <svg class="ml-2 w-4 h-4" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M19 9l-7 7-7-7" />
                                                                    </svg>
                                                                </span>
                                                            </button>

                                                            <ul id="dropdown-worker" x-show="open" x-transition
                                                                class="absolute left-0 mt-2 px-2 w-max bg-white rounded-md shadow-lg z-50 profile"
                                                                @click.away="open = false">
                                                                <form id="printCDWForm" action="" method="POST"
                                                                    target="_blank">
                                                                    @csrf
                                                                    <input type="hidden" name="cycle_id" id="cycle_id"
                                                                        value="{{ $cycle->id }}">
                                                                    <input type="hidden" name="center_name" id="center_id"
                                                                        value="">
                                                                    <li>
                                                                        <button
                                                                            class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                            onclick="workerReport('masterlist'); return false;">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor"
                                                                                class="size-5 mr-2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                            </svg>
                                                                            <span>Masterlist</span>
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="border-t my-1">
                                                                    </li>

                                                                    <li class="nav-heading">
                                                                        Age Bracket
                                                                    </li>
                                                                    <li>
                                                                        <button
                                                                            class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                            onclick="workerReport('age-bracket-upon-entry'); return false;">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor"
                                                                                class="size-5 mr-2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                            </svg>
                                                                            <span>Upon Entry</span>
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button
                                                                            class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                            onclick="workerReport('age-bracket-after-120'); return false;">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor"
                                                                                class="size-5 mr-2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                            </svg>
                                                                            <span>After 120 Feedings</span>
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="border-t my-1">
                                                                    </li>
                                                                    <li>
                                                                        <button
                                                                            class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                            onclick="workerReport('monitoring'); return false;">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor"
                                                                                class="size-5 mr-2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                            </svg>
                                                                            <span>Monitoring</span>
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="border-t my-1">
                                                                    </li>
                                                                    <li>
                                                                        <button
                                                                            class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mb-1 items-center"
                                                                            onclick="workerReport('unfunded'); return false;">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor"
                                                                                class="size-5 mr-2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                            </svg>
                                                                            <span>Unfunded</span>
                                                                        </button>
                                                                    </li>
                                                                </form>
                                                            </ul>
                                                        </li>

                                                    </ul>
                                                @endif
                                                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal') || auth()->user()->hasRole('sfp coordinator'))
                                                    <ul class="flex list-none">
                                                        <li class="nav-item relative" x-data="{ open: false }">
                                                            <button @click="open = !open" @click.away="open = false"
                                                                class="d-flex items-center focus:outline-none bg-blue-600 text-white rounded px-3 min-h-9">
                                                                <b>Focal Reports</b>
                                                                <span>
                                                                    <svg class="ml-2 w-4 h-4" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                                    </svg>
                                                                </span>
                                                            </button>
                                                            <ul id="dropdown-focal" x-show="open" x-transition
                                                                class="absolute left-0 mt-2 px-2 w-60 bg-white rounded-md shadow-lg z-50 profile"
                                                                @click.away="open = false">
                                                                <form id="printFocalForm" action="" method="POST"
                                                                    target="_blank">
                                                                    @csrf
                                                                    <input type="hidden" name="cycle_id2" id="cycle_id2"
                                                                        value="{{ $cycle->id }}">
                                                                    <input type="hidden" name="center_name2"
                                                                        id="center_id2" value="">
                                                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                                                        <li>
                                                                            <button
                                                                                class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                                onclick="focalReport('malnourished'); return false;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke-width="2" stroke="currentColor"
                                                                                    class="size-5 mr-2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                </svg>
                                                                                <span>Malnourished Children</span>
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <hr class="border-t my-1">
                                                                        </li>
                                                                        <li>
                                                                            <button
                                                                                class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                                onclick="focalReport('disabilities'); return false;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke-width="2" stroke="currentColor"
                                                                                    class="size-5 mr-2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                </svg>
                                                                                <span>Persons with
                                                                                    Disability</span>
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <hr class="border-t my-1">
                                                                        </li>
                                                                        <li class="nav-heading d-flex align-items-center">
                                                                            Undernourish
                                                                        </li>
                                                                        <li>
                                                                            <button
                                                                                class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                                onclick="focalReport('undernourished-upon-entry'); return false;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke-width="2" stroke="currentColor"
                                                                                    class="size-5 mr-2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                </svg>
                                                                                <span>Upon Entry</span>
                                                                            </button>
                                                                        </li>
                                                                        <li class="hidden">
                                                                            <button
                                                                                class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                                onclick="focalReport('undernourished-after-120'); return false;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke-width="2" stroke="currentColor"
                                                                                    class="size-5 mr-2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                </svg>
                                                                                <span>After 120 Feedings</span>
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <hr class="border-t my-1">
                                                                        </li>
                                                                        <li class="nav-heading d-flex align-items-center">
                                                                            Nutritional Status Upon Entry
                                                                        <li>
                                                                            <button
                                                                                class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                                onclick="focalReport('weight-for-age-upon-entry'); return false;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke-width="2" stroke="currentColor"
                                                                                    class="size-5 mr-2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                </svg>
                                                                                <span>Weight for Age</span>
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <button
                                                                                class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                                onclick="focalReport('height-for-age-upon-entry'); return false;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke-width="2" stroke="currentColor"
                                                                                    class="size-5 mr-2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                </svg>
                                                                                <span>Height for Age</span>
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <button
                                                                                class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                                onclick="focalReport('weight-for-height-upon-entry'); return false;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke-width="2" stroke="currentColor"
                                                                                    class="size-5 mr-2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                </svg>
                                                                                <span>Weight for Height</span>
                                                                            </button>
                                                                        </li>
                                                                        <li class="hidden">
                                                                            <hr class="border-t my-1">
                                                                        </li>
                                                                        <li class="hidden">
                                                                            NS After 120 Feedings
                                                                        </li>
                                                                        <li class="hidden">
                                                                            <button
                                                                                class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                                onclick="focalReport('weight-for-age-after-120'); return false;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke-width="2" stroke="currentColor"
                                                                                    class="size-5 mr-2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                </svg>
                                                                                <span>Weight for Age</span>
                                                                            </button>
                                                                        </li>
                                                                        <li class="hidden">
                                                                            <button
                                                                                class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                                onclick="focalReport('height-for-age-after-120'); return false;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke-width="2" stroke="currentColor"
                                                                                    class="size-5 mr-2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                </svg>
                                                                                <span>Height for Age</span>
                                                                            </button>
                                                                        </li>
                                                                        <li class="hidden">
                                                                            <button
                                                                                class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center"
                                                                                onclick="focalReport('weight-for-height-after-120'); return false;">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke-width="2" stroke="currentColor"
                                                                                    class="size-5 mr-2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                                </svg>
                                                                                <span>Weight for Height</span>
                                                                            </button>
                                                                        </li>
                                                                    @endif
                                                            </ul>
                                                        </li>
                                                    </ul>

                                                    </form>
                                                @endif
                                                <form action="{{ route('export-report') }}" method="POST"
                                                    target="_blank">
                                                    @csrf

                                                    <input type="hidden" name="cycle_id" id="cycle_id" value="">

                                                    <button type="submit"
                                                        class="bg-blue-600 text-white flex align-items-center rounded px-3 min-h-9">
                                                        <b><span>Export Report</span></b>
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="2" stroke="white"
                                                            class="size-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                                        </svg>


                                                    </button>
                                                </form>
                                            </nav>
                                        </div>

                                    </div>


                                    <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                                        @include('reports.partials.funded-table', [
                                            'isFunded' => $isFunded,
                                        ])
                                    </div>

                                    <script>
                                        function workerReport(reportType) {
                                            let printCDWForm = document.getElementById('printCDWForm');
                                            let centerInput = document.getElementById('center_name');
                                            let centerHiddenInput = document.getElementById('center_id');
                                            let cycle_id = document.getElementById('cycle_id');

                                            let center_name = centerInput.value;

                                            centerHiddenInput.value = center_name;

                                            printCDWForm.action = `{{ url('') }}/reports/show-${reportType}`;
                                            printCDWForm.target = "_blank";
                                            printCDWForm.submit();
                                        }

                                        function focalReport(reportType) {
                                            let printFocalForm = document.getElementById('printFocalForm');
                                            let centerInput = document.getElementById('center_name');
                                            let centerHiddenInput = document.getElementById('center_id');
                                            let cycle_id = document.getElementById('cycle_id2');

                                            let center_name = centerInput.value;

                                            printFocalForm.action = `{{ url('') }}/reports/show-${reportType}`;
                                            printFocalForm.target = "_blank";
                                            printFocalForm.submit();
                                        }

                                        function nsReport(reportType, nsType) {
                                            let printNSForm = document.getElementById('printNSForm');
                                            let centerInput = document.getElementById('center_name');
                                            let centerHiddenInput = document.getElementById('ns_center_id');
                                            let cycle_id = document.getElementById('ns_cycle_id');
                                            let ns_type = document.getElementById('ns_type');
                                            let report_type = document.getElementById('report_type');

                                            let center_name = centerInput.value;

                                            ns_type.value = nsType;
                                            report_type.value = reportType;

                                            printNSForm.action = `{{ url('') }}/reports/show-${reportType}/${nsType}`;
                                            printNSForm.method = "POST";
                                            printNSForm.target = "_blank";
                                            printNSForm.submit();
                                        }
                                    </script>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <script>
        window.addEventListener('load', function () {
            $(document).ready(function() {
                $("#funded-table").DataTable({
                    paging: true, // Enable paging
                    pageLength: 15, // Show 10 entries per page
                    lengthChange: false, // Hide the dropdown to change entry count
                    searching: true,
                    order: [
                        [0, 'asc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: 'no-sort'
                    }],
                    info: false,
                    rowCallback: function(row, data, index) {
                        var table = $('#funded-table').DataTable();
                        if (data && Object.keys(data).length !== 0) {
                            $('td:eq(0)', row).html(table.page.info().start + index + 1);
                        } else {
                            $('td:eq(0)', row).html('');
                        }
                    }
                });
            });
        });
    </script>


@endsection
