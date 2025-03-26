@extends('layouts.app')

@section('content')


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
        document.addEventListener('DOMContentLoaded', function() {
            var alert1 = document.getElementById('success-alert');
            var alert2 = document.getElementById('danger-alert');
            if (alert1) {
                // Automatically close the alert after 3 seconds (3000 milliseconds)
                setTimeout(function() {
                    var bsAlert1 = new bootstrap.Alert(alert1);
                    bsAlert1.close();
                }, 5000);
            }
            if (alert2) {
                // Automatically close the alert after 3 seconds (3000 milliseconds)
                setTimeout(function() {
                    var bsAlert2 = new bootstrap.Alert(alert2);
                    bsAlert2.close();
                }, 5000);
            }
        });
    </script>

    <div class="wrapper">
        <section class="section">

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <form class="row" method="post" action="{{ route('child.store') }} ">
                            @csrf

                            <input type="hidden" name="step" value="{{ session('step', 1) }}">

                            @if (session('step', 1) == 1)
                                <div id="step1" class="row">
                                    <h5 class="card-title ml-3">Personal Details</h5>
                                    <div class='col-md-12 mt-3 text-gray-400 text-xs'>Personal Information<hr></div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="firstname">First Name</label><label for="firstname"
                                            class="text-red-600">*</label>
                                        <input type="text"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="firstname" name='firstname'
                                            value="{{ old('firstname', session('step1Data.firstname')) }}" autofocus>
                                        @if ($errors->has('firstname'))
                                            <span class="text-xs text-red-600">{{ $errors->first('firstname') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="middlename">Middle Name</label>
                                        <input type="text"
                                            class="form-control invalid:border-red-500 rounded border-gray-300"
                                            id="middlename" name='middlename'
                                            value="{{ old('middlename', session('step1Data.middlename')) }}">
                                        @if ($errors->has('middlename'))
                                            <span class="text-xs text-red-600">{{ $errors->first('middlename') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="lastname">Last Name</label><label for="lastname"
                                            class="text-red-600">*</label>
                                        <input type="text"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="lastname" name='lastname'
                                            value="{{ old('lastname', session('step1Data.lastname')) }}">
                                        @if ($errors->has('lastname'))
                                            <span class="text-xs text-red-600">{{ $errors->first('lastname') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="extension_name">Extension Name</label>
                                        <select
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="extension_name" name='extension_name'>
                                            <option value="" disabled selected></option>
                                            <option value="Jr"
                                                {{ old('extension_name', session('step1Data.extension_name')) === 'Jr' ? 'selected' : '' }}>
                                                Jr
                                            </option>
                                            <option value="Sr"
                                                {{ old('extension_name', session('step1Data.extension_name')) === 'Sr' ? 'selected' : '' }}>
                                                Sr
                                            </option>
                                            <option value="I"
                                                {{ old('extension_name', session('step1Data.extension_name')) === 'I' ? 'selected' : '' }}>
                                                I
                                            </option>
                                            <option value="II"
                                                {{ old('extension_name', session('step1Data.extension_name')) === 'II' ? 'selected' : '' }}>
                                                II
                                            </option>
                                            <option value="III"
                                                {{ old('extension_name', session('step1Data.extension_name')) === 'III' ? 'selected' : '' }}>
                                                III
                                            </option>
                                            <option value="IV"
                                                {{ old('extension_name', session('step1Data.extension_name')) === 'IV' ? 'selected' : '' }}>
                                                IV
                                            </option>
                                            <option value="V"
                                                {{ old('extension_name', session('step1Data.extension_name')) === 'V' ? 'selected' : '' }}>
                                                V
                                            </option>
                                            <option value="VI"
                                                {{ old('extension_name', session('step1Data.extension_name')) === 'VI' ? 'selected' : '' }}>
                                                VI
                                            </option>
                                            <option value="VII"
                                                {{ old('extension_name', session('step1Data.extension_name')) === 'VII' ? 'selected' : '' }}>
                                                VII
                                            </option>
                                            <option value="VIII"
                                                {{ old('extension_name', session('step1Data.extension_name')) === 'VIII' ? 'selected' : '' }}>
                                                VIII
                                            </option>
                                            <option value="IX"
                                                {{ old('extension_name', session('step1Data.extension_name')) === 'IX' ? 'selected' : '' }}>
                                                IX
                                            </option>
                                            <option value="X"
                                                {{ old('extension_name', session('step1Data.extension_name')) === 'X' ? 'selected' : '' }}>
                                                X
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="date_of_birth">Date of Birth</label><label for="date_of_birth"
                                            class="text-red-600">*</label>
                                        <input type="date"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="date_of_birth" name='date_of_birth'
                                            value="{{ old('date_of_birth', session('step1Data.date_of_birth')) }}"
                                            max="{{ date('Y-m-d') }}">
                                        @if ($errors->has('date_of_birth'))
                                            <span class="text-xs text-red-600">{{ $errors->first('date_of_birth') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="sex">Sex</label><label for="sex"
                                            class="text-red-600">*</label>
                                        <select
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="sex_id" name="sex_id">
                                            <option value="" disabled
                                                {{ old('sex_id', session('step1Data.sex_id')) == '' ? 'selected' : '' }}>
                                                Select sex</option>
                                            @foreach ($sexOptions as $sex)
                                                <option value="{{ $sex->id }}"
                                                    {{ $sex->id == old('sex_id', session('step1Data.sex_id')) ? 'selected' : '' }}>
                                                    {{ $sex->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('sex_id'))
                                            <span class="text-xs text-red-600">{{ $errors->first('sex_id') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-4 mt-4 text-sm">
                                        <label for="is_pantawid">Pantawid Member:</label><label for="is_pantawid"
                                            class="text-red-600">*</label>
                                    </div>
                                    <div class="col-md-1 mt-4 text-sm">
                                        <input type="radio" id="is_pantawid_yes" name="is_pantawid" value="1"
                                            {{ old('is_pantawid', session('step1Data.is_pantawid')) === '1' ? 'checked' : '' }}>
                                        <label for="is_pantawid_yes">Yes</label>
                                    </div>
                                    <div class="col-md-1 mt-4 text-sm">
                                        <input type="radio" id="is_pantawid_no" name="is_pantawid" value="0"
                                            {{ old('is_pantawid', session('step1Data.is_pantawid')) === '0' ? 'checked' : '' }}>
                                        <label for="is_pantawid_no">No</label>
                                    </div>
                                    <div class="col-md-6 mt-4 text-sm additional-details">
                                        <select class="form-control rounded border-gray-300" id="pantawid_details"
                                            name="pantawid_details" placeholder="Please specify if RCCT or MCCT" disabled>
                                            <option value="" disabled selected>Select an option</option>
                                            <option value="rcct"
                                                {{ old('pantawid_details', session('step1Data.pantawid_details')) === 'rcct' ? 'selected' : '' }}>
                                                RCCT</option>
                                            <option value="mcct"
                                                {{ old('pantawid_details', session('step1Data.pantawid_details')) === 'mcct' ? 'selected' : '' }}>
                                                MCCT</option>
                                        </select>
                                        @if ($errors->has('pantawid_details'))
                                            <span
                                                class="text-xs text-red-600">{{ $errors->first('pantawid_details') }}</span>
                                        @endif
                                    </div>


                                    <div class="col-md-4 mt-2 text-sm">
                                        <label for="is_person_with_disability">Person with Disability:</label><label
                                            for="is_person_with_disability" class="text-red-600">*</label>
                                    </div>
                                    <div class="col-md-1 mt-2 text-sm">
                                        <input type="radio" id="is_person_with_disability_yes"
                                            name="is_person_with_disability" value="1"
                                            {{ old('is_person_with_disability', session('step1Data.is_person_with_disability')) === '1' ? 'checked' : '' }}>
                                        <label for="is_person_with_disability_yes">Yes</label>
                                    </div>
                                    <div class="col-md-1 mt-2 text-sm">
                                        <input type="radio" id="is_person_with_disability_no"
                                            name="is_person_with_disability" value="0"
                                            {{ old('is_person_with_disability', session('step1Data.is_person_with_disability')) === '0' ? 'checked' : '' }}>
                                        <label for="is_person_with_disability_no">No</label>
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm additional-details">
                                        <input type="text" class="form-control rounded border-gray-300"
                                            id="person_with_disability_details" name="person_with_disability_details"
                                            placeholder="Please specify" disabled
                                            value="{{ old('person_with_disability_details', session('step1Data.person_with_disability_details')) }}">
                                        @if ($errors->has('person_with_disability_details'))
                                            <span
                                                class="text-xs text-red-600">{{ $errors->first('person_with_disability_details') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-4 mt-2 text-sm">
                                        <label for="is_indigenous_people">Indigenous People (IP):</label>
                                    </div>
                                    <div class="col-md-1 mt-2 text-sm">
                                        <input type="radio" name="is_indigenous_people" id="is_indigenous_people_yes"
                                            value="1"
                                            {{ old('is_indigenous_people', session('step1Data.is_indigenous_people')) === '1' ? 'checked' : '' }}>
                                        <label for="is_indigenous_people_yes">Yes</label>
                                    </div>
                                    <div class="col-md-1 mt-2 text-sm">
                                        <input type="radio" name="is_indigenous_people" id="is_indigenous_people_no"
                                            value="0"
                                            {{ old('is_indigenous_people', session('step1Data.is_indigenous_people')) === '0' ? 'checked' : '' }}>
                                        <label for="is_indigenous_people_no">No</label>
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm" style="visibility: hidden">
                                        <input type="text"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            name="spaceonly">
                                    </div>

                                    <div class="col-md-4 mt-2 text-sm">
                                        <label for="is_child_of_soloparent">Child of Solo Parent:</label>
                                    </div>
                                    <div class="col-md-1 mt-2 text-sm">
                                        <input type="radio" name="is_child_of_soloparent"
                                            id="is_child_of_soloparent_yes" value="1"
                                            {{ old('is_child_of_soloparent', session('step1Data.is_child_of_soloparent')) === '1' ? 'checked' : '' }}>
                                        <label for="is_child_of_soloparent_yes">Yes</label>
                                    </div>
                                    <div class="col-md-1 mt-2 text-sm">
                                        <input type="radio" name="is_child_of_soloparent"
                                            id="is_child_of_soloparent_no" value="0"
                                            {{ old('is_child_of_soloparent', session('step1Data.is_child_of_soloparent')) === '0' ? 'checked' : '' }}>
                                        <label for="is_child_of_soloparent_no">No</label>
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm" style="visibility: hidden">
                                        <input type="text"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            name="spaceonly">
                                    </div>

                                    <div class="col-md-4 mt-2 text-sm">
                                        <label for="is_lactose_intolerant">Lactose Intolerant:</label>
                                    </div>
                                    <div class="col-md-1 mt-2 text-sm">
                                        <input type="radio" name="is_lactose_intolerant" id="is_lactose_intolerant_yes"
                                            value="1"
                                            {{ old('is_lactose_intolerant', session('step1Data.is_lactose_intolerant')) === '1' ? 'checked' : '' }}>
                                        <label for="is_lactose_intolerant_yes">Yes</label>
                                    </div>
                                    <div class="col-md-1 mt-2 text-sm">
                                        <input type="radio" name="is_lactose_intolerant" id="is_lactose_intolerant_no"
                                            value="0"
                                            {{ old('is_lactose_intolerant', session('step1Data.is_lactose_intolerant')) === '0' ? 'checked' : '' }}>
                                        <label for="is_lactose_intolerant_no">No</label>
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm" style="visibility: hidden">
                                        <input type="text"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            name="spaceonly">
                                    </div>

                                    <div class='col-md-12 mt-4 text-gray-400 text-xs'>Address<hr></div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="region">Region</label><label for="region"
                                            class="text-red-600">*</label>
                                        <select
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="region_psgc" name="region_psgc" disabled>
                                            <option value="110000000" selected>Region XI</option>
                                        </select>
                                    </div>

                                    <input type="hidden" name="region_psgc" value="110000000">

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="province">Province</label><label for="province"
                                            class="text-red-600">*</label>
                                        <select class="form-control" id="province" name="province_psgc">
                                            <option value="" selected>Select Province</option>
                                            @foreach ($provinces as $psgc => $name)
                                                <option value="{{ $psgc }}"
                                                    {{ old('province_psgc', session('step1Data.province_psgc')) == $psgc ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('province_psgc'))
                                            <span
                                                class="text-xs text-red-600">{{ $errors->first('province_psgc') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="city">City/Municipality</label><label for="city"
                                            class="text-red-600">*</label>
                                        <select class="form-control" id="city" name="city_name_psgc">
                                            <option value="" selected>Select City/Municipality</option>
                                            @foreach ($cities as $psgc => $name)
                                                <option value="{{ $psgc }}"
                                                    {{ old('city_name_psgc', session('step1Data.city_name_psgc')) == $psgc ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('city_name_psgc'))
                                            <span
                                                class="text-xs text-red-600">{{ $errors->first('city_name_psgc') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="barangay">Barangay</label><label for="barangay"
                                            class="text-red-600">*</label>
                                        <select class="form-control" id="barangay" name="brgy_psgc">
                                            <option value="" selected>Select Barangay</option>
                                            @foreach ($barangays as $psgc => $name)
                                                <option value="{{ $psgc }}"
                                                    {{ old('brgy_psgc', session('step1Data.brgy_psgc')) == $psgc ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('brgy_psgc'))
                                            <span class="text-xs text-red-600">{{ $errors->first('brgy_psgc') }}</span>
                                        @endif
                                    </div>


                                    <div class="col-12 mt-2 text-sm">
                                        <label for="address">House No./ Street/ Purok</label><label for="address"
                                            class="text-red-600">*</label>
                                        <input type="text"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="address" name='address'
                                            value="{{ old('address', session('step1Data.address')) }}">
                                        @if ($errors->has('address'))
                                            <span class="text-xs text-red-600">{{ $errors->first('address') }}</span>
                                        @endif
                                    </div>

                                    <input type="hidden" id="psgc_id" name="psgc_id" value="">
                                </div>

                            @elseif(session('step') == 2)
                                <div id="step2" class="row step">
                                    <h5 class="card-title">Center and Implement Details</h5>

                                    {{-- <div class='col-md-12 mt-4 text-gray-400 text-xs'>Child Development Center/Supervise Neighborhood Play<hr></div> --}}

                                    <div class="flex flex-wrap">
                                        <div class='w-full px-3 mt-2 text-gray-400 text-xs'>Personal Information<hr></div>
                                    </div>

                                    <div class="col-md-12 mt-3 text-sm">
                                        <label for="child_development_center_id">CDC or SNP <span
                                                class="text-red-600">*</span></label>
                                        <select class="form-control rounded border-gray-300"
                                            id="child_development_center_id" name='child_development_center_id'>
                                            <option value="" disabled selected>Select CDC or SNP</option>
                                            @foreach ($centerNames as $center)
                                                <option value="{{ $center->id }}"
                                                    {{ $center->id == old('child_development_center_id', session('step2Data.child_development_center_id')) ? 'selected' : '' }}>
                                                    {{ $center->center_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('child_development_center_id'))
                                            <span
                                                class="text-xs text-red-600">{{ $errors->first('child_development_center_id') }}</span>
                                        @endif
                                    </div>

                                    <div class='col-md-12 mt-4 text-gray-400 text-xs'>Implementation<hr></div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="implementation_id">Cycle Implementation</label>
                                        <select class="form-control rounded border-gray-300" id="implementation_id"
                                            name='implementation_id'>
                                            <option value="" selected>Not Applicable</option>
                                            @foreach ($cycleImplementations as $cycle)
                                                <option value="{{ $cycle->id }}"
                                                    {{ $cycle->id == old('implementation_id', session('step2Data.implementation_id')) ? 'selected' : '' }}>
                                                    {{ $cycle->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="milk_feeding_id">Milk Feeding Implementation</label>
                                        <select class="form-control rounded border-gray-300" id="milk_feeding_id"
                                            name='milk_feeding_id'>
                                            <option value="" selected>Not Applicable</option>
                                            @foreach ($milkFeedings as $milkFeeding)
                                                <option value="{{ $milkFeeding->id }}"
                                                    {{ $milkFeeding->id == old('milk_feeding_id', session('step2Data.milk_feeding_id')) ? 'selected' : '' }}>
                                                    {{ $milkFeeding->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @elseif(session('step') == 3)
                                <div id="step3" class="row step">
                                    <h5 class="card-title ml-3">Summary</h5>

                                    <div class='col-md-6 mt-3 text-gray-400 text-xs'>Personal Information</div>
                                    <div class='col-md-6 mt-3 text-gray-400 text-xs'>Address</div>

                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>First Name:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='firstname'
                                            value="{{ session('step1Data.firstname') }}" disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Region:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='region_psgc'
                                            value="{{ session('step1Data.region_name') }}" disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Middle Name:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='middlename'
                                            value="{{ session('step1Data.middlename') }}" disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Province:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='province_psgc'
                                            value="{{ session('step1Data.province_name') }}" disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Last Name:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='lastname'
                                            value="{{ session('step1Data.lastname') }}" disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>City/Municpality:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='city_name_psgc'
                                            value="{{ session('step1Data.city_name') }}" disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Extension Name:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" id="extension_name"
                                            name="extension_name" value="{{ session('step1Data.extension_name') ?? '' }}"
                                            disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Barangay:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='brgy_psgc'
                                            value="{{ session('step1Data.brgy_name') }}" disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Date of birth:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='date_of_birth'
                                            value="{{ session('step1Data.date_of_birth') }}" disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Address:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='address'
                                            value="{{ session('step1Data.address') }}" disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Sex:</label>
                                    </div>
                                    <div class="col-md-10 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='sex_name'
                                            value="{{ session('step1Data.sex_name') }}" disabled>
                                    </div>

                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Pantawid Member:</label>
                                    </div>
                                    <div class="col-md-10 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='pantawid_details'
                                            value="{{ session('step1Data.pantawid_details') ? session('step1Data.pantawid_details') : 'No' }}"
                                            disabled>
                                    </div>

                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Person with Disability:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300"
                                            name='person_with_disability_details'
                                            value="{{ session('step1Data.person_with_disability_details') ? session('step1Data.person_with_disability_details') : 'No' }}"
                                            disabled>
                                    </div>
                                    <div class='col-md-6 mt-3 text-gray-400 text-xs'>Center Information</div>

                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Indigenous People:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='is_indigenous_people'
                                            value="{{ session('step1Data.is_indigenous_people') ? 'Yes' : 'No' }}"
                                            disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>CDC/SNP:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300"
                                            name='child_development_center_id'
                                            value="{{ session('step2Data.center_name') }}" disabled>
                                    </div>

                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Child of Solo Parent:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300"
                                            name='is_child_of_soloparent'
                                            value="{{ session('step1Data.is_child_of_soloparent') ? 'Yes' : 'No' }}"
                                            disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Cycle Implementation:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='implementation_id'
                                            value="{{ session('step2Data.implementation_name') }}" disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Lactose Intolerant:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300"
                                            name='is_lactose_intolerant'
                                            value="{{ session('step1Data.is_lactose_intolerant') ? 'Yes' : 'No' }}"
                                            disabled>
                                    </div>
                                    <div class="col-md-2 mt-3 text-sm">
                                        <label>Milk Feeding:</label>
                                    </div>
                                    <div class="col-md-4 mt-1 text-sm">
                                        <input type="text" class="rounded border-gray-300" name='milk_feeding_id'
                                            value="{{ session('step2Data.milk_feeding_name') }}" disabled>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-12 mt-4 text-right">
                                @if (session('step', 1) > 1)
                                    <button type="submit" name="action" value="prev"
                                        class="text-white bg-gray-600 rounded px-3 min-h-9">Previous</button>
                                @endif

                                @if (session('step', 1) < 3)
                                    <button type="submit" name="action" value="next"
                                        class="text-white bg-blue-600 rounded px-3 min-h-9">Next</button>
                                @else
                                    <button type="submit" name="action" value="submit"
                                        class="text-white bg-blue-600 rounded px-3 min-h-9">Submit</button>
                                @endif
                            </div>

                        </form><!-- End floating Labels Form -->


                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.2/dist/cdn.min.js" defer></script>

    <!-- {{-- pantawid and pwd additional details --}} -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function toggleAdditionalDetails(radioName, additionalDetailsId) {
                const radios = document.getElementsByName(radioName);
                const additionalDetailsSelect = document.getElementById(additionalDetailsId);

                function updateFieldState() {
                const selectedRadio = document.querySelector(`input[name="${radioName}"]:checked`);

                if (selectedRadio && selectedRadio.value === '1') {
                    additionalDetailsSelect.disabled = false;
                    additionalDetailsSelect.setAttribute('required', 'required');
                } else {
                    additionalDetailsSelect.disabled = true;
                    additionalDetailsSelect.removeAttribute('required');
                    additionalDetailsSelect.value = '';
                }
            }

            // Run on page load to preserve state when going back
            updateFieldState();

            // Add event listener to update when selection changes
            radios.forEach(radio => {
                radio.addEventListener('change', updateFieldState);
            });

            }

            toggleAdditionalDetails('is_pantawid', 'pantawid_details');
            toggleAdditionalDetails('is_person_with_disability', 'person_with_disability_details');

        });
    </script>

    {{-- city and barangay  --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get dropdown elements
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');
            const barangaySelect = document.getElementById('barangay');

            // Convert PHP arrays to JSON for JavaScript use
            const locations = {
                provinces: @json($provinces),
                cities: {!! json_encode($cities, JSON_UNESCAPED_UNICODE) !!},
                barangays: {!! json_encode($barangays, JSON_UNESCAPED_UNICODE) !!}
            };

            // Get old selected values (from Laravel session for form validation)
            let selectedProvince = @json(old('province_psgc', session('step1Data.province_psgc')));
            let selectedCity = @json(old('city_name_psgc', session('step1Data.city_name_psgc')));
            let selectedBarangay = @json(old('brgy_psgc', session('step1Data.brgy_psgc')));

            // Disable city and barangay initially
            citySelect.disabled = true;
            barangaySelect.disabled = true;

            // Function to reset and populate cities when province changes
            function populateCities(provincePsgc) {
                citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
                barangaySelect.innerHTML = '<option value="">Select Barangay</option>'; // Reset barangay

                citySelect.disabled = !provincePsgc; // Enable only if a province is selected
                barangaySelect.disabled = true; // Always reset barangay when province changes

                if (provincePsgc && locations.cities[provincePsgc]) {
                    locations.cities[provincePsgc].forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.psgc;
                        option.text = city.name;
                        citySelect.appendChild(option);
                    });

                    // Restore selected city after validation error
                    if (selectedCity && locations.cities[provincePsgc].some(city => city.psgc == selectedCity)) {
                        citySelect.value = selectedCity;
                        populateBarangays(selectedCity); // Load barangays for selected city
                    } else {
                        selectedCity = ''; // Reset if no match
                    }
                }
            }

            // Function to reset and populate barangays when city changes
            function populateBarangays(cityPsgc) {
                barangaySelect.innerHTML = '<option value="">Select Barangay</option>'; // Always clear barangay

                barangaySelect.disabled = !cityPsgc; // Enable only if a city is selected

                if (cityPsgc && locations.barangays[cityPsgc]) {
                    locations.barangays[cityPsgc].forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay.psgc;
                        option.text = barangay.name;
                        barangaySelect.appendChild(option);
                    });

                    // Restore selected barangay after validation error
                    if (selectedBarangay && locations.barangays[cityPsgc].some(barangay => barangay.psgc == selectedBarangay)) {
                        barangaySelect.value = selectedBarangay;
                    } else {
                        selectedBarangay = ''; // Reset if no match
                    }
                }
            }

            // Load old selections if they exist (for validation errors)
            if (selectedProvince) {
                provinceSelect.value = selectedProvince;
                populateCities(selectedProvince);
            }

            // Event Listeners
            provinceSelect.addEventListener('change', function() {
                selectedProvince = this.value;
                selectedCity = ''; // Reset city when province changes
                selectedBarangay = ''; // Reset barangay when province changes
                populateCities(this.value);
            });

            citySelect.addEventListener('change', function() {
                selectedCity = this.value;
                selectedBarangay = ''; // Reset barangay when city changes
                populateBarangays(this.value);
            });
        });
    </script>
@endsection
