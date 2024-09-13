@extends('layouts.app')

@section('title', 'SFP Onse ')

@section('content')

    <main id="main" class="main">

        <div class="pagetitle">

            <nav style="--bs-breadcrumb-divider: '>';">
                <ol class="breadcrumb mb-3 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('centers.index') }}">Child Development Centers</a></li>
                    <li class="breadcrumb-item active">Child Development Centers Details</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        @if ($errors->any())
    <div class="alert alert-success">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="wrapper">
            <section class="section">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Child Development Center Details</h5>
                                <form class="row" method="post" action="{{ route('centers.store') }} ">
                                    @csrf

                                    <div class='col-md-3 mt-2 text-gray-400 text-xs'>Child Development Center Information</div>
                                    <div class='col-md-9 mt-3 text-gray-400 text-xs'>
                                        <hr>
                                    </div>

                                    <div class="col-md-12 mt-3 text-sm">
                                        <label for="center_name">Child Development Center Name<b class='text-red-600'>*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="center_name" name="center_name" value="{{ old('center_name') }}" autofocus>
                                        @error('center_name')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                
                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="assigned_user_id">Child Development Worker<b class='text-red-600'>*</b></label>
                                        <select class="form-control rounded border-gray-300" id="assigned_user_id" name="assigned_user_id">
                                            <option value="">Select a worker</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('assigned_user_id')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class='col-md-1 mt-4 text-gray-400 text-xs'>Address</div>
                                    <div class='col-md-11 mt-8 text-gray-400 text-xs'>
                                        <hr>
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="region">Region</label><label for="region"
                                            class="text-red-600">*</label>
                                        <select class="form-control rounded border-gray-300" id="region">
                                            <option value="110000000" selected>Region XI</option>
                                        </select>
                                    </div>

                                    <input type="hidden" name="region_psgc" value="110000000">

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="province">Province</label><label for="province"
                                            class="text-red-600">*</label>
                                        <select class="form-control rounded border-gray-300" id="province" name="province_psgc"
                                            onchange="filterCities()">
                                            <option value="" selected>Select Province</option>
                                            @foreach ($provinces as $psgc => $name)
                                                <option value="{{ $psgc }}"
                                                    {{ request('province_psgc') == $psgc ? 'selected' : '' }}>
                                                    {{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('province_psgc')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="city">City/Municipality</label><label for="city"
                                            class="text-red-600">*</label>
                                        <select class="form-control rounded border-gray-300" id="city" name="city_name_psgc"
                                            onchange="filterBarangays()">
                                            <option value="" selected>Select City/Municipality</option>
                                            @foreach ($cities as $psgc => $name)
                                                <option value="{{ $psgc }}"
                                                    {{ request('city_name_psgc') == $psgc ? 'selected' : '' }}>
                                                    {{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('city_name_psgc')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="barangay">Barangay</label><label for="barangay"
                                            class="text-red-600">*</label>
                                        <select class="form-control rounded border-gray-300" id="barangay" name="brgy_psgc">
                                            <option value="" selected>Select Barangay</option>
                                            @foreach ($barangays as $psgc => $name)
                                                <option value="{{ $psgc }}"
                                                    {{ request('brgy_psgc') == $psgc ? 'selected' : '' }}>
                                                    {{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('brgy_psgc')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-6 mt-2 text-sm">
                                        <label for="address">House No./ Street/ Purok</label><label for="address"
                                            class="text-red-600">*</label>
                                        <input type="text" class="form-control rounded border-gray-300" id="address" name='address'
                                            value="{{ old('address') }}">
                                        @error('address')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="zip_code">Zip Code</label><label for="zip_code"
                                            class="text-red-600">*</label>
                                        <input type="text" class="form-control rounded border-gray-300" id="zip_code" name='zip_code'
                                            value="{{ old('zip_code') }}" maxlength="4">
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
                                            <input type="text" class="form-control rounded border-gray-300 rounded border-gray-300 mr-3"
                                                name="cdcname[]" value="{{ old('cdcname') }}" autofocus>
                                            <button type="button"
                                                class="text-white bg-blue-600 rounded text-sm text-nowrap px-4 min-h-9 add-more">
                                                Add More
                                            </button>
                                        </div>
                                    </div> --}}

                                    <div class="col-md-12 mt-4 text-right">
                                        <button type="submit"
                                            class="text-white bg-blue-600 rounded px-3 min-h-9">Submit</button>
                                        <button type="reset"
                                            class="text-white bg-gray-600 rounded px-3 min-h-9">Cancel</button>
                                    </div>
                            </div>
                            </form><!-- End floating Labels Form -->
                        </div>
                    </div>
                </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        {{-- <script>
            document.addEventListener('DOMContentLoaded', function() {
                const inputContainer = document.getElementById('input-container');

                function addInput() {
                    // Hide the last "Add More" button
                    const lastInputGroup = inputContainer.querySelector('.input-group:last-child');
                    const lastAddMoreButton = lastInputGroup.querySelector('.add-more');
                    const lastRemoveButton = lastInputGroup.querySelector('.remove')
                    if (lastAddMoreButton) {
                        lastAddMoreButton.style.visibility = 'hidden'; // Hide the button
                    }
                    if (lastRemoveButton) {
                        lastRemoveButton.style.visibility = 'hidden';
                    }

                    // Create a new input group
                    const newInputGroup = document.createElement('div');
                    newInputGroup.className = 'input-group mb-2';

                    // Create a new input field
                    const newInput = document.createElement('input');
                    newInput.type = 'text';
                    newInput.className = 'form-control rounded border-gray-300 rounded border-gray-300 mr-2';
                    newInput.name = 'cdcname[]'; // Use array notation to handle multiple inputs

                    // Create a new "Add More" button
                    const newAddMoreButton = document.createElement('button');
                    newAddMoreButton.type = 'button';
                    newAddMoreButton.className =
                        'text-white bg-blue-600 rounded text-sm text-nowrap px-3 mr-1 min-h-9 add-more';
                    newAddMoreButton.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        `;
                    newAddMoreButton.addEventListener('click', function() {
                        addInput(); // Add another input field
                    });

                    // Create a new "Remove" button with SVG icon
                    const newRemoveButton = document.createElement('button');
                    newRemoveButton.type = 'button';
                    newRemoveButton.className = 'text-white bg-red-600 rounded text-sm text-nowrap px-3 min-h-9 remove';
                    newRemoveButton.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        `;
                    newRemoveButton.addEventListener('click', function() {
                        removeInput(newInputGroup); // Remove the current input field
                    });

                    // Append the new input field, buttons to the new input group
                    newInputGroup.appendChild(newInput);
                    newInputGroup.appendChild(newAddMoreButton);
                    newInputGroup.appendChild(newRemoveButton);

                    // Append the new input group to the container
                    inputContainer.appendChild(newInputGroup);
                }

                function removeInput(inputGroup) {
                    if (inputGroup) {
                        inputGroup.remove(); // Remove the specific input group

                        // Show the "Add More" button on the new last input group
                        const inputGroups = inputContainer.querySelectorAll('.input-group');
                        if (inputGroups.length > 0) {
                            const lastInputGroup = inputContainer.querySelector('.input-group:last-child');
                            const lastAddMoreButton = lastInputGroup.querySelector('.add-more');
                            const lastRemoveButton = lastInputGroup.querySelector('.remove');
                            if (lastAddMoreButton) {
                                lastAddMoreButton.style.visibility = 'visible'; // Show the button
                            }
                            if (lastRemoveButton && inputGroups.length === 1) {
                                lastRemoveButton.style.visibility = 'hidden';
                            } else if (lastRemoveButton) {
                                lastRemoveButton.style.visibility = 'visible';
                            }
                        } else {
                            // Show the initial "Add More" button if no input groups are left
                            const initialAddMoreButton = document.querySelector('.add-more');
                            if (initialAddMoreButton) {
                                initialAddMoreButton.style.visibility = 'visible'; // Show the initial button
                            }
                        }
                    }
                }

                // Attach the initial "Add More" button's event listener
                document.querySelector('.add-more').addEventListener('click', function() {
                    addInput(); // Add the first new input field
                });
            });
        </script> --}}


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const locations = {
                    provinces: @json($provinces),
                    cities: @json($cities),
                    barangays: @json($barangays)
                };

                const provinceSelect = document.getElementById('province');
                const citySelect = document.getElementById('city');
                const barangaySelect = document.getElementById('barangay');

                // Function to filter cities based on selected province
                function filterCities() {
                    const provincePsgc = provinceSelect.value;

                    // Clear existing options
                    citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
                    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

                    if (provincePsgc) {
                        citySelect.style.display = 'block'; // Show the city dropdown
                        if (locations.cities[provincePsgc]) {
                            locations.cities[provincePsgc].forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.psgc;
                                option.text = city.name;
                                citySelect.appendChild(option);
                            });
                        }
                    } else {
                        citySelect.style.display = 'disabled'; // Hide the city dropdown if no province is selected
                    }

                    // Reset city and barangay selects
                    citySelect.value = '';
                    barangaySelect.value = '';
                    barangaySelect.style.display = 'disabled'; // Hide barangay dropdown by default
                }

                // Function to filter barangays based on selected city
                function filterBarangays() {
                    const cityPsgc = citySelect.value;

                    // Clear existing options
                    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

                    if (cityPsgc) {
                        barangaySelect.style.display = 'block'; // Show the barangay dropdown
                        if (locations.barangays[cityPsgc]) {
                            locations.barangays[cityPsgc].forEach(barangay => {
                                const option = document.createElement('option');
                                option.value = barangay.psgc;
                                option.text = barangay.name;
                                barangaySelect.appendChild(option);
                            });
                        }
                    } else {
                        barangaySelect.style.display = 'disabled'; // Hide the barangay dropdown if no city is selected
                    }
                }

                // Attach event listeners
                provinceSelect.addEventListener('change', filterCities);
                citySelect.addEventListener('change', filterBarangays);

                // Initialize visibility based on current selection
                filterCities();
                filterBarangays();
            });
        </script>


        </section>

        </div>
    </main><!-- End #main -->
