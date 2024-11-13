<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Http\Requests\StoreChildRequest;
use App\Http\Requests\UpdateChildRequest;
use App\Models\ChildDevelopmentCenter;
use App\Models\Sex;
use App\Models\Attendance;
use App\Http\Controllers\Log;
use Illuminate\Http\Request;
use App\Models\Psgc;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Models\CycleImplementation;
use App\Models\MilkFeeding;

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
        $this->middleware('permission:delete-child', ['only' => ['destroy']]);
        $this->middleware('permission:search-child', ['only' => ['search']]);
    }
    public function index(Request $request, CycleImplementation $cycle)
    {
        $cycle = CycleImplementation::where('cycle_status', 'active')->first();

        $cdcId = $request->input('center_name', 'all_center');

        $fundedChildren = Child::with('nutritionalStatus', 'sex')
            ->where('is_funded', true)
            ->where('cycle_implementation_id', $cycle->id);

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
                $maleChildren = $maleChildrenQuery->whereHas('sex', function ($query) {
                    $query->where('name', 'Male');
                })
                ->where('child_development_center_id', $cdcId)
                ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->where('child_development_center_id', $cdcId)
                    ->paginate(5);
            }
            
        } elseif (auth()->user()->hasRole('lgu focal')) {
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_focal_user_id', $focalID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $maleChildren = $maleChildrenQuery->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->whereIn('child_development_center_id', $centerIds)
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->whereIn('child_development_center_id', $centerIds)
                    ->paginate(5);

            } else {
                $maleChildren = $maleChildrenQuery->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->where('child_development_center_id', $cdcId)
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->where('child_development_center_id', $cdcId)
                    ->paginate(5);
            }

        } else {

            $workerID = auth()->id();
            $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();
            $centerIds = $centers->pluck('id');

            if ($cdcId == 'all_center') {
                $maleChildren = $maleChildrenQuery->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->whereIn('child_development_center_id', $centerIds)
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->whereIn('child_development_center_id', $centerIds)
                    ->paginate(5);

            } else {
                $maleChildren = $maleChildrenQuery->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->where('child_development_center_id', $cdcId)
                    ->paginate(5);

                $femaleChildren = $femaleChildrenQuery->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                    ->where('child_development_center_id', $cdcId)
                    ->paginate(5);
            }
        }
        
        return view('child.index', compact('maleChildren', 'femaleChildren', 'centers', 'cdcId'));
    }
    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search) {

            $keywords = explode(" ", $search);

            if (auth()->user()->hasRole('admin')) {

                $centers = ChildDevelopmentCenter::all();

                $cdcId = $request->input('center_name', null);

                $maleChildren = Child::whereHas('sex', function ($query) {
                    $query->where('name', 'Male');
                })
                    ->where('is_funded', true)
                    ->where(function ($query) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $query->where('full_name', 'LIKE', "%{$keyword}%");
                        }
                    })
                    ->paginate(5, ['*'], 'malePage');

                $femaleChildren = Child::whereHas('sex', function ($query) {
                    $query->where('name', 'Female');
                })
                    ->where('is_funded', true)
                    ->where(function ($query) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $query->where('full_name', 'LIKE', "%{$keyword}%");
                        }
                    })
                    ->paginate(5, ['*'], 'femalePage');

                if ($request->ajax()) {

                    $maleChildrentable = view('child.partials.malechild-table', compact('maleChildren'))->render();
                    $femaleChildrentable = view('child.partials.femalechild-table', compact('femaleChildren'))->render();

                    return response()->json([
                        'maleChildrenTable' => $maleChildrentable,
                        'femaleChildrenTable' => $femaleChildrentable,
                    ]);
                }

                return view('child.index', compact('maleChildren', 'femaleChildren', 'centers', 'cdcId'));

            } elseif (auth()->user()->hasRole('lgu focal')) {

                $userCityPsgc = auth()->user()->city_name_psgc;

                $matchingPsgcIds = Psgc::where('city_name_psgc', $userCityPsgc)
                    ->pluck('psgc_id');

                $children = Child::whereHas('center', function ($query) use ($matchingPsgcIds) {
                    $query->whereIn('psgc_id', $matchingPsgcIds);
                })
                    ->with('center.psgc')
                    ->get();

                $centers = ChildDevelopmentCenter::whereIn('psgc_id', $matchingPsgcIds)->get();

                $cdcId = $request->input('center_name', null);

                // dd($children);

                // foreach($centers as $center){

                foreach ($children as $child) {

                    $maleChildren = Child::whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                        ->whereIn('child_development_center_id', $centers->pluck('id'))
                        ->where(function ($query) use ($keywords) {
                            foreach ($keywords as $keyword) {
                                $query->where('full_name', 'LIKE', "%{$keyword}%");
                            }
                        })
                        ->where('is_funded', true)
                        ->paginate(5, ['*'], 'malePage');

                    $femaleChildren = Child::whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                        ->whereIn('child_development_center_id', $centers->pluck('id'))
                        ->where(function ($query) use ($keywords) {
                            foreach ($keywords as $keyword) {
                                $query->where('full_name', 'LIKE', "%{$keyword}%");
                            }
                        })
                        ->where('is_funded', true)
                        ->paginate(5, ['*'], 'femalePage');
                }
                // }

                if ($request->ajax()) {

                    $maleChildrentable = view('child.partials.malechild-table', compact('maleChildren'))->render();
                    $femaleChildrentable = view('child.partials.femalechild-table', compact('femaleChildren'))->render();

                    return response()->json([
                        'maleChildrenTable' => $maleChildrentable,
                        'femaleChildrenTable' => $femaleChildrentable,
                    ]);
                }

                return view('child.index', compact('maleChildren', 'femaleChildren', 'centers', 'cdcId'));

            } else {

                $assignedCdcId = auth()->user()->childDevelopmentCenter->id;

                $maleChildren = Child::where('child_development_center_id', $assignedCdcId)
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                    ->where('is_funded', true)
                    ->where(function ($query) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $query->where('full_name', 'LIKE', "%{$keyword}%");
                        }
                    })
                    ->paginate(5, ['*'], 'malePage');

                $femaleChildren = Child::where('child_development_center_id', $assignedCdcId)
                    ->whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })->where('is_funded', true)
                    ->where(function ($query) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $query->where('full_name', 'LIKE', "%{$keyword}%");
                        }
                    })
                    ->paginate(5, ['*'], 'femalePage');

                if ($request->ajax()) {

                    $maleChildrentable = view('child.partials.malechild-table', compact('maleChildren'))->render();
                    $femaleChildrentable = view('child.partials.femalechild-table', compact('femaleChildren'))->render();

                    return response()->json([
                        'maleChildrenTable' => $maleChildrentable,
                        'femaleChildrenTable' => $femaleChildrentable,
                    ]);
                }

                return view('child.index', compact('maleChildren', 'femaleChildren'));
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create-child');
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();
        $milkFeeding = MilkFeeding::where('status', 'active')->first();
        
        $workerID = auth()->id();
        $centers = ChildDevelopmentCenter::where('assigned_worker_user_id', $workerID)->get();

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

        return view('child.create', compact('cycleImplementation', 'milkFeeding', 'centers', 'sexOptions', 'provinces', 'cities', 'barangays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChildRequest $request)
    {
        $this->authorize('create-child');
        
        $validatedData = $request->validated();

        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        $childExists = Child::where('firstname', $request->firstname)
            ->where('middlename', $request->middlename)
            ->where('lastname', $request->lastname)
            ->where('extension_name', $request->extension_name)
            ->where('date_of_birth', $request->date_of_birth)
            ->where('cycle_implementation_id', $cycleImplementation->id)
            ->exists();

        if ($childExists) {
            return redirect()->back()->with('error', 'Child already exists.');
        }

        $psgc = Psgc::where('province_psgc', $request->input('province_psgc'))
            ->where('city_name_psgc', $request->input('city_name_psgc'))
            ->where('brgy_psgc', $request->input('brgy_psgc'))
            ->first();

        if ($psgc) {
            $psgc_id = $psgc->psgc_id;
        } else {
            return redirect()->back()->withErrors(['msg' => 'Location not found']);
        }

        $child = Child::create([
            'cycle_implementation_id' => $request->cycle_implementation_id,
            'milk_feeding_id' => $request->milk_feeding_id,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'extension_name' => $request->extension_name,
            'date_of_birth' => $request->date_of_birth,
            'sex_id' => $request->sex_id,
            'address' => $request->address,
            'psgc_id' => $psgc_id,
            'zip_code' => $request->zip_code,
            'is_pantawid' => $request->is_pantawid,
            'pantawid_details' => $request->pantawid_details,
            'is_person_with_disability' => $request->is_person_with_disability,
            'person_with_disability_details' => $request->person_with_disability_details,
            'is_indigenous_people' => $request->is_indigenous_people,
            'is_child_of_soloparent' => $request->is_child_of_soloparent,
            'is_lactose_intolerant' => $request->is_lactose_intolerant,
            'deworming_date' => $request->deworming_date,
            'vitamin_a_date' => $request->vitamin_a_date,
            'is_funded' => $request->is_funded,
            'child_development_center_id' => $request->child_development_center_id,
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('child.index')->with('success', 'Child details saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, CycleImplementation $cycle, $id)
    {
        $this->authorize('edit-child');

        $cycle = CycleImplementation::where('cycle_status', 'active')->first();
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
        $cycleImplementation = CycleImplementation::where('cycle_status', 'active')->first();

        $query = Child::where('firstname', $validatedData['firstname'])
            ->where('middlename', $validatedData['middlename'])
            ->where('lastname', $validatedData['lastname'])
            ->where('date_of_birth', $validatedData['date_of_birth'])
            ->where('cycle_implementation_id', $cycleImplementation->id)
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


    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Child $child)
    {
        //
    }
}
