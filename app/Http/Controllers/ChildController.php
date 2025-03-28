<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Http\Requests\StoreChildRequest;
use App\Http\Requests\UpdateChildRequest;
use App\Models\ChildDevelopmentCenter;
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

class ChildController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-child', ['only' => ['create', 'store', 'additionalInfo']]);
        $this->middleware('permission:edit-child', ['only' => ['edit', 'update']]);
        $this->middleware('permission:view-child', ['only' => ['index']]);
    }
    public function index(Request $request, Implementation $cycle)
    {
        $cycle = Implementation::where('status', 'active')->first();

        $cdcId = $request->input('center_name', 'all_center');

        $fundedChildren = Child::with('records', 'nutritionalStatus', 'sex');

        $childrenQuery = clone $fundedChildren;

        $userID = auth()->id();

        if (auth()->user()->hasRole('admin')) {
            $centers = UserCenter::all();
            $centerIds = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::all()->keyBy('id');

            if ($cdcId == 'all_center') {
                $children = $childrenQuery->whereHas('records', function ($query) use ($centerIds) {
                    $query->whereIn('child_development_center_id', $centerIds)
                        ->where('status', 'active')
                        ->where('funded', 1)
                        ->groupBy('child_id');
                    })
                    ->whereHas('sex')
                    ->orderBy('sex_id', 'asc')
                    ->paginate(10);

            } else {
                $centerId = ChildCenter::where('child_development_center_id', $cdcId)->first();

                $children = $childrenQuery->whereHas('records', function ($query) use ($cdcId) {
                    $query->where('child_development_center_id', $cdcId)
                        ->where('status', 'active')
                        ->where('funded', 1)
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

            if ($cdcId == 'all_center') {
                $children = $childrenQuery->whereHas('records', function ($query) use ($centerIDs) {
                    $query->whereIn('child_development_center_id', $centerIDs)
                        ->where('status', 'active')
                        ->where('funded', 1)
                        ->groupBy('child_id');
                    })
                    ->whereHas('sex')
                    ->orderBy('sex_id', 'asc')
                    ->paginate(10);

            } else {
                $children = $childrenQuery->whereHas('records', function ($query) use ($cdcId) {
                    $query->where('child_development_center_id', $cdcId)
                        ->where('status', 'active')
                        ->where('funded', 1)
                        ->groupBy('child_id');
                    })
                    ->whereHas('sex')
                    ->orderBy('sex_id', 'asc')
                    ->paginate(10);
            }
        }

        return view('child.index', compact('children', 'centerNames', 'centers', 'cdcId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create-child');
        $cycleImplementations = Implementation::where('status', 'active')->where('type', 'regular')->get();
        $milkFeedings = Implementation::where('status', 'active')->where('type', 'milk')->get();

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

        return view('child.create', compact('cycleImplementations', 'milkFeedings', 'centerNames', 'sexOptions', 'provinces', 'cities', 'barangays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChildRequest $request)
    {
        $this->authorize('create-child');

        $currentCycle = Implementation::where('status', 'active')->first();

        $step = $request->input('step', 1);

        if ($step == 1) {
            $validatedData = $request->validated();

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
                $getChildStatus = ChildCenter::where('child_id', $existingChild->id)
                    ->where('status', 'active')
                    ->first();

                    if($getChildStatus){
                        $childStatus = $getChildStatus->status;
                        $childCycle = $getChildStatus->implementation_id;

                        if($childStatus === 'active' && $childCycle === $currentCycle){
                            return redirect()->back()->with('error', 'Child already in current .');
                        }
                    }
            }



            $step1Data = $validatedData;
            $step1Data['sex_name'] = Sex::where('id', $request->sex_id)->value('name');

            $step1Data['is_pantawid'] = $request->is_pantawid;
            $step1Data['is_person_with_disability'] = $request->is_person_with_disability;

            $step1Data['region_name'] = PSGC::where('region_psgc', $request->region_psgc ?? null)->value('region_name');
            $step1Data['province_name'] = PSGC::where('province_psgc', $request->province_psgc ?? null)->value('province_name');
            $step1Data['city_name'] = PSGC::where('city_name_psgc', $request->city_name_psgc ?? null)->value('city_name');
            $step1Data['brgy_name'] = PSGC::where('brgy_psgc', $request->brgy_psgc ?? null)->value('brgy_name');

            session()->put('step1Data', array_merge(session()->get('step1Data', []), $step1Data));
            session()->put('step', 2);

            // dd($step1Data);

            return redirect()->back();
        }

        if ($step == 2) {
            $validatedData2 = $request->validated();

            if ($request->input('action') === 'prev') {
                session()->put('step', 1);
                return redirect()->back();

            } else {
                $step2Data = $validatedData2;

                $step2Data['center_name'] = ChildDevelopmentCenter::where('id', $request->child_development_center_id)->value('center_name');
                $step2Data['implementation_name'] = Implementation::where('id', $request->implementation_id)->value('name');
                $step2Data['milk_feeding_name'] = Implementation::where('id', $request->milk_feeding_id)->value('name');

                session()->put('step2Data', array_merge(session()->get('step2Data', []), $step2Data));
                session()->put('step', 3);
            }

            return redirect()->back();
        }

        if ($step == 3) {
            if ($request->input('action') === 'prev') {
                session()->put('step', 2);
                return redirect()->back();
            }
            // Final step - process data
            $finalData = array_merge(
                session()->get('step1Data', []),
                session()->get('step2Data', [])
            );

            $psgc = Psgc::where('region_psgc', $finalData['region_psgc'])
                ->where('province_psgc', $finalData['province_psgc'])
                ->where('city_name_psgc', $finalData['city_name_psgc'])
                ->where('brgy_psgc', $finalData['brgy_psgc'])
                ->first();

            if ($psgc) {
                $psgc_id = $psgc->psgc_id;
            } else {
                return redirect()->back()->withErrors(['msg' => 'Location not found']);
            }

            $child = Child::where([
                'firstname' => $finalData['firstname'],
                'middlename' => $finalData['middlename'],
                'lastname' => $finalData['lastname'],
                'date_of_birth' => $finalData['date_of_birth']
            ]);

            if (isset($finalData['extension_name'])) {
                $child->where('extension_name', $finalData['extension_name']);
            }

            $existsInChildTable = $child->first();

            // dd($existsInChildTable);


            if ($existsInChildTable) {
                $funded = $finalData['implementation_id'] ? true : false;

                if (!empty($finalData['implementation_id']) && !empty($finalData['milk_feeding_id'])){

                    ChildCenter::create([
                        'child_id' => $existsInChildTable->id,
                        'child_development_center_id' => $finalData['child_development_center_id'],
                        'implementation_id' => $finalData['implementation_id'],
                        'status' => 'active',
                        'funded' => $funded
                    ]);

                    ChildCenter::create([
                        'child_id' => $existsInChildTable->id,
                        'child_development_center_id' => $finalData['child_development_center_id'],
                        'implementation_id' => $finalData['milk_feeding_id'],
                        'status' => 'active',
                        'funded' => $funded
                    ]);
                } elseif (!empty($finalData['implementation_id'])) {

                    ChildCenter::create([
                        'child_id' => $existsInChildTable->id,
                        'child_development_center_id' => $finalData['child_development_center_id'],
                        'implementation_id' => $finalData['implementation_id'],
                        'status' => 'active',
                        'funded' => $funded
                    ]);
                }

                session()->forget(['step', 'step1Data', 'step2Data']);

                return redirect()->route('child.index')->with('success', 'Child details saved successfully.');
            } else {
                $newChild = Child::create([
                    'firstname' => $finalData['firstname'],
                    'middlename' => $finalData['middlename'] ?? null,
                    'lastname' => $finalData['lastname'],
                    'extension_name' => $finalData['extension_name'] ?? null,
                    'date_of_birth' => $finalData['date_of_birth'],
                    'sex_id' => $finalData['sex_id'],
                    'address' => $finalData['address'],
                    'psgc_id' => $psgc_id,
                    'pantawid_details' => $finalData['pantawid_details'] ?? null,
                    'person_with_disability_details' => $finalData['person_with_disability_details'] ?? null,
                    'is_indigenous_people' => $finalData['is_indigenous_people'] ?? false,
                    'is_child_of_soloparent' => $finalData['is_child_of_soloparent'] ?? false,
                    'is_lactose_intolerant' => $finalData['is_lactose_intolerant'] ?? false,
                    'created_by_user_id' => auth()->id(),
                ]);

                $funded = $finalData['implementation_id'] ? true : false;

                if (!empty($finalData['implementation_id']) && !empty($finalData['milk_feeding_id'])){

                    ChildCenter::create([
                        'child_id' => $newChild->id,
                        'child_development_center_id' => $finalData['child_development_center_id'],
                        'implementation_id' => $finalData['implementation_id'],
                        'status' => 'active',
                        'funded' => $funded
                    ]);

                    ChildCenter::create([
                        'child_id' => $newChild->id,
                        'child_development_center_id' => $finalData['child_development_center_id'],
                        'implementation_id' => $finalData['milk_feeding_id'],
                        'status' => 'active',
                        'funded' => $funded
                    ]);
                } elseif (!empty($finalData['implementation_id'])) {

                    ChildCenter::create([
                        'child_id' => $newChild->id,
                        'child_development_center_id' => $finalData['child_development_center_id'],
                        'implementation_id' => $finalData['implementation_id'],
                        'status' => 'active',
                        'funded' => $funded
                    ]);
                }

                session()->forget(['step', 'step1Data', 'step2Data']);

                return redirect()->route('child.index')->with('success', 'Child details saved successfully.');
            }

        }

    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */


    public function edit(Request $request)
    {
        $this->authorize('edit-child');

        $childID = $request->input('child_id');

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();
        $milkFeeding = Implementation::where('status', 'active')->where('type', 'milk')->first();

        $child = Child::findOrFail($childID);

        $centers = ChildDevelopmentCenter::all();

        $psgc = new Psgc();

        $psgcRecord = Psgc::where('psgc_id', $child->psgc_id)->first();

        $provinces = $psgc->getByProvinces();
        $cities = Psgc::getCitiesByProvince($psgcRecord->province_psgc);
        $barangays = Psgc::getBarangaysByCity($psgcRecord->city_name_psgc);

        $provinceChange = $psgc->getProvinces();
        $cityChange = $psgc->allCities();
        $barangayChange = $psgc->allBarangays();

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


        return view(
            'child.edit',
            compact([
                'child',
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
                'provinceChange',
                'cityChange',
                'barangayChange',
                'isChildPantawid',
                'isChildPWD',
                'childCenterId',
                'centerName'
            ])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChildRequest $request, Child $child)
    {
        $child = Child::findOrFail($request->input('child_id'));
        $validatedData = $request->validated();

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
            'updated_by_user_id' => auth()->id(),
        ]);



        $currentChildCenter = ChildCenter::where('child_id', $child->id)
            ->where('status', 'active')->first();



        if ($request->child_development_center_id != $currentChildCenter->child_development_center_id) {
            ChildCenter::where('child_id', $child->id)->update(['status' => 'inactive']);

            $funded = $request->implementation_id ? true : false;

            ChildCenter::create([
                'child_id' => $child->id,
                'child_development_center_id' => $request->child_development_center_id,
                'implementation_id' => $request->implementation_id,
                'milk_feeding_id' => $request->implementation_id,
                'status' => 'active',
                'funded' => $funded
            ]);
        }

        return redirect()->route('child.index')->with('success', 'Child record updated successfully.');

    }

}
