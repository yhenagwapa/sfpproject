<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Http\Requests\StoreChildRequest;
use App\Http\Requests\UpdateChildRequest;
use App\Models\ChildDevelopmentCenter;
use App\Models\Sex;
use App\Models\Attendance;
use App\Http\Controllers\Log;
use App\Models\UserCenter;
use Illuminate\Http\Request;
use App\Models\Psgc;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Models\Implementation;
use App\Models\ChildCenter;

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
    public function index(Request $request, Implementation $cycle)
    {
        $cycle = Implementation::where('status', 'active')->first();

        $cdcId = $request->input('center_name', 'all_center');

        $fundedChildren = Child::with('records','nutritionalStatus', 'sex');

        $maleChildrenQuery = clone $fundedChildren;
        $femaleChildrenQuery = clone $fundedChildren;

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all()->keyBy('id');

            if ($cdcId == 'all_center') {
                $maleChildren = $maleChildrenQuery->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })->paginate(5);
            } else {
                $centerId = ChildCenter::where('child_development_center_id', $cdcId)->first();

                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($centerId) {
                        $query->where('child_development_center_id', $centerId);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($centerId) {
                        $query->where('child_development_center_id', $centerId);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->paginate(5);
            }

        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = UserCenter::where('user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($centerIds) {
                        $query->whereIn('child_development_center_id', $centerIds);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($centerIds) {
                        $query->whereIn('child_development_center_id', $centerIds);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->paginate(5);

            } else {

                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->paginate(5);
            }

        } elseif (auth()->user()->hasRole('child development worker')){
            $workerID = auth()->id();
            $centers = UserCenter::where('user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($centerIds) {
                        $query->whereIn('child_development_center_id', $centerIds);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($centerIds) {
                        $query->whereIn('child_development_center_id', $centerIds);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->paginate(5);

            } else {
                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->paginate(5);
            }
        } elseif (auth()->user()->hasRole('encoder')){
            $encoderID = auth()->id();
            $centers = UserCenter::where('user_id', $encoderID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($centerIds) {
                        $query->whereIn('child_development_center_id', $centerIds);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($centerIds) {
                        $query->whereIn('child_development_center_id', $centerIds);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->paginate(5);

            } else {
                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->paginate(5);
            }
        } elseif (auth()->user()->hasRole('pdo')){
            $pdoID = auth()->id();
            $centers = UserCenter::where('user_id', $pdoID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($centerIds) {
                        $query->whereIn('child_development_center_id', $centerIds);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($centerIds) {
                        $query->whereIn('child_development_center_id', $centerIds);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->paginate(5);

            } else {
                $maleChildren = $maleChildrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('records', function ($query) use ($cdcId) {
                        $query->where('child_development_center_id', $cdcId);
                    })
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->paginate(5);
            }
        }

        return view('child.index', compact('maleChildren', 'femaleChildren', 'centers', 'cdcId'));
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
            $centers = ChildDevelopmentCenter::where('assigned_encoder_user_id', $userID)->get();
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

        $validatedData = $request->validated();

        $implementation = Implementation::where('id', $request->implementation_id)
                ->where('status', 'active')->get();

        $child = Child::where('firstname', $request->firstname)
            ->where('middlename', $request->middlename)
            ->where('lastname', $request->lastname)
            ->where('extension_name', $request->extension_name)
            ->where('date_of_birth', $request->date_of_birth)
            ->get();

        if ($child->isNotEmpty()) {
            $childIDs = $child->pluck('id');
            $childExists = ChildCenter::whereIn('child_id', $childIDs)
                ->where('child_development_center_id', $request->child_development_center_id)
                ->where('implementation_id', $request->implementation_id)
                ->where('status', 'active')
                ->exists();

            if ($childExists) {
                return redirect()->back()->with('error', 'Child already exists.');
            }

            $childExistsInactive = ChildCenter::whereIn('child_id', $childIDs)
                ->where('child_development_center_id', $request->child_development_center_id)
                ->where('implementation_id', $request->implementation_id)
                ->whereIn('status', ['inactive', 'unfunded'])
                ->exists();

            if ($childExistsInactive) {
                ChildCenter::create([
                    'child_id' => $childIDs->first(),
                    'child_development_center_id' => $request->child_development_center_id,
                    'implementation_id' => $request->implementation_id,
                    'status' => 'active',
                ]);

                return redirect()->route('child.index')->with('success', 'Child details saved successfully.');
            }

        } else {

            $psgc = Psgc::where('province_psgc', $request->input('province_psgc'))
            ->where('city_name_psgc', $request->input('city_name_psgc'))
            ->where('brgy_psgc', $request->input('brgy_psgc'))
            ->first();

            if ($psgc) {
                $psgc_id = $psgc->psgc_id;
            } else {
                return redirect()->back()->withErrors(['msg' => 'Location not found']);
            }

            $addChild = Child::create([
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                'lastname' => $request->lastname,
                'extension_name' => $request->extension_name,
                'date_of_birth' => $request->date_of_birth,
                'sex_id' => $request->sex_id,
                'address' => $request->address,
                'psgc_id' => $psgc_id,
                'pantawid_details' => $request->pantawid_details,
                'person_with_disability_details' => $request->person_with_disability_details ? $request->person_with_disability_details : '0',
                'is_indigenous_people' => $request->is_indigenous_people,
                'is_child_of_soloparent' => $request->is_child_of_soloparent,
                'is_lactose_intolerant' => $request->is_lactose_intolerant,
                'created_by_user_id' => auth()->id(),
            ]);

            ChildCenter::create([
                'child_id' => $addChild->id,
                'child_development_center_id' => $request->child_development_center_id,
                'implementation_id' => $request->implementation_id,
                'status' => $request->implementation_id ? 'active' : 'unfunded',
            ]);

            return redirect()->route('child.index')->with('success', 'Child details saved successfully');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Implementation $cycle, $id)
    {
        $this->authorize('edit-child');

        $cycle = Implementation::where('status', 'active')->first();
        $milkFeeding = MilkFeeding::where('status', 'active')->first();

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

        return view('child.edit', compact('child', 'cycle', 'milkFeeding', 'centers', 'sexOptions', 'extNameOptions', 'pantawidDetails', 'psgcRecord', 'provinces', 'cities', 'barangays', 'provinceChange', 'cityChange', 'barangayChange'));
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
        $Implementation = Implementation::where('status', 'active')->first();

        $query = Child::where('firstname', $validatedData['firstname'])
            ->where('middlename', $validatedData['middlename'])
            ->where('lastname', $validatedData['lastname'])
            ->where('date_of_birth', $validatedData['date_of_birth'])
            ->where('cycle_implementation_id', $Implementation->id)
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

        $psgc = Psgc::where('province_psgc', $request->input('province_psgc'))
                ->where('city_name_psgc', $request->input('city_name_psgc'))
                ->where('brgy_psgc', $request->input('brgy_psgc'))
                ->first();

        if ($psgc) {
            $validatedData['psgc_id'] = $psgc->psgc_id;
        } else {
            return redirect()->back()->withErrors(['psgc' => 'Selected location is not valid.']);
        }

        $child->update($validatedData);

        return redirect()->route('child.index')->with('success', 'Child record updated successfully.');
    }

}
