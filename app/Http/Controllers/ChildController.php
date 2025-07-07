<?php

namespace App\Http\Controllers;

use App\Enums\CycleStatus;
use App\Models\Child;
use App\Http\Requests\StoreChildRequest;
use App\Http\Requests\UpdateChildRequest;
use App\Models\ChildDevelopmentCenter;
use App\Models\NutritionalStatus;
use App\Models\cgs_wfa_girls;
use App\Models\cgs_wfa_boys;
use App\Models\cgs_hfa_girls;
use App\Models\cgs_hfa_boys;
use App\Models\cgs_wfh_girls;
use App\Models\cgs_wfh_boys;
use App\Models\Sex;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;
use App\Models\UserCenter;
use Illuminate\Http\Request;
use App\Models\Psgc;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Models\Implementation;
use App\Models\ChildCenter;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class ChildController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-child', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-child', ['only' => ['edit', 'update']]);
        $this->middleware('permission:view-child', ['only' => ['index']]);
    }
    public function _index(Request $request, Implementation $cycle)
    {

        $permissionNames = auth()->user()->getAllPermissions()->pluck('name');
        //        dd($permissionNames);

        $cycle = Implementation::where('status', 'active')->first();
        $search = $request->get('search');
        $cdcId = $request->input('center_name', 'all_center');

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        $childrenQuery = clone $fundedChildren;

        $userID = auth()->id();

        if (auth()->user()->hasRole('admin')) {
            $centers = UserCenter::all();
            $centerIds = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::all()->keyBy('id');

            if ($cdcId === 'all_center') {
                if ($search) {
                    $children = $childrenQuery->whereHas('records', function ($query) use ($centerIds) {
                        $query->whereIn('child_development_center_id', $centerIds)
                            ->where('status', 'active')
                            ->groupBy('child_id');
                    })
                        ->whereHas('sex')
                        ->where('firstname', 'like', "%{$search}%")
                        ->orWhere('middlename', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orderBy('sex_id', 'asc')
                        ->paginate('10');

                }

                $children = $childrenQuery->whereHas('records', function ($query) use ($centerIds) {
                    $query->whereIn('child_development_center_id', $centerIds)
                        ->where('status', 'active')
                        ->groupBy('child_id');
                })
                    ->whereHas('sex')
                    ->orderBy('sex_id', 'asc')
                    ->paginate('10');

            } else {
                if ($search) {
                    $children = $childrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId)
                            ->where('status', 'active')
                            ->groupBy('child_id');
                    })
                        ->whereHas('sex')
                        ->where('firstname', 'like', "%{$search}%")
                        ->orWhere('middlename', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orderBy('sex_id', 'asc')
                        ->paginate(10);
                }

                $children = $childrenQuery->whereHas('records', function ($query) use ($cdcId) {
                    $query->where('child_development_center_id', $cdcId)
                        ->where('status', 'active')
                        ->groupBy('child_id');
                })
                    ->whereHas('sex')
                    ->orderBy('sex_id', 'asc')
                    ->paginate(10);
            }

        } else {
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId === 'all_center') {
                if ($search) {
                    $children = $childrenQuery->whereHas('records', function ($query) use ($centerIDs) {
                        $query->whereIn('child_development_center_id', $centerIDs)
                            ->where('status', 'active')
                            ->groupBy('child_id');
                    })
                        ->whereHas('sex')
                        ->where('firstname', 'like', "%{$search}%")
                        ->orWhere('middlename', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orderBy('sex_id', 'asc')
                        ->paginate(10);
                }

                $children = $childrenQuery->whereHas('records', function ($query) use ($centerIDs) {
                    $query->whereIn('child_development_center_id', $centerIDs)
                        ->where('status', 'active')
                        ->groupBy('child_id');
                })
                    ->whereHas('sex')
                    ->orderBy('sex_id', 'asc')
                    ->paginate(10);


            } else {
                if ($search) {
                    $children = $childrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId)
                            ->where('status', 'active')
                            ->groupBy('child_id');
                    })
                        ->whereHas('sex')
                        ->where('firstname', 'like', "%{$search}%")
                        ->orWhere('middlename', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orderBy('sex_id', 'asc')
                        ->paginate(10);
                }
                $children = $childrenQuery->whereHas('records', function ($query) use ($cdcId) {
                    $query->where('child_development_center_id', $cdcId)
                        ->where('status', 'active')
                        ->groupBy('child_id');
                })
                    ->whereHas('sex')
                    ->orderBy('sex_id', 'asc')
                    ->paginate(10);
            }
        }

        return view('child.index', compact('children', 'centerNames', 'centers', 'cdcId', 'search'));
    }

    public function index(Request $request)
    {
        $cdcId = $request->input('center_name', 'all_center');

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();

        $fundedChildren = Child::with(['records' => function ($query) use ($cycle) {
                $query->where('implementation_id', $cycle->id)
                    ->where('status', 'active');
            }, 'sex', 'records.center'])
            ->orderByRaw("CASE WHEN sex_id = 1 THEN 0 ELSE 1 END");

        $userID = auth()->id();

        $center_name = null;
        $childCount = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = UserCenter::all();
            $centerIds = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::all()->keyBy('id');

            if (!$cycle) {
                $children = null;
                return view('child.index', compact('children', 'centerNames', 'cdcId'))->with('error', 'No active implementation.');
            }

            if ($cdcId === 'all_center') {
                $children = $fundedChildren->whereHas('records', function ($query) use ($cycle) {
                        $query->where('implementation_id', $cycle->id)
                            ->where('status', 'active');
                    })
                    ->orderBy('lastname', 'asc')
                    ->get();

                $center_name = "All CDC/SNP";

            } else {
                $children = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
                    $query->where('child_development_center_id', $cdcId)
                        ->where('implementation_id', $cycle->id)
                        ->where('status', 'active');
                    })
                    ->orderBy('lastname', 'asc')
                            ->get();
                    }

                $center_name = ChildDevelopmentCenter::where('id', $cdcId)->first();

        } else {
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if (!$cycle) {
                $children = null;
                return view('child.index', compact('children','centerNames', 'cdcId'))->with('error', 'No active implementation.');
            }

            if ($cdcId === 'all_center') {
                $children = $fundedChildren->whereHas('records', function ($query) use ($centerIDs, $cycle) {
                    $query->whereIn('child_development_center_id', $centerIDs)
                        ->where('implementation_id', $cycle->id)
                        ->where('status', 'active');
                    })
                    ->orderBy('lastname', 'asc')
                    ->get();

                $center_name = "All CDC/SNP";

            } else {
                $children = $fundedChildren->whereHas('records', function ($query) use ($cdcId, $cycle) {
                    $query->where('child_development_center_id', $cdcId)
                        ->where('implementation_id', $cycle->id)
                        ->where('status', 'active');
                    })
                    ->orderBy('lastname', 'asc')
                    ->get();

                $center_name = ChildDevelopmentCenter::where('id', $cdcId)->first();

            }
        }

        $childCount = $children->count();

        return view('child.index', compact('children', 'centerNames', 'centers', 'cdcId', 'center_name', 'childCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create-child');
        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();

        if(!$cycle){
            return redirect()->back()->with('error', 'There is no active implementation.');
        }

        $userID = auth()->id();
        if (auth()->user()->hasRole('child development worker')) {
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

        } elseif (auth()->user()->hasRole('encoder')) {
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();
        }

        $minDate = Carbon::now()->subYears(6)->addDay()->format('Y-m-d');
        $maxDate = Carbon::create(null, 6, 30)->subYears(2)->format('Y-m-d');

        $sexOptions = Sex::all();

        $psgc = new Psgc();

        $provinces = $psgc->getProvinces();
        $cities = $psgc->allCities();
        $barangays = $psgc->allBarangays();

        if ($request->has('province_psgc') && !empty($request->input('province_psgc'))) {
            $province_psgc = $request->input('province_psgc');

            $cities = $psgc->allCities()->get($province_psgc, collect([]));
        }

        if ($request->has('city_name_psgc') && !empty($request->input('city_name_psgc'))) {
            $city_psgc = $request->input('city_name_psgc');
            $barangays = $psgc->getBarangays($city_psgc);
        }

        $disabilities = Child::disabilityOptions();

        // get all children regardless
//        $allChildren = Child::select(
//            'id',
//            DB::raw("CONCAT(firstname, ' ', middlename, ' ', lastname, ' ', extension_name) AS full_name"),
//        )->pluck('full_name', 'id');
//        dd($allChildren);

        return view('child.create', compact('cycle', 'centerNames', 'minDate', 'maxDate', 'sexOptions', 'provinces', 'cities', 'barangays', 'disabilities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChildRequest $request)
    {
        $this->authorize('create-child');

        $currentCycle = Implementation::where('status', 'active')->first();

        $validatedData = $request->validated();

        //        dd($validatedData);

        $child = Child::where([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'date_of_birth' => $request->date_of_birth
        ]);

        if (isset($validatedData['extension_name'])) {
            $child->where('extension_name', $validatedData['extension_name']);
        }

        $existingChild = $child->first();

        if ($existingChild) {
            return redirect()->back()->with('error', 'Child already exist.');
        }

        $psgc = Psgc::where('region_psgc', $validatedData['region_psgc'])
            ->where('province_psgc', $validatedData['province_psgc'])
            ->where('city_name_psgc', $validatedData['city_name_psgc'])
            ->where('brgy_psgc', $validatedData['brgy_psgc'])
            ->first();

        if ($psgc) {
            $psgc_id = $psgc->psgc_id;
        } else {
            return redirect()->back()->withErrors(['msg' => 'Location not found']);
        }

        $newChild = Child::create([
            'firstname' => $validatedData['firstname'],
            'middlename' => $validatedData['middlename'] ?? null,
            'lastname' => $validatedData['lastname'],
            'extension_name' => $validatedData['extension_name'] ?? null,
            'date_of_birth' => $validatedData['date_of_birth'],
            'sex_id' => $validatedData['sex_id'],
            'address' => $validatedData['address'],
            'psgc_id' => $psgc_id,
            'pantawid_details' => $validatedData['pantawid_details'] ?? null,
            'person_with_disability_details' => $validatedData['person_with_disability_details'] ?? null,
            'is_indigenous_people' => $validatedData['is_indigenous_people'] ?? false,
            'is_child_of_soloparent' => $validatedData['is_child_of_soloparent'] ?? false,
            'is_lactose_intolerant' => $validatedData['is_lactose_intolerant'] ?? false,
            'created_by_user_id' => auth()->id(),
        ]);

        $newChildRecord = ChildCenter::create([
            'child_id' => $newChild->id,
            'child_development_center_id' => $validatedData['child_development_center_id'],
            'implementation_id' => $validatedData['implementation_id'],
            'status' => 'active',
            'funded' => $validatedData['is_funded'],
        ]);

        return redirect()->route('child.index')->with('success', 'Child details saved successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        session(['editing_child_id' => $request->input('child_id')]);

        return redirect()->route('child.edit');
    }

    /**
     * Show the form for editing the specified resource.
     */
     public function view(Request $request)
    {
        session(['view_child_id' => $request->input('child_id')]);

        $childID = session('view_child_id');

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();

        $child = Child::findOrFail($childID);

        $childSex = Sex::where('id', $child->sex_id)->pluck('name')->first();

        $psgc = new Psgc();

        $psgcRecord = Psgc::where('psgc_id', $child->psgc_id)->first();

        $childRecord = ChildCenter::where('child_id', $child->id)
            ->where('status', 'active')
            ->first();

        $centerName = ChildDevelopmentCenter::where('id', $childRecord->child_development_center_id)->get();
        $cycleName = Implementation::where('id', $childRecord->implementation_id)->get();
        $childNS = NutritionalStatus::where('child_id', $child->id)->where('implementation_id', $childRecord->implementation_id)->get();

        $childCenter = $centerName->pluck('center_name')->first();
        $childCycle = $cycleName->pluck('name')->first();
        $childIsFunded = $childRecord->funded;

        return view(
            'child.view',
            compact([
                'child',
                'childSex',
                'cycle',
                'psgcRecord',
                'childCenter',
                'centerName',
                'childCycle',
                'childIsFunded',
                'childNS'
            ])
        );
    }

    public function edit(Request $request)
    {
        $this->authorize('edit-child');

        $childID = session('editing_child_id');

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();
        $milkFeeding = Implementation::where('status', 'active')->where('type', 'milk')->first();

        $child = Child::findOrFail($childID);

        $minDate = Carbon::now()->subYears(5)->startOfYear()->format('Y-m-d');
        $maxDate = Carbon::now()->subYears(2)->endOfYear()->format('Y-m-d');

        // $centers = ChildDevelopmentCenter::all();

        $userID = auth()->id();
        $centers = UserCenter::where('user_id', $userID)->get();
        $centerIDs = $centers->pluck('child_development_center_id');

        $centerNames = ChildDevelopmentCenter::all();

        if (!auth()->user()->hasRole('admin')) {
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();
        }

        $centers = ChildDevelopmentCenter::all();

        $psgc = new Psgc();

        $psgcRecord = Psgc::where('psgc_id', $child->psgc_id)->first();

        $provinces = $psgc->getProvinces();
        $cities = $psgc->allCities();
        $barangays = $psgc->allBarangays();

        if ($request->has('province_psgc') && !empty($request->input('province_psgc'))) {
            $province_psgc = $request->input('province_psgc');

            $cities = $psgc->allCities()->get($province_psgc, collect([]));
        }

        if ($request->has('city_name_psgc') && !empty($request->input('city_name_psgc'))) {
            $city_psgc = $request->input('city_name_psgc');
            $barangays = $psgc->getBarangays($city_psgc);
        }

        $sexOptions = Sex::all();

        $extNameOptions = [
            'Jr' => 'Jr',
            'Sr' => 'Sr',
            'I' => 'I',
            'II' => 'II',
            'III' => 'III',
            'IV' => 'IV',
            'V' => 'V',
            'VI' => 'VI',
            'VII' => 'VII',
            'VIII' => 'VIII',
            'IX' => 'IX',
            'X' => 'X',
        ];

        $pantawidDetails = [
            'rcct' => 'RCCT',
            'mcct' => 'MCCT'
        ];

        $isChildPantawid = false;
        $isChildPWD = false;

        if ($child->pantawid_details) {
            $isChildPantawid = true;
        } else {
            $isChildPantawid = false;
        }

        if ($child->person_with_disability_details) {
            $isChildPWD = true;
        } else {
            $isChildPWD = false;
        }

        $childCenterId = ChildCenter::where('child_id', $child->id)
            ->where('status', 'active')
            ->first();

        $centerName = ChildDevelopmentCenter::where('id', $childCenterId);
        $childCycle = $childCenterId->implementation_id;
        $childMilkFeeding = $childCenterId->milk_feeding_id;

        $disabilities = Child::disabilityOptions();

        return view(
            'child.edit',
            compact([
                'child',
                'minDate',
                'maxDate',
                'cycle',
                'milkFeeding',
                'centers',
                'sexOptions',
                'extNameOptions',
                'pantawidDetails',
                'psgcRecord',
                'provinces',
                'cities',
                'barangays',
                'isChildPantawid',
                'isChildPWD',
                'childCenterId',
                'centerName',
                'centerNames',
                'disabilities',
            ])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChildRequest $request)
    {
        $validatedData = $request->validated();

        $childID = session('editing_child_id');

        $editCounter = 0;

        $child = Child::findOrFail($childID);
        $oldDOB = $child->date_of_birth;
        $childSex = $child->sex_id;

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();



        if(!auth()->user()->hasRole('lgu focal')){
            $query = Child::where('firstname', $validatedData['firstname'])
                ->where('middlename', $validatedData['middlename'])
                ->where('lastname', $validatedData['lastname'])
                ->where('date_of_birth', $validatedData['date_of_birth'])
                ->where('id', '!=', $child->id);

            if (isset($validatedData['extension_name'])) {
                $query->where('extension_name', $validatedData['extension_name']);
            }

            $existingChild = $query->first();

            if ($existingChild) {
                return redirect()->back()->with('error', 'Child already exists.');
            }

            $psgc = Psgc::where('province_psgc', $request->province_psgc)
                ->where('city_name_psgc', $request->city_name_psgc)
                ->where('brgy_psgc', $request->brgy_psgc)
                ->first();

            if ($psgc) {
                $validatedData['psgc_id'] = $psgc->psgc_id;
            } else {
                return redirect()->back()->withErrors(['psgc' => 'Selected location is not valid.']);
            }

            $childEditCount = $child->edit_counter;

            if (!auth()->user()->hasRole('admin')) {
                $editCounter = $childEditCount + 1;
            }

            $updated = $child->update([
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                'lastname' => $request->lastname,
                'extension_name' => $request->extension_name,
                'date_of_birth' => $request->date_of_birth,
                'sex_id' => $request->sex_id,
                'address' => $request->address,
                'psgc_id' => $psgc->psgc_id,
                'pantawid_details' => $request->pantawid_details ? $request->pantawid_details : null,
                'person_with_disability_details' => $request->person_with_disability_details ? $request->person_with_disability_details : null,
                'is_indigenous_people' => $request->is_indigenous_people,
                'is_child_of_soloparent' => $request->is_child_of_soloparent,
                'is_lactose_intolerant' => $request->is_lactose_intolerant,
                'edit_counter' => $editCounter,
                'updated_by_user_id' => auth()->id(),
            ]);

            if ($request->date_of_birth != $oldDOB) {
                $nutritionalStatus = NutritionalStatus::where('child_id', $child->id)->where('implementation_id', $cycle->id)->get();

                foreach ($nutritionalStatus as $nutrition) {
                    $weighingDate = $nutrition->actual_weighing_date;

                    $age = $child->calculateAgeAt($weighingDate);
                    $nutrition->age_in_years = $age['years'];
                    $nutrition->age_in_months = $age['months'];

                    $weight = $nutrition->weight;
                    $height = $nutrition->height;
                    $weightForAge = null;
                    $heightForAge = null;
                    $weightForHeight = null;
                    $isMalnourished = false;
                    $isUndernourished = false;

                    if ($childSex == '1') {
                        $getAge = cgs_wfa_boys::where('age_month', $nutrition->age_in_months)->first();

                        if (!$getAge) {
                            return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                        }

                        if ((float) $weight <= (float) $getAge->severely_underweight) {
                            $weightForAge = 'Severely Underweight';
                        } elseif ((float) $weight >= (float) $getAge->underweight_from && (float) $weight <= (float) $getAge->underweight_to) {
                            $weightForAge = 'Underweight';
                        } elseif ((float) $weight >= (float) $getAge->normal_from && (float) $weight <= (float) $getAge->normal_to) {
                            $weightForAge = 'Normal';
                        } elseif ((float) $weight >= (float) $weight) {
                            $weightForAge = 'Overweight';
                        } else {
                            $weightForAge = 'Not Applicable';
                        }

                    } else {
                        $getAge = cgs_wfa_girls::where('age_month', $nutrition->age_in_months)->first();

                        if (!$getAge) {
                            return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                        }

                        if ((float) $weight <= (float) $getAge->severely_underweight) {
                            $weightForAge = 'Severely Underweight';
                        } elseif ((float) $weight >= (float) $getAge->underweight_from && (float) $weight <= (float) $getAge->underweight_to) {
                            $weightForAge = 'Underweight';
                        } elseif ((float) $weight >= (float) $getAge->normal_from && (float) $weight <= (float) $getAge->normal_to) {
                            $weightForAge = 'Normal';
                        } elseif ((float) $weight >= (float) $weight) {
                            $weightForAge = 'Overweight';
                        } else {
                            $weightForAge = 'Not Applicable';
                        }
                    }

                    //height for age
                    if ($childSex == '1') {
                        $getAge = cgs_hfa_boys::where('age_month', $nutrition->age_in_months)->first();

                        if (!$getAge) {
                            return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                        }

                        if ((float) $height <= (float) $getAge->severely_stunted) {
                            $heightForAge = 'Severely Stunted';
                        } elseif ((float) $height >= (float) $getAge->stunted_from && (float) $height <= (float) $getAge->stunted_to) {
                            $heightForAge = 'Stunted';
                        } elseif ((float) $height >= (float) $getAge->normal_from && (float) $height <= (float) $getAge->normal_to) {
                            $heightForAge = 'Normal';
                        } elseif ((float) $height >= (float) $getAge->tall) {
                            $heightForAge = 'Tall';
                        } else {
                            $heightForAge = 'Not Applicable';
                        }

                    } else {
                        $getAge = cgs_hfa_girls::where('age_month', $nutrition->age_in_months)->first();

                        if (!$getAge) {
                            return redirect()->back()->withErrors(['ageError' => 'Age is out of range.']);
                        }

                        if ((float) $height <= (float) $getAge->severely_stunted) {
                            $heightForAge = 'Severely Stunted';
                        } elseif ((float) $height >= (float) $getAge->stunted_from && (float) $height <= (float) $getAge->stunted_to) {
                            $heightForAge = 'Stunted';
                        } elseif ((float) $height >= (float) $getAge->normal_from && (float) $height <= (float) $getAge->normal_to) {
                            $heightForAge = 'Normal';
                        } elseif ((float) $height >= (float) $getAge->tall) {
                            $heightForAge = 'Tall';
                        } else {
                            $heightForAge = 'Not Applicable';
                        }
                    }

                    //weight for height
                    if ($childSex == '1') {
                        $getHeight = cgs_wfh_boys::where('length_from', '<=', $request->height)
                                        ->where('length_to', '>=', $request->height)
                                        ->first();

                        if (!$getHeight) {
                            return redirect()->back()->withErrors(['ageError' => 'Height is out of range.']);
                        }

                        if ((float) $weight <= (float) $getHeight->severely_wasted) {
                            $weightForHeight = 'Severely Wasted';
                        } elseif ((float) $weight >= (float) $getHeight->wasted_from && (float) $weight <= (float) $getHeight->wasted_to) {
                            $weightForHeight = 'Wasted';
                        } elseif ((float) $weight >= (float) $getHeight->normal_from && (float) $weight <= (float) $getHeight->normal_to) {
                            $weightForHeight = 'Normal';
                        } elseif ((float) $weight >= (float) $getHeight->overweight_from && (float) $weight <= $getHeight->overweight_to) {
                            $weightForHeight = 'Overweight';
                        } elseif ((float) $weight >= (float) $getHeight->obese) {
                            $weightForHeight = 'Obese';
                        } else {
                            $weightForHeight = 'Not Applicable';
                        }

                    } else {
                        $getHeight = cgs_wfh_girls::where('length_from', '<=', $request->height)
                                        ->where('length_to', '>=', $request->height)
                                        ->first();

                        if (!$getHeight) {
                            return redirect()->back()->withErrors(['ageError' => 'Height is out of range.']);
                        }

                        if ((float) $weight <= (float) $getHeight->severely_wasted) {
                            $weightForHeight = 'Severely Wasted';
                        } elseif ((float) $weight >= (float) $getHeight->wasted_from && (float) $weight <= (float) $getHeight->wasted_to) {
                            $weightForHeight = 'Wasted';
                        } elseif ((float) $weight >= (float) $getHeight->normal_from && (float) $weight <= (float) $getHeight->normal_to) {
                            $weightForHeight = 'Normal';
                        } elseif ((float) $weight >= (float) $getHeight->overweight_from && (float) (float) $weight <= $getHeight->overweight_to) {
                            $weightForHeight = 'Overweight';
                        } elseif ((float) $weight >= (float) $getHeight->obese) {
                            $weightForHeight = 'Obese';
                        } else {
                            $weightForHeight = 'Not Applicable';
                        }

                    }

                    $isMalnourished = in_array($weightForAge, ['Underweight', 'Severely Underweight', 'Overweight']) ||
                            in_array($heightForAge, ['Stunted', 'Severely Stunted']) ||
                            in_array($weightForHeight, ['Wasted', 'Severely Wasted', 'Overweight', 'Obese']);


                    $isUndernourished = in_array($weightForAge, ['Underweight', 'Severely Underweight']) ||
                            in_array($heightForAge, ['Stunted', 'Severely Stunted']) ||
                            in_array($weightForHeight, ['Wasted', 'Severely Wasted']);

                    $nutrition->age_in_months = $age['months'];
                    $nutrition->age_in_years = $age['years'];
                    $nutrition->weight_for_age = $weightForAge;
                    $nutrition->height_for_age = $heightForAge;
                    $nutrition->weight_for_height = $weightForHeight;
                    $nutrition->is_malnourish = $isMalnourished;
                    $nutrition->is_undernourish = $isUndernourished;

                    $nutrition->save();

                    if ($nutrition->save()) {
                        \Log::info('Nutrition update saved successfully');
                    } else {
                        \Log::warning('Nutrition update failed to save');
                    }

                }
            }
        }

        if(auth()->user()->hasRole('admin ')|| auth()->user()->hasRole('lgu focal') || auth()->user()->hasRole('sfp coordinator')){

            $currentChildCenter = ChildCenter::where('child_id', $child->id)
                ->where('status', 'active')->first();

            if ($request->child_development_center_id != $currentChildCenter->child_development_center_id) {
                ChildCenter::where('child_id', $child->id)->update(['status' => 'inactive']);

                ChildCenter::create([
                    'child_id' => $child->id,
                    'child_development_center_id' => $request->child_development_center_id,
                    'implementation_id' => $request->implementation_id,
                    'milk_feeding_id' => $request->implementation_id,
                    'status' => 'active',
                    'funded' => $request->is_funded,
                ]);
            }

            if($request->is_funded != $currentChildCenter->funded){
                ChildCenter::where('child_id', $child->id)->update(['funded' => $request->is_funded]);
            }
        }

        return redirect()->route('child.index')->with('success', 'Child record updated successfully.');

    }

}
