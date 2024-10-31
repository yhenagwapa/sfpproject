@extends('layouts.app')

@section('content')

<main id="main" class="main">

    <div class="pagetitle">

        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('centers.index') }}">Child Development Centers</a></li>
                <li class="breadcrumb-item active">{{ $center->center_name }}</li>
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
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Child Development Center Details</h5>
                            <form class="row" method="post" action="{{ route('centers.update', $center->id) }}">
                                @csrf
                                @method('put')

                                <div class='col-md-3 mt-2 text-gray-400 text-xs'>Child Development Center Information
                                </div>
                                <div class='col-md-9 mt-3 text-gray-400 text-xs'>
                                    <hr>
                                </div>

                                <div class="col-md-12 mt-3 text-sm">
                                    <label for="center_name">Center Name<b class="text-red-600">*</b></label>
                                    <input type="text" class="form-control rounded border-gray-300" id="center_name"
                                        name="center_name" value="{{ old('center_name', $center->center_name) }}"
                                        autofocus>
                                    @error('center_name')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="assigned_user_id">Assigned LGU Focal<b
                                            class='text-red-600'>*</b></label>
                                    <select class="form-control rounded border-gray-300" id="assigned_focal_user_id"
                                        name="assigned_focal_user_id">
                                        <option value="" selected>Select LGU Focal</option>
                                        @foreach ($focals as $focal)
                                            <option value="{{ $focal->id }}" {{ old('assigned_focal_user_id', $center->assigned_focal_user_id) == $focal->id ? 'selected' : '' }}>
                                                {{ $focal->full_name }} 
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_focal_user_id')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="assigned_user_id">Assigned Child Development Worker<b
                                            class='text-red-600'>*</b></label>
                                    <select class="form-control rounded border-gray-300" id="assigned_worker_user_id"
                                        name="assigned_worker_user_id">
                                        <option value="" selected>Select Worker</option>
                                        @foreach ($workers as $worker)
                                            <option value="{{ $worker->id }}" {{ old('assigned_worker_worker_id', $center->assigned_worker_user_id) == $worker->id ? 'selected' : '' }}>
                                                {{ $worker->full_name }} 
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_worker_user_id')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class='col-md-1 mt-4 text-gray-400 text-xs'>Address</div>
                                <div class='col-md-11 mt-8 text-gray-400 text-xs'>
                                    <hr>
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="region">Region<b class="text-red-600">*</b></label>
                                    <select class="form-control rounded border-gray-300" id="region" name="region_psgc">
                                        <option value="110000000" selected>Region XI</option>
                                    </select>
                                </div>

                                <!-- Hidden Region (pre-selected) -->
                                <input type="hidden" name="region_psgc" value="110000000">

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="province">Province<b class="text-red-600">*</b></label>
                                    <select class="form-control rounded border-gray-300" id="province"
                                        name="province_psgc">
                                        <option value="" selected>Select Province</option>
                                        @foreach ($provinces as $psgc => $name)
                                            <option value="{{ $psgc }}" {{ old('province_psgc', $psgcRecord->province_psgc) == $psgc ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('province_psgc')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="city">City/Municipality<b class="text-red-600">*</b></label>
                                    <select class="form-control rounded border-gray-300" id="city"
                                        name="city_name_psgc">
                                        <option value="">Select City/Municipality</option>
                                        @foreach ($cities as $psgc => $name)
                                            <option value="{{ $psgc }}" {{ old('city_name_psgc', $psgcRecord->city_name_psgc) == $psgc ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('city_name_psgc')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="barangay">Barangay<b class="text-red-600">*</b></label>
                                    <select class="form-control rounded border-gray-300" id="barangay" name="brgy_psgc">
                                        <option value="">Select Barangay</option>
                                        @foreach ($barangays as $psgc => $name)
                                            <option value="{{ $psgc }}" {{ old('brgy_psgc', $psgcRecord->brgy_psgc) == $psgc ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brgy_psgc')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>



                                <div class="col-6 mt-2 text-sm">
                                    <label for="address">Purok<b class="text-red-600">*</b></label>
                                    <input type="text" class="form-control rounded border-gray-300" id="address"
                                        name='address' value="{{ old('address', $center->address) }}">
                                    @error('address')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="zip_code">Zip Code<b class="text-red-600">*</b></label>
                                    <input type="text" class="form-control rounded border-gray-300" id="zip_code"
                                        name='zip_code' value="{{ old('zip_code', $center->zip_code) }}" maxlength="4">
                                    @error('zip_code')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>



                                {{-- <div class='col-md-1 mt-4 text-gray-400 text-xs'>Areas</div>
                                <div class='col-md-11 mt-8 text-gray-400 text-xs'>
                                    <hr>
                                </div> --}}

                                {{-- <div class="col-md-12 mt-3 mb-0 text-sm">
                                    <label for="cdcname" class="mr-2">Area/s</label>
                                </div>
                                <div class="col-md-12" id="input-container">
                                    <div class="input-group mb-2">
                                        <input type="text"
                                            class="form-control rounded border-gray-300 rounded border-gray-300 mr-3"
                                            name="cdcname[]" value="{{ old('cdcname') }}" autofocus>
                                        <button type="button"
                                            class="text-white bg-blue-600 rounded text-sm text-nowrap px-4 min-h-9 add-more">
                                            Add More
                                        </button>
                                    </div>
                                </div> --}}

                                <div class="col-md-12 mt-4 text-right">
                                    <button type="button" class="text-white bg-blue-600 rounded px-3 min-h-9"
                                        data-bs-toggle="modal" data-bs-target="#verticalycentered">Submit</button>
                                    <a href="{{ route('centers.index') }}"></a><button type="reset"
                                        class="text-white bg-gray-600 rounded px-3 min-h-9">Cancel</button></a>
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
                                                Are you sure you want to save these update?
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
                        </div>
                        </form><!-- End floating Labels Form -->
                    </div>
                </div>
            </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const locations = {
                provinces: @json($provinces),
                cities: @json($cities),
                barangays: @json($barangays),
                changedCities: @json($changedCities),
                changedBrgys: @json($changedBrgys)
            };

            console.log(locations);

            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');
            const barangaySelect = document.getElementById('barangay');

            console.log('province psgc preselected', provinceSelect.value);
            console.log('city psgc preselected', citySelect.value);
            console.log('brgy psgc preselected', barangaySelect.value);

            function filterCities() {
                let provincePsgc = provinceSelect.value;

                if (provincePsgc && locations.cities[provincePsgc]) {
                    
                    Object.entries(locations.cities[provincePsgc]).forEach(([psgc, name]) => {
                        const option = document.createElement('option');
                        option.value = psgc;
                        option.text = name;
                        citySelect.appendChild(option);
                    });

                    citySelect.value = '{{ old('city_name_psgc', $psgcRecord->city_name_psgc) }}';
                    filterBarangays();
                }
            }

            function filterBarangays() {
                let cityPsgc = citySelect.value;

                if (cityPsgc && locations.barangays[cityPsgc]) {
                    
                    Object.entries(locations.barangays[cityPsgc]).forEach(([psgc, name]) => {
                        const option = document.createElement('option');
                        option.value = psgc;
                        option.text = name;
                        barangaySelect.appendChild(option);
                    });

                    
                    barangaySelect.value = '{{ old('brgy_psgc', $psgcRecord->brgy_psgc) }}';
                }
            }

            filterCities();
            filterBarangays();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const newlocations = {
                changedCities: @json($changedCities),
                changedBrgys: @json($changedBrgys)
            };

            const newProvinceSelect = document.getElementById('province');
            const newCitySelect = document.getElementById('city');
            const newBarangaySelect = document.getElementById('barangay');

            function filterCitiesWhenProvinceChanged() {
                const newProvincePsgc = newProvinceSelect.value;

                citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
                barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

                if (newProvincePsgc) {
                    newCitySelect.style.display = 'block';
                    if (locations.changedCities[newProvincePsgc]) {
                        locations.changedCities[newProvincePsgc].forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.psgc;
                            option.text = city.name;
                            newCitySelect.appendChild(option);
                        });
                    }
                }
                
                newCitySelect.value = '';
                newBarangaySelect.value = '';

            }

            function filterBarangaysWhenCitiesChanged() {
                const newCityPsgc = newCitySelect.value;

                newBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';

                if (newCityPsgc) {
                    newBarangaySelect.style.display = 'block'; // Show the barangay dropdown
                    if (locations.barangays[newCityPsgc]) {
                        locations.barangays[newCityPsgc].forEach(barangay => {
                            const option = document.createElement('option');
                            option.value = barangay.psgc;
                            option.text = barangay.name;
                            newBarangaySelect.appendChild(option);
                        });
                    }
                }
            }

            newProvinceSelect.addEventListener('change', filterCitiesWhenProvinceChanged);
            newCitySelect.addEventListener('change', filterBarangaysWhenCitiesChanged);

        });
    </script>








    </section>

    </div>
</main><!-- End #main -->