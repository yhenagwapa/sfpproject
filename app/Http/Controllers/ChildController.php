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

        $fundedChildren = Child::with('records','nutritionalStatus', 'sex');

        $maleChildrenQuery = clone $fundedChildren;
        $femaleChildrenQuery = clone $fundedChildren;

        $userID = auth()->id();

        if (auth()->user()->hasRole('admin')) {
            $centers = UserCenter::all();
            $centerIds = $centers->pluck('id');
            $centerNames = ChildDevelopmentCenter::all()->keyBy('id');

            if ($cdcId == 'all_center') {
                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($centerIds) {
                    $query->whereIn('child_development_center_id', $centerIds)
                    ->where('status', 'active');
                })
                ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($centerIds) {
                    $query->whereIn('child_development_center_id', $centerIds)
                    ->where('status', 'active');
                })
                ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })->paginate(5);
            } else {
                $centerId = ChildCenter::where('child_development_center_id', $cdcId)->first();

                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($cdcId) {
                    $query->where('child_development_center_id', $cdcId)
                    ->where('status', 'active');
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId)
                        ->where('status', 'active');
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->paginate(5);
                }

        } else{
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

            if ($cdcId == 'all_center') {
                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($centerIDs) {
                        $query->whereIn('child_development_center_id', $centerIDs)
                        ->where('status', 'active');
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($centerIDs) {
                        $query->whereIn('child_development_center_id', $centerIDs)
                        ->where('status', 'active');
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->paginate(5);

            } else {
                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId)
                        ->where('status', 'active');
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId)
                        ->where('status', 'active');
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->paginate(5);
            }
        }

        return view('child.index', compact('maleChildren', 'femaleChildren', 'centerNames', 'centers', 'cdcId'));
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
        if (auth()->user()->hasRole('child development worker')){
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

        } elseif(auth()->user()->hasRole('encoder')){
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

     public function multiStepForm(Request $request)
    {


        return view('multi-step-form', compact('step'));
    }
    public function store(StoreChildRequest $request)
    {
        $this->authorize('create-child');

        $implementation = Implementation::where('id', $request->implementation_id)
            ->where('status', 'active')
            ->first();

        $step = $request->input('step', 1);
        $validatedData = $request->validated();

        if ($request->isMethod('post')) {
            if ($step == 1) {
                $child = Child::where([
                    'firstname' => $request->firstname,
                    'middlename' => $request->middlename,
                    'lastname' => $request->lastname,
                    'extension_name' => $request->extension_name,
                    'date_of_birth' => $request->date_of_birth
                ])->exists();

                if ($child) {
                    return redirect()->back()->with('error', 'Child already exists.');
                } else {
                    Session::put('step1Data', $validatedData);
                    return redirect()->back()->with('step', 2);
                }

            } elseif ($step == 2) {
                Session::put('step2Data', $validatedData);
                return redirect()->back()->with('step', 3);

            } elseif ($step == 3) {
                // Final step - process data
                $finalData = array_merge(
            Session::get('step1Data', []),
                    Session::get('step2Data', [])
                );

                // Handle new child creation if the child doesn't exist
                $psgc = Psgc::where('province_psgc', $request->input('province_psgc'))
                        ->where('city_name_psgc', $request->input('city_name_psgc'))
                        ->where('brgy_psgc', $request->input('brgy_psgc'))
                        ->first();

                if ($psgc) {
                    $psgc_id = $psgc->psgc_id;
                } else {
                    return redirect()->back()->withErrors(['msg' => 'Location not found']);
                }

                // Create a new child record
                $newChild = Child::create([
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
                    'created_by_user_id' => auth()->id(),
                ]);

                $funded = $request->implementation_id ? true : false;

                ChildCenter::create([
                        'child_id' => $newChild->id,
                        'child_development_center_id' => $request->child_development_center_id,
                        'implementation_id' => $request->implementation_id,
                        'status' => 'active',
                        'funded' => $funded
                    ]);

                // Clear session and complete
                Session::forget(['step1Data', 'step2Data']);

                return redirect()->route('child.index')->with('success', 'Child details saved successfully.');
            }
        }

    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, Implementation $cycle, $id)
    {
        $this->authorize('edit-child');

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();
        $milkFeeding = Implementation::where('status', 'active')->where('type', 'milk')->first();

        $child = Child::findOrFail($id);

        $centers = ChildDevelopmentCenter::all();

        $psgc = new Psgc();

        $psgcRecord = Psgc::where('psgc_id',$child->psgc_id)->first();

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
            'jr' => 'Jr',
            'sr' => 'Sr',
            'i' => 'I',
            'ii' => 'II',
            'iii' => 'III',
            'iv' => 'IV',
            'v' => 'V',
            'vi' => 'VI',
            'vii' => 'VII',
            'viii' => 'VIII',
            'ix' => 'IX',
            'x' => 'X',
        ];

        $pantawidDetails = [
            'rcct' => 'RCCT',
            'mcct' => 'MCCT'
        ];

        $isChildPantawid = false;
        $isChildPWD = false;

        if($child->pantawid_details) {
            $isChildPantawid = true;
        } else {
            $isChildPantawid  = false;
        }

        if($child->person_with_disability_details) {
            $isChildPWD = true;
        } else {
            $isChildPWD  = false;
        }

        $childCenterId = ChildCenter::where('child_id', $child->id)
            ->where('status', 'active')
            ->first();

        $centerName = ChildDevelopmentCenter::where('id', $childCenterId);


        return view('child.edit',
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
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     */


    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChildRequest $request, Child $child)
    {
        $validatedData = $request->validated();

        $implementation = Implementation::where('status', 'active')->first();

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
            return redirect()->back()->withErrors([
                'error' => 'A child with the same name and date of birth already exists in this cycle.',
            ])->withInput();
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

        if($request->child_development_center_id != $currentChildCenter->child_development_center_id){
            ChildCenter::where('child_id', $child->id)->update(['status' => 'inactive']);

            $funded = $request->implementation_id ? true : false;

            ChildCenter::create([
                'child_id' => $child->id,
                'child_development_center_id' => $request->child_development_center_id,
                'implementation_id' => $request->implementation_id,
                'status' => 'active',
                'funded' => $funded
            ]);
        }

        return redirect()->route('child.index')->with('success', 'Child record updated successfully.');

    }

}
