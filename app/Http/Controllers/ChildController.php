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
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('admin')) {

            $centers = ChildDevelopmentCenter::all();

            $cdcId = $request->input('center_name', null);

            $maleChildren = Child::whereHas('sex', function ($query) {
                $query->where('name', 'Male');
            })
                ->where('is_funded', true)
                ->with('nutritionalStatus')
                ->paginate(5, ['*'], 'malePage');

            $femaleChildren = Child::whereHas('sex', function ($query) {
                $query->where('name', 'Female');
            })
                ->where('is_funded', true)
                ->with('nutritionalStatus')
                ->paginate(5, ['*'], 'femalePage');

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
                    ->where('is_funded', true)
                    ->with('nutritionalStatus')
                    ->paginate(5, ['*'], 'malePage');

                $femaleChildren = Child::whereHas('sex', function ($query) {
                    $query->where('name', 'Female');
                })
                    ->whereIn('child_development_center_id', $centers->pluck('id'))
                    ->where('is_funded', true)
                    ->with('nutritionalStatus')
                    ->paginate(5, ['*'], 'femalePage');
            }
            // }

            return view('child.index', compact('maleChildren', 'femaleChildren', 'centers', 'cdcId'));

        } else {

            $assignedCdcId = optional(auth()->user()->worker)->id;

            $maleChildren = Child::where('child_development_center_id', $assignedCdcId)
                ->whereHas('sex', function ($query) {
                    $query->where('name', 'Male');
                })
                ->where('is_funded', true)
                ->with('nutritionalStatus')
                ->paginate(5, ['*'], 'malePage');

            $femaleChildren = Child::where('child_development_center_id', $assignedCdcId)
                ->whereHas('sex', function ($query) {
                    $query->where('name', 'Female');
                })->where('is_funded', true)
                ->with('nutritionalStatus')
                ->paginate(5, ['*'], 'femalePage');

            return view('child.index', compact('maleChildren', 'femaleChildren'));
        }


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
        $centers = ChildDevelopmentCenter::all();
        $sexOptions = Sex::all();

        $psgc = new Psgc();

        // Fetch all provinces for the form
        $provinces = $psgc->getProvinces();
        $cities = $psgc->allCities();      // Fetch all cities once
        $barangays = $psgc->allBarangays(); // Fetch all barangays once

        if ($request->has('province_psgc') && !empty($request->input('province_psgc'))) {
            $province_psgc = $request->input('province_psgc');
            // Fetch cities for the selected province
            $cities = $psgc->allCities()->get($province_psgc, collect([]));
        }

        // Check if city is selected
        if ($request->has('city_name_psgc') && !empty($request->input('city_name_psgc'))) {
            $city_psgc = $request->input('city_name_psgc'); 
            $barangays = $psgc->getBarangays($city_psgc);
        }

        return view('child.create', compact('cycleImplementation','centers', 'sexOptions', 'provinces', 'cities', 'barangays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChildRequest $request)
    {
        $this->authorize('create-child');
        
        $validatedData = $request->validated();

        $childExists = Child::where('firstname', $request->firstname)
            ->where('middlename', $request->middlename)
            ->where('lastname', $request->lastname)
            ->where('extension_name', $request->extension_name)
            ->where('date_of_birth', $request->date_of_birth)
            ->where('sex_id', $request->sex_id)
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

        $childDevelopmentCenter = ChildDevelopmentCenter::where('assigned_user_id', auth()->id())->first();

        if (!$childDevelopmentCenter) {
            // Handle the case where the user is not assigned to any Child Development Center
            return redirect()->back()->withErrors('You are not assigned to any Child Development Center.');
        }

        $child = Child::create([
            'cycle_implementation_id' => $request->cycle_implementation_id,
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
            'child_development_center_id' => $childDevelopmentCenter->id,
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('child.index')->with('success', 'Child details saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $this->authorize('edit-child');

        $child = Child::findOrFail($id);
        $centers = ChildDevelopmentCenter::all();

        $psgc = new Psgc();

        $psgcRecord = Psgc::find($child->psgc_id);

        $provinces = $psgc->getProvinces();
        $cities = $psgc->getCities($psgcRecord->province_psgc);
        $barangays = $psgc->getBarangays($psgcRecord->city_name_psgc);

        $getallcities = $psgc->allCities();      // Fetch all cities once
        $getallbarangays = $psgc->allBarangays();

        // dd($provinces);

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

        return view('child.edit', compact('child', 'centers', 'sexOptions', 'extNameOptions', 'pantawidDetails', 'psgcRecord', 'provinces', 'cities', 'barangays', 'getallcities', 'getallbarangays'));
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

        $child->update($validatedData);

        return redirect()->route('child.index')->with('success', 'Child record updated successfully.');
    }

    public function filterByCdc(Request $request)
    {

        if (auth()->user()->hasRole('admin')) {

            $cdcId = $request->input('center_name', null);

            $centers = ChildDevelopmentCenter::all();
            $children = Child::all();

            if ($cdcId == 'all_center') {
                $maleChildren = Child::whereHas('sex', function ($query) {
                    $query->where('name', 'Male');
                })
                    ->where('is_funded', true)
                    ->paginate(5, ['*'], 'malePage');

                $femaleChildren = Child::whereHas('sex', function ($query) {
                    $query->where('name', 'Female');
                })
                    ->where('is_funded', true)
                    ->paginate(5, ['*'], 'femalePage');
            } else {
                foreach ($children as $child) {

                    $maleChildren = Child::whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                        ->where('child_development_center_id', $cdcId)
                        ->where('is_funded', true)
                        ->paginate(5, ['*'], 'malePage');

                    $femaleChildren = Child::whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                        ->where('child_development_center_id', $cdcId)
                        ->where('is_funded', true)
                        ->paginate(5, ['*'], 'femalePage');
                }
            }

            return view('child.index', compact('maleChildren', 'femaleChildren', 'centers', 'cdcId'));

        } else {
            $cdcId = $request->input('center_name');

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

            $maleChildren = collect();
            $femaleChildren = collect();

            if ($cdcId == 'all_center') {
                foreach ($children as $child) {

                    $maleChildren = Child::whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                        ->whereIn('child_development_center_id', $centers->pluck('id'))
                        ->where('is_funded', true)
                        ->paginate(5, ['*'], 'malePage');

                    $femaleChildren = Child::whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                        ->whereIn('child_development_center_id', $centers->pluck('id'))
                        ->where('is_funded', true)
                        ->paginate(5, ['*'], 'femalePage');
                }
            } else {

                foreach ($children as $child) {

                    $maleChildren = Child::whereHas('sex', function ($query) {
                        $query->where('name', 'Male');
                    })
                        ->where('child_development_center_id', $cdcId)
                        ->where('is_funded', true)
                        ->paginate(5, ['*'], 'malePage');

                    $femaleChildren = Child::whereHas('sex', function ($query) {
                        $query->where('name', 'Female');
                    })
                        ->where('child_development_center_id', $cdcId)
                        ->where('is_funded', true)
                        ->paginate(5, ['*'], 'femalePage');
                }
            }

            return view('child.index', compact('maleChildren', 'femaleChildren', 'centers', 'cdcId'));
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Child $child)
    {
        //
    }
}
