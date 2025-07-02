@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>'; ">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('child.index') }}" class="no-underline">Children</a></li>
                <li class="breadcrumb-item active uppercase">{{ $child->full_name }}</li>
            </ol>
        </nav>
    </div>

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

    {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var alert1 = document.getElementById('success-alert');
            var alert2 = document.getElementById('danger-alert');
            if (alert1) {
                // Automatically close the alert after 3 seconds (3000 milliseconds)
                setTimeout(function() {
                    var bsAlert1 = new bootstrap.Alert(alert1);
                    bsAlert1.close();
                }, 3000);
            }
            if (alert2) {
                // Automatically close the alert after 3 seconds (3000 milliseconds)
                setTimeout(function() {
                    var bsAlert2 = new bootstrap.Alert(alert2);
                    bsAlert2.close();
                }, 3000);
            }
        });
    </script>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class='card-title uppercase'>{{ $child->full_name }}</h5>

                            <!-- Personal Information Form -->
                            <form class="row" method="post" action="{{ route('child.update') }}" novalidate>
                                @csrf
                                @method('patch')

                                <input type="hidden" name="child_id" value="{{ $child->id }}">

                                @if(!auth()->user()->hasRole('lgu focal'))
                                    <div class='col-md-12 mt-3 text-gray-400 text-xs'>Personal Information<hr></div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="firstname">First Name<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="firstname"
                                            name='firstname' value="{{ old('firstname', $child->firstname) }}" autofocus>
                                        @error('firstname')
                                            <span class="text-xs text-red-600">{{ $errors->first('firstname') }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="middlename">Middle Name</label>
                                        <input type="text" class="form-control rounded border-gray-300" id="middlename"
                                            name='middlename' value="{{ old('middlename', $child->middlename) }}">
                                        @error('middlename')
                                            <span class="text-xs text-red-600">{{ $errors->first('firstname') }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="lastname">Last Name</label>
                                        <input type="text" class="form-control rounded border-gray-300" id="lastname"
                                            name='lastname' value="{{ old('lastname', $child->lastname) }}">
                                        @error('lastname')
                                            <span class="text-xs text-red-600">{{ $errors->first('firstname') }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="extension_name">Extension Name</label>
                                        <select class="form-control rounded border-gray-300" id="extension_name"
                                            name="extension_name">
                                            <option value=""></option>
                                            @foreach ($extNameOptions as $value1 => $label1)
                                                <option value="{{ $value1 }}"
                                                    {{ old('extension_name', $child->extension_name) == $value1 ? 'selected' : '' }}>
                                                    {{ $label1 }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- @php
                                        use Carbon\Carbon;

                                        $min = Carbon::now()->subYears(5)->startOfYear()->format('Y-m-d');
                                        $max = Carbon::now()->subYears(2)->endOfYear()->format('Y-m-d');
                                    @endphp --}}
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="date_of_birth">Date of Birth<b class="text-red-600">*</b></label>
                                        <input type="date" class="form-control rounded border-gray-300" id="date_of_birth"
                                            name='date_of_birth' value="{{ old('date_of_birth', $child->date_of_birth->format('Y-m-d')) }}" min="{{ $minDate }}" max="{{ $maxDate }}">
                                        @error('date_of_birth')
                                            <span class="text-xs text-red-600">{{ $errors->first('date_of_birth') }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="sex">Sex<b class="text-red-600">*</b></label>
                                        <select class="form-control rounded border-gray-300" id="sex_id" name="sex_id">
                                            <option value="" disabled>Select Sex</option>
                                            @foreach ($sexOptions as $sex)
                                                <option value="{{ $sex->id }}"
                                                    {{ old('sex_id', $child->sex_id) == $sex->id ? 'selected' : '' }}>
                                                    {{ $sex->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('sex_id')
                                            <span class="text-xs text-red-600">{{ $errors->first('sex_id') }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mt-4">
                                        <label for="is_pantawid">Pantawid Member:<b class="text-red-600">*</b></label>
                                        @if ($errors->has('is_pantawid'))
                                                <span
                                                class="text-xs text-red-600">{{ $errors->first('is_pantawid') }}</span>
                                            @endif
                                    </div>
                                    <div class="col-md-1 mt-4">
                                        <input type="radio" id="is_pantawid_yes" name="is_pantawid" value="1"
                                            {{ old('is_pantawid', $isChildPantawid) == true ? 'checked' : '' }}>
                                        <label for="is_pantawid_yes">Yes</label>
                                    </div>
                                    <div class="col-md-1 mt-4">
                                        <input type="radio" id="is_pantawid_no" name="is_pantawid" value="0"
                                            {{ old('is_pantawid', $isChildPantawid) == false ? 'checked' : '' }}>
                                        <label for="is_pantawid_no">No</label>
                                    </div>
                                    <div class="col-md-6 mt-4 additional-details">
                                        <label for="pantawid_details">Pantawid Details:</label><b
                                                class="text-red-600">*</b>
                                        <select class="form-control rounded border-gray-300" id="pantawid_details"
                                            name="pantawid_details"
                                            {{ old('pantawid_details', $child->pantawid_details) == null ? 'disabled' : '' }}>
                                            <option value="" selected disabled>SELECT DETAILS</option>
                                            @foreach ($pantawidDetails as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ old('pantawid_details', $child->pantawid_details) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('pantawid_details')
                                            <span class="text-xs text-red-600">{{ $errors->first('firstname') }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mt-2">
                                        <label for="is_person_with_disability">Person with
                                            Disability:<b class="text-red-600">*</b></label>
                                            @if ($errors->has('is_person_with_disability'))
                                            <span
                                                class="text-xs text-red-600">{{ $errors->first('is_person_with_disability') }}</span>
                                            @endif
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <input type="radio" id="is_person_with_disability_yes"
                                            name="is_person_with_disability" value="1"
                                            {{ old('is_person_with_disability', $isChildPWD) == true ? 'checked' : '' }}>
                                        <label for="is_person_with_disability_yes">Yes</label>
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <input type="radio" id="is_person_with_disability_no"
                                            name="is_person_with_disability" value="0"
                                            {{ old('is_person_with_disability', $isChildPWD) == false ? 'checked' : '' }}>
                                        <label for="is_person_with_disability_no">No</label>
                                    </div>
                                    <div class="col-md-6 mt-2 additional-details" id="person_with_disability_additionalDetails">
                                        <label for="person_with_disability_details">Disability Details:</label><b class="text-red-600">*</b>
                                        <select class="form-control rounded border-gray-300"
                                                id="person_with_disability_details"
                                                name="person_with_disability_details"
                                            {{ $isChildPWD ? '' : 'disabled' }}>
                                            <option value="">-- Select Disability --</option>
                                            @foreach ($disabilities as $disability)
                                                <option value="{{ $disability }}"
                                                    {{ old('person_with_disability_details', $child->person_with_disability_details) == $disability ? 'selected' : '' }}>
                                                    {{ $disability }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('person_with_disability_details')
                                        <span class="text-xs text-red-600">{{ $errors->first('firstname') }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mt-2">
                                        <label for="is_indigenous_people">Indigenous People (IP):</label><b
                                        class="text-red-600">*</b>
                                        @if ($errors->has('is_indigenous_people'))
                                                <span
                                                    class="text-xs text-red-600">{{ $errors->first('is_indigenous_people') }}</span>
                                            @endif
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <input type="radio" name="is_indigenous_people" id="is_indigenous_people_yes"
                                            value="1"
                                            {{ old('is_indigenous_people', $child->is_indigenous_people) == '1' ? 'checked' : '' }}>
                                        <label for="is_indigenous_people_yes">Yes</label>
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <input type="radio" name="is_indigenous_people" id="is_indigenous_people_no"
                                            value="0"
                                            {{ old('is_indigenous_people', $child->is_indigenous_people) == '0' ? 'checked' : '' }}>
                                        <label for="is_indigenous_people_no">No</label>
                                    </div>
                                    <div class="col-md-6 mt-2" style="visibility: hidden">
                                        <input type="text" class="form-control rounded border-gray-300" name="spaceonly">
                                    </div>
                                    <div class="col-md-4 mt-2">
                                        <label for="is_child_of_soloparent">Child of Solo Parent:</label><b
                                        class="text-red-600">*</b>
                                        @if ($errors->has('is_child_of_soloparent'))
                                                <span
                                                    class="text-xs text-red-600">{{ $errors->first('is_child_of_soloparent') }}</span>
                                            @endif
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <input type="radio" name="is_child_of_soloparent" id="is_child_of_soloparent_yes"
                                            value="1"
                                            {{ old('is_child_of_soloparent', $child->is_child_of_soloparent) == '1' ? 'checked' : '' }}>
                                        <label for="is_child_of_soloparent_yes">Yes</label>
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <input type="radio" name="is_child_of_soloparent" id="is_child_of_soloparent_no"
                                            value="0"
                                            {{ old('is_child_of_soloparent', $child->is_child_of_soloparent) == '0' ? 'checked' : '' }}>
                                        <label for="is_child_of_soloparent_no">No</label>
                                    </div>
                                    <div class="col-md-6 mt-2" style="visibility: hidden">
                                        <input type="text" class="form-control rounded border-gray-300" name="spaceonly">
                                    </div>
                                    <div class="col-md-4 mt-2">
                                        <label for="is_lactose_intolerant">Lactose Intolerant:</label><b
                                        class="text-red-600">*</b>
                                        @if ($errors->has('is_lactose_intolerant'))
                                                <span
                                                    class="text-xs text-red-600">{{ $errors->first('is_lactose_intolerant') }}</span>
                                            @endif
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <input type="radio" name="is_lactose_intolerant" id="is_lactose_intolerant_yes"
                                            value="1"
                                            {{ old('is_lactose_intolerant', $child->is_lactose_intolerant) == '1' ? 'checked' : '' }}>
                                        <label for="is_lactose_intolerant_yes">Yes</label>
                                    </div>
                                    <div class="col-md-1 mt-2">
                                        <input type="radio" name="is_lactose_intolerant" id="is_lactose_intolerant_no"
                                            value="0"
                                            {{ old('is_lactose_intolerant', $child->is_lactose_intolerant) == '0' ? 'checked' : '' }}>
                                        <label for="is_lactose_intolerant_no">No</label>
                                    </div>
                                    <div class="col-md-6 mt-2" style="visibility: hidden">
                                        <input type="text" class="form-control rounded border-gray-300" name="spaceonly">
                                    </div>

                                    <div class='col-md-12 mt-4 text-gray-400 text-xs'>Address<hr>
                                    </div>



                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="region">Region</label><label for="region"
                                            class="text-red-600">*</label>
                                        <select
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="region" disabled>
                                            <option value="110000000" selected>Region XI</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="province">Province<b class="text-red-600">*</b></label>
                                        <select class="form-control rounded border-gray-300" id="province"
                                            name="province_psgc">
                                            <option value="" selected>Select Province</option>
                                            @foreach ($provinces as $psgc => $name)
                                                <option value="{{ $psgc }}"
                                                    {{ $psgc == old('province_psgc', $psgcRecord->province_psgc) ? 'selected' : '' }}>
                                                    {{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('province_psgc')
                                            <span class="text-xs text-red-600">{{ $errors->first('province_psgc') }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="city">City/Municipality<b class="text-red-600">*</b></label>
                                        <select class="form-control rounded border-gray-300" id="city"
                                            name="city_name_psgc">
                                            <option value="" selected>Select City/Municipality</option>
                                            @foreach ($cities as $psgc => $name)
                                                <option value="{{ $psgc }}"
                                                    {{ $psgc == old('city_name_psgc', $psgcRecord->city_name_psgc) ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('city_name_psgc')
                                            <span class="text-xs text-red-600">{{ $errors->first('city_name_psgc') }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="barangay">Barangay<b class="text-red-600">*</b></label>
                                        <select class="form-control rounded border-gray-300" id="barangay" name="brgy_psgc">
                                            <option value="" selected>Select Barangay</option>
                                            @foreach ($barangays as $psgc => $name)
                                                <option value="{{ $psgc }}"
                                                    {{ $psgc == old('brgy_psgc', $psgcRecord->brgy_psgc) ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('brgy_psgc')
                                            <span class="text-xs text-red-600">{{ $errors->first('brgy_psgc') }}</span>
                                        @enderror
                                    </div>


                                    <input type="hidden" id="psgc_id" name="psgc_id" value="{{ $child->psgc_id }}">

                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="address">House No./ Street/ Purok<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="address"
                                            name='address' value="{{ old('address', $child->address) }}">
                                        @error('address')
                                            <span class="text-xs text-red-600">{{ $errors->first('address') }}</span>
                                        @enderror
                                    </div>
                                @endif

                                @if(auth()->user()->hasRole('lgu focal') || auth()->user()->hasRole('sfp coordinator') || auth()->user()->hasRole('admin'))

                                    <div class='col-md-12 mt-4 text-gray-400 text-xs'>Child Development Center or Supervised
                                        Neighborhood Play<hr>
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="child_development_center_id">CDC or SNP</label><b
                                            class="text-red-600">*</b>
                                        <select
                                            class="form-control rounded border-gray-300 uppercase"
                                            id="child_development_center_id" name='child_development_center_id'>
                                            <option value="" disabled selected>Select CDC or SNP</option>
                                            @foreach ($centerNames as $center)
                                                <option value="{{ $center->id }}"
                                                    {{ $center->id == old('child_development_center_id', $childCenterId->child_development_center_id) ? 'selected' : '' }}>
                                                    {{ $center->center_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('child_development_center_id')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-2 text-sm" style="visibility: hidden">
                                        <input type="text"
                                            class="form-control rounded border-gray-300"
                                            name="spaceonly">
                                    </div>

                                    <input type="hidden" name="implementation_id" value="{{ $childCenterId->implementation_id }}">
                                    <input type="hidden" name="is_funded" value="{{ $childCenterId->funded }}" />
                                @endif
                                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('child development worker') || auth()->user()->hasRole('encoder'))
                                    <div class='col-md-12 mt-4 text-gray-400 text-xs'>Implementation<hr>
                                    </div>
                                @endif

                                @if(auth()->user()->hasRole('admin'))
                                    <div class="col-md-6 text-sm">
                                        <label for="cycle_implementation_id">Cycle Implementation</label>
                                        <select class="form-control rounded border-gray-300" id="implementation_select"
                                            name='implementation_select' readonly>
                                            <option value="{{ $cycle->id }}">{{ $cycle->name }}
                                            </option>
                                        </select>
                                        <input type="hidden" id="implementation_id" name="implementation_id"
                                            value="{{ $childCenterId->implementation_id }}" />


                                    </div>
                                @endif
                                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('child development worker') || auth()->user()->hasRole('encoder'))
                                    <div class="col-md-6 text-sm">
                                        <label for="is_funded">Is child funded?<b class="text-red-600">*</b></label><br>
                                        <input type="radio" class="ml-5" name="is_funded" id="is_funded_yes" value="1"
                                            {{ old('is_funded', $childCenterId->funded) == '1' ? 'checked' : '' }}>
                                        <label class="mt-2" for="is_funded_yes">Yes</label>

                                        <input type="radio" class="ml-5" name="is_funded" id="is_funded_no" value="0"
                                            {{ old('is_funded', $childCenterId->funded) == '0' ? 'checked' : '' }}>
                                        <label class="mt-2" for="is_funded_no">No</label>
                                        <div class="col-md-6 text-sm">
                                            @if ($errors->has('is_funded'))
                                                <span class="text-xs text-red-600">{{ $errors->first('is_funded') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm hidden">
                                        <label for="milk_feeding_id">Milk Feeding Implementation</label>
                                        <select
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="milk_feeding_id" name='milk_feeding_id'>
                                            @if ($milkFeeding)
                                                <option value="{{ $milkFeeding->id }}"
                                                    {{ $milkFeeding->id == old('milk_feeding_id', $childCenterId->milk_feeding_id) ? 'selected' : '' }}>
                                                    {{ $milkFeeding->name }}
                                                </option>
                                            @else
                                                <option value="" disabled selected>No active milk feeding implementation
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                @endif

                                <div class="modal fade" id="verticalycentered" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-red-600">Confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if ($cycle)
                                                    Are you sure you want to save these details?
                                                @else
                                                    No active cycle implementation.
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                @if ($cycle)
                                                    <button type="submit"
                                                        class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                                @endif
                                                <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form><!-- End floating Labels Form -->

                            <div class="col-md-12 flex mt-4 justify-end text-right">
                                <button type="button" class="text-white bg-blue-600 rounded px-3 mr-1 min-h-9"
                                    data-bs-toggle="modal" data-bs-target="#verticalycentered">Save Changes</button>
                                    <form id="cancel-form" method="GET" action="{{ route('child.index') }}">
                                    </form>

                                    <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" onclick="submitCancelForm()">
                                        Cancel
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- pantawid and pwd additional details --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function toggleAdditionalDetails(radioName, additionalDetailsId) {
                const radios = document.getElementsByName(radioName);
                const additionalDetailsElement = document.getElementById(additionalDetailsId);

                radios.forEach((radio) => {
                    radio.addEventListener("change", function() {
                        if (radio.value === "1" && radio.checked) {
                            additionalDetailsElement.disabled = false;
                        } else if (radio.value === "0" && radio.checked) {
                            additionalDetailsElement.disabled = true;
                            additionalDetailsElement.value = '';
                        }
                    });
                });
            }

            toggleAdditionalDetails("is_pantawid", "pantawid_details");
            toggleAdditionalDetails("is_person_with_disability", "person_with_disability_details");
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
            let selectedProvince = @json(old('province_psgc', $psgcRecord->province_psgc));
            let selectedCity = @json(old('city_name_psgc', $psgcRecord->city_name_psgc));
            let selectedBarangay = @json(old('brgy_psgc', $psgcRecord->brgy_psgc));

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
                populateCities(selectedProvince); // <-- This loads cities

                // Delay populateBarangays until after cities are loaded
                if (selectedCity) {
                    citySelect.value = selectedCity;
                    populateBarangays(selectedCity);

                    if (selectedBarangay) {
                        barangaySelect.value = selectedBarangay;
                    }
                }
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
