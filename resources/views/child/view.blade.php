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

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-md-12 flex text-right">
                            <a href={{ route('child.index') }} class="flex italic" style="text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1e9730" class="mr-1 mt-1 size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                </svg>
                                <span class="text-green-600">
                                    Back
                                </span>
                            </a>
                        </div>

                        <h5 class='card-title uppercase'>{{ $child->full_name }}</h5>
                        <div class="row">
                            <div class='col-md-2 mt-2 text-gray-400 text-xs'>Personal Information</div>
                            <div class='col-md-10 text-gray-400'>
                                <hr>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="firstname">First Name:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='firstname'
                                    value="{{ $child->firstname }}" readonly>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="middlename">Middle Name:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='middlename'
                                    value="{{ $child->middlename }}" readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="lastname">Last Name:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='lastname'
                                    value="{{ $child->lastname }}" readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="extension_name">Extension Name:</label>
                                <input type="text" class="form-control rounded border-gray-300" id="extension_name"
                                    name="extension_name" value="{{ $child->extension_name }}" readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="date_of_birth">Date of birth:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='date_of_birth'
                                    value="{{ $child->date_of_birth->format('Y-m-d') }}" readonly>
                            </div>
                            <div class="col-md-6 mt-1 text-sm">
                                <label for="sex">Sex:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='sex_name'
                                    value="{{ $childSex }}" readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="pantawid_details">Pantawid Member:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='pantawid_details'
                                    value="{{ $child->pantawid_details ? $child->pantawid_details : 'NO' }}" readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="person_with_disability_details">Person with Disability:</label>
                                <input type="text" class="form-control rounded border-gray-300"
                                    name='person_with_disability_details'
                                    value="{{ $child->person_with_disability_details ? $child->person_with_disability_details : 'NO' }}" readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="is_indigenous_people">Indigenous People:</label>
                                <input type="text" class="form-control rounded border-gray-300"
                                    name='is_indigenous_people' value="{{ $child->is_indigenous_people ? 'Yes' : 'No' }}"
                                    readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="is_child_of_soloparent">Child of Solo Parent:</label>
                                <input type="text" class="form-control rounded border-gray-300"
                                    name='is_child_of_soloparent'
                                    value="{{ $child->is_child_of_soloparent ? 'Yes' : 'No' }}" readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="is_lactose_intolerant">Lactose Intolerant:</label>
                                <input type="text" class="form-control rounded border-gray-300"
                                    name='is_lactose_intolerant' value="{{ $child->is_lactose_intolerant ? 'Yes' : 'No' }}"
                                    readonly>
                            </div>
                            <div class="col-md-6 mt-1 text-sm"></div>

                            <div class='col-md-1 mt-4 flex text-gray-400 text-sm'>Address</div>
                            <div class='col-md-11 mt-3 text-gray-400'>
                                <hr>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="region_psgc">Region:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='region_psgc'
                                    value="{{ $psgcRecord->region_name }}" readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="province_psgc">Province:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='province_psgc'
                                    value="{{ $psgcRecord->province_name }}" readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="city_name_psgc">City/Municpality:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='city_name_psgc'
                                    value="{{ $psgcRecord->city_name }}" readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="brgy_psgc">Barangay:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='brgy_psgc'
                                    value="{{ $psgcRecord->brgy_name }}" readonly>
                            </div>

                            <div class="col-md-12 text-sm">
                                <label for="address">Address:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='address'
                                    value="{{ $child->address }}" readonly>
                            </div>

                            <div class='col-md-3 mt-4 text-gray-400 text-sm'>CDC/SNP and Implementation</div>
                            <div class='col-md-9 mt-3 text-gray-400'>
                                <hr>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="child_development_center_id">CDC/SNP:</label>
                                <input type="text" class="form-control rounded border-gray-300"
                                    name='child_development_center_id' value="{{ $childCenter }}" readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="implementation_id">Cycle Implementation:</label>
                                <input type="text" class="form-control rounded border-gray-300"
                                    name='implementation_id' value="{{ $childCycle }}" readonly>
                            </div>

                            <div class="col-md-6 mt-1 text-sm">
                                <label for="is_funded">Is child funded?</label>
                                <input type="text" class="form-control rounded border-gray-300" name='is_funded'
                                    value="{{ $childIsFunded ? 'Yes' : 'No' }}" readonly>
                            </div>
                            <div class="col-md-6 mt-1 text-sm"></div>

                            {{-- <div class='col-md-3 mt-4 text-gray-400 text-sm'>Nutritional Status Upon Entry</div>
                            <div class='col-md-9 mt-3 text-gray-400'>
                                <hr>
                            </div>

                            <div class="col-md-4 mt-1 text-sm">
                                <label for="wfa">Weight for Age:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='wfa'
                                    value="{{ $childNS->first()?->weight_for_age }}" readonly>
                            </div>
                            <div class="col-md-4 mt-1 text-sm">
                                <label for="hfa">Height for Age:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='hfa'
                                    value="{{ $childNS->first()?->height_for_age }}" readonly>
                            </div>
                            <div class="col-md-4 mt-1 text-sm">
                                <label for="wfh">Weight for Age:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='wfh'
                                    value="{{ $childNS->first()?->weight_for_height }}" readonly>
                            </div>

                            <div class='col-md-3 mt-4 text-gray-400 text-sm'>Nutritional Status After 120 Feedings</div>
                            <div class='col-md-9 mt-3 text-gray-400'>
                                <hr>
                            </div>

                            <div class="col-md-4 mt-1 text-sm">
                                <label for="wfa">Weight for Age:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='wfa'
                                    value="{{ $childNS->get(1)?->weight_for_age }}" readonly>
                            </div>
                            <div class="col-md-4 mt-1 text-sm">
                                <label for="hfa">Height for Age:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='hfa'
                                    value="{{ $childNS->get(1)?->height_for_age }}" readonly>
                            </div>
                            <div class="col-md-4 mt-1 text-sm">
                                <label for="wfh">Weight for Age:</label>
                                <input type="text" class="form-control rounded border-gray-300" name='wfh'
                                    value="{{ $childNS->get(1)?->weight_for_height }}" readonly>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
