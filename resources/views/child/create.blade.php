@extends('layouts.app')

@section('title', 'SFP Onse ')

@section('content')

<main id="main" class="main">

    <div class="pagetitle">
      
      <nav style="--bs-breadcrumb-divider: '>';">
        <ol class="breadcrumb mb-3 p-0">
          <li class="breadcrumb-item"><a href="{{ route('child.index') }}">Children</a></li>
          <li class="breadcrumb-item active">Child Details</li>
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
                            <h5 class="card-title">Child Details</h5>
                            <form class="row" method="post" action="{{ route('child.store') }} ">
                                @csrf

                                <div class='col-md-2 mt-2 text-gray-400 text-xs'>Personal Information</div>
                                <div class='col-md-10 mt-3 text-gray-400 text-xs'><hr></div>
                                
                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="firstname">First Name</label><label for="firstname" class="text-red-600">*</label>
                                    <input type="text" class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="firstname" name='firstname' value="{{ old('firstname') }}" autofocus >
                                    @error('firstname')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="middlename">Middle Name</label>
                                    <input type="text"  class="form-control invalid:border-red-500 rounded border-gray-300" id="middlename" name='middlename' value="{{ old('middlename') }}" >
                                    @error('middlename')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="lastname" >Last Name</label><label for="lastname" class="text-red-600">*</label>
                                    <input type="text"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="lastname" name='lastname' value="{{ old('lastname') }}"  >
                                    @error('lastname')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="extname" >Extension Name</label>
                                    <select  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="extname" name='extname'  >
                                        <option value="" disabled selected></option>
                                        <option value="jr" {{ old('extname') == 'jr' ? 'selected' : '' }}>Jr</option>
                                        <option value="sr" {{ old('extname') == 'sr' ? 'selected' : '' }}>Sr</option>
                                        <option value="i" {{ old('extname') == 'i' ? 'selected' : '' }}>I</option>
                                        <option value="ii" {{ old('extname') == 'ii' ? 'selected' : '' }}>II</option>
                                        <option value="iii" {{ old('extname') == 'iii' ? 'selected' : '' }}>III</option>
                                        <option value="iv" {{ old('extname') == 'iv' ? 'selected' : '' }}>IV</option>
                                        <option value="v" {{ old('extname') == 'v' ? 'selected' : '' }}>V</option>
                                        <option value="vi" {{ old('extname') == 'vi' ? 'selected' : '' }}>VI</option>
                                        <option value="vii" {{ old('extname') == 'vii' ? 'selected' : '' }}>VII</option>
                                        <option value="viii" {{ old('extname') == 'viii' ? 'selected' : '' }}>VIII</option>
                                        <option value="ix" {{ old('extname') == 'ix' ? 'selected' : '' }}>IX</option>
                                        <option value="x" {{ old('extname') == 'x' ? 'selected' : '' }}>X</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="date_of_birth" >Date of Birth</label><label for="date_of_birth" class="text-red-600">*</label>
                                    <input type="date"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="date_of_birth" name='date_of_birth' value="{{ old('date_of_birth') }}"  >
                                    @error('date_of_birth')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="sex" >Sex</label><label for="sex" class="text-red-600">*</label>
                                    <select  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="sex" name='sex'  >
                                        <option value="" disabled selected></option>
                                        <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('sex')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                {{-- <div class="col-md-3 mt-2 text-sm">
                                    <label for="weight" >Weight</label><label for="weight" class="text-red-600">*</label>
                                    <input type="text"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="weight" name='weight' value="{{ old('weight') }}" >
                                    @error('weight')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-3 mt-2 text-sm">
                                    <label for="height" >Height</label><label for="height" class="text-red-600">*</label>
                                    <input type="text"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="height" name='height'value="{{ old('height') }}" >
                                    @error('height')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="actual_date_of_weighing" >Actual date of weighing</label><label for="actual_date_of_weighing" class="text-red-600">*</label>
                                    <input type="date"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="actual_date_of_weighing" name='actual_date_of_weighing' value="{{ old('actual_date_of_weighing') }}"  >
                                    @error('actual_date_of_weighing')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div> --}}

                                
                                {{-- <div class="col-md-12 mt-2 text-sm">
                                    <label for="cdc_id">Child Development Center</label><label for="cdc_id" class="text-red-600">*</label>
                                    <select  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="cdc_id" name='cdc_id' >
                                        <option value="">Select a center</option>
                                            @foreach ($centers as $center)
                                                <option value="{{ $center->id }}" {{ old('cdc_id', $child->cdc_id ?? '') == $center->id ? 'selected' : '' }}>
                                                    {{ $center->name }}
                                                </option>
                                            @endforeach
                                    </select>
                                    @error('cdc_id')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div> --}}

                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="deworming_date">Deworming Date</label>
                                    <input type="date"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="deworming_date" name='deworming_date' value="{{ old('deworming') }}" >
                                </div>

                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="vitamin_a_date">Vitamin A</label>
                                    <input type="date"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="vitamin_a_date" name='vitamin_a_date' value="{{ old('vitamin_a') }}" >
                                </div>

                                <div class="col-md-4 mt-4 text-sm">
                                    <label for="is_pantawid">Pantawid Member:</label><label for="is_pantawid" class="text-red-600">*</label>
                                </div>
                                <div class="col-md-1 mt-4 text-sm">
                                    <input type="radio" id="is_pantawid_yes" name="is_pantawid" value="1" {{ old('is_pantawid') == '1' ? 'checked' : '' }}>
                                    <label for="is_pantawid_yes">Yes</label>
                                </div>
                                <div class="col-md-1 mt-4 text-sm">
                                    <input type="radio" id="is_pantawid_no" name="is_pantawid" value="0" {{ old('is_pantawid', '0') == '0' ? 'checked' : '' }}>
                                    <label for="is_pantawid_no">No</label>
                                </div>
                                <div class="col-md-6 mt-4 additional-details text-sm">
                                    <select  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="pantawid_details" name="pantawid_details" placeholder="Please specify if RCCT or MCCT" disabled>
                                        <option value="" disabled selected>Please specify </option>
                                        <option value="rcct">RCCT</option>
                                        <option value="mcct">MCCT</option>
                                    </select>
                                    @error('pantawid_details')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mt-2 text-sm">
                                    <label for="is_person_with_disability">Person with Disability:</label><label for="is_pwd" class="text-red-600">*</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" id="is_person_with_disability_yes" name="is_person_with_disability" value="1" {{ old('is_pwd') == '1' ? 'checked' : '' }}>
                                    <label for="is_person_with_disability_yes">Yes</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" id="is_person_with_disability_no" name="is_person_with_disability" value="0" {{ old('is_pwd', '0') == '0' ? 'checked' : '' }}>
                                    <label for="is_person_with_disability_no">No</label>
                                </div>
                                <div class="col-md-6 mt-2 text-sm additional-details" id="disability_additionalDetails">
                                    <input type="text"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="person_with_disability_details" name="person_with_disability_details" placeholder="Please specify" disabled>
                                    @error('person_with_disability_details')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mt-2 text-sm">
                                    <label for="is_indigenous_people">Indigenous People (IP):</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" name="is_indigenous_people" id="is_indigenous_people_yes" value="1" {{ old('is_ip') == '1' ? 'checked' : '' }}>
                                    <label for="is_indigenous_people_yes">Yes</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" name="is_indigenous_people" id="is_indigenous_people_no" value="0" {{ old('is_ip', '0') == '0' ? 'checked' : '' }}>
                                    <label for="is_indigenous_people_no">No</label>
                                </div>
                                <div class="col-md-6 mt-2 text-sm" style="visibility: hidden">
                                    <input type="text"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" name="spaceonly">
                                </div>

                                <div class="col-md-4 mt-2 text-sm">
                                    <label for="is_child_of_soloparent">Child of Solo Parent:</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" name="is_child_of_soloparent" id="is_child_of_soloparent_yes" value="1" {{ old('is_child_of_soloparent') == '1' ? 'checked' : '' }}>
                                    <label for="is_child_of_soloparent_yes">Yes</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" name="is_child_of_soloparent" id="is_child_of_soloparent_no" value="0" {{ old('is_child_of_soloparent', '0') == '0' ? 'checked' : '' }}>
                                    <label for="is_child_of_soloparent_no">No</label>
                                </div>
                                <div class="col-md-6 mt-2 text-sm" style="visibility: hidden">
                                    <input type="text"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" name="spaceonly">
                                </div>

                                <div class="col-md-4 mt-2 text-sm">
                                    <label for="is_lactose_intolerant">Lactose Intolerant:</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" name="is_lactose_intolerant" id="is_lactose_intolerant_yes" value="1" {{ old('is_lactose_intolerant') == '1' ? 'checked' : '' }}>
                                    <label for="is_lactose_intolerant_yes">Yes</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" name="is_lactose_intolerant" id="is_lactose_intolerant_no" value="0" {{ old('is_lactose_intolerant', '0') == '0' ? 'checked' : '' }}>
                                    <label for="is_lactose_intolerant_no">No</label>
                                </div>
                                <div class="col-md-6 mt-2 text-sm" style="visibility: hidden">
                                    <input type="text"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" name="spaceonly">
                                </div>

                                <div class='col-md-1 mt-4 text-gray-400 text-xs'>Address</div>
                                <div class='col-md-11 mt-8 text-gray-400 text-xs'><hr></div>
                                
                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="region">Region</label><label for="region" class="text-red-600">*</label>
                                    <select  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="region">
                                        <option value="110000000" selected>Region XI</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="province" >Province</label><label for="province" class="text-red-600">*</label>
                                    <select  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="province" name="province" disabled>
                                        <option value="" selected>Select Province</option>
                                    </select>
                                    @error('province')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                    
                                </div> 
                                
                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="city">City/Municipality</label><label for="city" class="text-red-600">*</label>
                                    <select  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="city" name="city" disabled>
                                        <option value="" selected>Select City/Municipality</option>
                                    </select>
                                    @error('city')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                    
                                </div>
                                
                        
                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="barangay">Barangay</label><label for="barangay" class="text-red-600">*</label>
                                    <select  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="barangay" name="barangay" disabled>
                                        <option value=""  selected>Select Barangay</option>
                                    </select>
                                    @error('barangay')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                    
                                </div>
                                
                                
                                <div class="col-6 mt-2 text-sm">
                                    <label for="address" >House No./ Street/ Purok</label><label for="address" class="text-red-600">*</label>
                                    <input type="text"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="address" name='address' value="{{ old('address') }}" >
                                    @error('address')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="zip_code">Zip Code</label><label for="zip_code" class="text-red-600">*</label>
                                    <input type="text"  class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="zip_code" name='zip_code' value="{{ old('zip_code') }}" maxlength="4" >
                                    @error('zip_code')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <input type="hidden" id="psgc_id" name="psgc_id" value="">

                                <div class="col-md-12 mt-4 text-right">
                                    <button type="submit" class="text-white bg-blue-600 rounded px-3 min-h-9">Submit</button>
                                    <button type="reset" class="text-white bg-gray-600 rounded px-3 min-h-9">Cancel</button>
                                </div>
                            </div>
                            </form><!-- End floating Labels Form -->
                        </div>
                    </div>  
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            
            {{-- pantawid and pwd additional details --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                // Function to toggle additional details based on radio button selection
                function toggleAdditionalDetails(radioName, additionalDetailsId) {
                    const radios = document.getElementsByName(radioName);
                    const additionalDetailsSelect = document.getElementById(additionalDetailsId);

                    radios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            if (radio.value === '1' && radio.checked) {
                                additionalDetailsSelect.disabled = false;
                            } else if (radio.value === '0' && radio.checked) {
                                additionalDetailsSelect.disabled = true;
                            }
                        });
                    });
                    
                }
                    // Apply the function to each set of radio buttons and additional details
                    toggleAdditionalDetails('is_pantawid', 'pantawid_details');
                    toggleAdditionalDetails('is_person_wtih_disability', 'person_with_disability_details');
                    
                });
            </script>

            {{-- city and barangay  --}}
            <script>
                $(document).ready(function() {
                    // Load provinces based on the initial region
                    function loadProvinces(regionPsgc) {
                        $.ajax({
                            url: '{{ route("get.provinces", ":region_psgc") }}'.replace(':region_psgc', regionPsgc),
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                $('#province').empty().append('<option value="" disabled selected>Select Province</option>');
                                $.each(data, function(index, province) {
                                    $('#province').append('<option value="' + province.province_psgc + '">' + province.province_name + '</option>');
                                });
                                $('#province').prop('disabled', false);
                            },
                            error: function(xhr, status, error) {
                                alert("An error occurred while loading provinces: " + error);
                            }
                        });
                    }
                
                    // Initial load of provinces
                    var regionPsgc = 110000000; // You can change this to a dynamic value later
                    loadProvinces(regionPsgc);
                
                    // Load cities
                    $('#province').change(function() {
                        var provincePsgc = $(this).val();
                        if (provincePsgc) {
                            $.ajax({
                                url: '{{ route("get.cities", ":province_psgc") }}'.replace(':province_psgc', provincePsgc),
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    $('#city').empty().append('<option value="" disabled selected>Select City/Municipality</option>');
                                    $.each(data, function(key, value) {
                                        $('#city').append('<option value="' + key + '">' + value + '</option>');
                                    });
                                    $('#city').prop('disabled', false);
                                    $('#barangay').prop('disabled', true).empty().append('<option value="" disabled selected>Select Barangay</option>');
                                },
                                error: function(xhr, status, error) {
                                    alert("An error occurred while loading cities: " + error);
                                }
                            });
                        } else {
                            $('#city').empty().append('<option value="" disabled selected>Select City/Municipality</option>').prop('disabled', true);
                            $('#barangay').empty().append('<option value="" disabled selected>Select Barangay</option>').prop('disabled', true);
                        }
                    });
                
                    // Load barangays
                    $('#city').change(function() {
                        var cityPsgc = $(this).val();
                        if (cityPsgc) {
                            $.ajax({
                                url: '{{ route("get.barangays", ":city_name_psgc") }}'.replace(':city_name_psgc', cityPsgc),
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    $('#barangay').empty().append('<option value="" disabled selected>Select Barangay</option>');
                                    $.each(data, function(key, value) {
                                        $('#barangay').append('<option value="' + key + '">' + value + '</option>');
                                    });
                                    $('#barangay').prop('disabled', false);
                                },
                                error: function(xhr, status, error) {
                                    alert("An error occurred while loading barangays: " + error);
                                }
                            });
                        } else {
                            $('#barangay').empty().append('<option value="" disabled selected>Select Barangay</option>').prop('disabled', true);
                        }
                    });
                });

                $('#barangay').change(function() {
                    var region_psgc = 110000000; // Assuming this is always Region XI
                    var province_psgc = $('#province').val();
                    var city_psgc = $('#city').val();
                    var brgy_psgc = $(this).val();

                    $.ajax({
                        url: '/psgc/' + region_psgc + '/' + province_psgc + '/' + city_psgc + '/' + brgy_psgc,
                        type: 'GET',
                        success: function(data) {
                            console.log('PSGC ID:', data[0]); // Log the PSGC ID
                            $('#psgc_id').val(data[0]); // Set the hidden input field with the PSGC ID
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching PSGC ID');
                        }
                    });
                });

            </script>
                
                

            </section>

    </div>
</main><!-- End #main -->