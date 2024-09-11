<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Http\Requests\StoreChildRequest;
use App\Http\Requests\UpdateChildRequest;
use App\Models\ChildDevelopmentCenter;
use App\Models\Attendance;
use App\Http\Controllers\Log;
use Illuminate\Http\Request;
use App\Models\Psgc;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ChildController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-child|edit-child|delete-child', ['only' => ['index','show']]);
        $this->middleware('permission:create-child', ['only' => ['create','store']]);
        $this->middleware('permission:edit-child', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-child', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $search = $request->input('search.value', '');

        if ($request->ajax()) {
            $query = Child::query();

            if ($search) {
                $query->where('firstname', 'like', '%' . $search . '%')
                      ->orWhere('middlename', 'like', '%' . $search . '%')
                      ->orWhere('lastname', 'like', '%' . $search . '%');
            }

            $children = $query->paginate(5);
        }

        return view('child.index', compact('search'));
    }


    public function search(Request $request)
    {
        $search = $request->search;
        // Use when for cleaner query building
        $children = Child::when($search, function ($query, $search) {
            $query->where('firstname', 'like', "%{$search}%")
                ->orWhere('middlename', 'like', "%{$search}%")
                ->orWhere('lastname', 'like', "%{$search}%");
        })->paginate(5)->appends(['search' => $search]); // Ensure to append the correct search query

        $childrenWithFullNames = $children->map(function ($child) {
            return [
                'id' => $child->id,
                'full_name' => $child->full_name, // Ensure full_name is defined on the Child model
                'sex' => $child->sex,
                'date_of_birth' => $child->date_of_birth,
            ];
        });

        return response()->json([
            'children' => $childrenWithFullNames,
            'pagination_links' => (string) $children->links(), // Convert pagination links to a string
        ]);
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create-child');

        $centers = ChildDevelopmentCenter::all();

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

        return view('child.create', compact('centers', 'provinces', 'cities', 'barangays'));
    }

    // public function getProvinces($region_psgc)
    // {
    //     $provinces = Psgc::where('region_psgc', $region_psgc)
    //                     ->distinct()
    //                     ->orderBy("province_name")
    //                     ->pluck('province_name', 'province_psgc');

    //     return response()->json($provinces);

    // }

    // public function getCities($province_psgc)
    // {
    //     $cities = Psgc::where('province_psgc', $province_psgc)
    //                 ->distinct()
    //                 ->orderBy('city_name')
    //                 ->pluck('city_name', 'city_name_psgc');
            
    //     return response()->json($cities);
    // }

    // public function getBarangays($city_psgc)
    // {
    //     $barangays = Psgc::where('city_name_psgc', $city_psgc)
    //                     ->distinct()
    //                     ->orderBy('brgy_name')
    //                     ->pluck('brgy_name', 'brgy_psgc');
        
    //     return response()->json($barangays);
    // }

    // public function getPsgcId($region_psgc, $province_psgc, $city_name_psgc, $brgy_psgc)
    // {
    //     try {
    //         $psgc = Psgc::where('region_psgc', $region_psgc)
    //                     ->where('province_psgc', $province_psgc)
    //                     ->where('city_name_psgc', $city_name_psgc)
    //                     ->where('brgy_psgc', $brgy_psgc)
    //                     ->pluck('psgc_id');
    
    //         return response()->json($psgc);
    //     } catch (\Exception $e) {
    //         // Log the error for debugging
    //         \Log::error('Error fetching PSGC ID: ' . $e->getMessage());
    
    //         // Return a JSON response with an error message
    //         return response()->json(['error' => 'Unable to fetch PSGC ID'], 500);
    //     }
    // }

    // In your controller
    // public function getLocationData(Request $request)
    // {
    //     $regionPsgc = $request->input('region_psgc');
    //     $provincePsgc = $request->input('province_psgc');
    //     $cityPsgc = $request->input('city_name_psgc');
    //     // Get distinct provinces
    //     $provinces = DB::table('psgcs')
    //     ->where('region_psgc', $regionPsgc)
    //     ->distinct()
    //     ->pluck('province_psgc', 'province_name');

    //     // Get distinct cities
    //     $cities = DB::table('psgcs')
    //         ->where('province_psgc', $provincePsgc)
    //         ->distinct()
    //         ->pluck('city_name_psgc', 'city_name');

    //     // Get distinct barangays
    //     $barangays = DB::table('psgcs')
    //         ->where('city_name_psgc', $cityPsgc)
    //         ->distinct()
    //         ->pluck('brgy_psgc', 'brgy_name');

    //         dd($provinces, $cities, $barangays);

    //     return view('child.create', compact('provinces', 'cities', 'barangays'));
    // }



    // public function getLocationData($psgc_id)
    // {
    //     $location = Psgc::where('psgc_id', $psgc_id)->first();

    //     if ($location) {
    //         return response()->json([
    //             'province_psgc' => $location->province_psgc,
    //             'province_name' => $location->province_name,
    //             'city_psgc' => $location->city_name_psgc,
    //             'city_name' => $location->city_name,
    //             'barangay_psgc' => $location->brgy_psgc,
    //             'barangay_name' => $location->brgy_name,
    //         ]);
    //     } else {
    //         return response()->json(['error' => 'Location not found'], 404);
    //     }
    // }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChildRequest $request)
    {
        $this->authorize('create-child');

        $validatedData = $request->validated();

        // Check if the child already exists
        $childExists = Child::where('firstname', $request->firstname)
                            ->where('middlename', $request->middlename)
                            ->where('lastname', $request->lastname)
                            ->where('extension_name', $request->extension_name)
                            ->where('date_of_birth', $request->date_of_birth)
                            ->where('sex', $request->sex)
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
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'extension_name' => $request->extension_name,
            'date_of_birth' => $request->date_of_birth,
            'sex' => $request->sex,
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
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('child.index')->with('success', 'Child details saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $child = Child::findOrFail($id);
        
        // Fetch the province, city, and barangay based on psgc_id
        $psgc = Psgc::find($child->psgc_id);
        
        // Check if the PSGC record exists
        if ($psgc) {
            $selectedProvincePsgc = $psgc->province_psgc;
            $selectedCityPsgc = $psgc->city_name_psgc; // Adjust field names as necessary
            $selectedBrgyPsgc = $psgc->brgy_psgc; // Adjust field names as necessary
        } else {
            $selectedProvincePsgc = null;
            $selectedCityPsgc = null;
            $selectedBrgyPsgc = null;
        }
    
        $childAttendance = Attendance::where('child_id', $id)->get();
        $centers = ChildDevelopmentCenter::all();
        $sexOptions = [
            'male' => 'Male',
            'female' => 'Female',
        ];
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
    
        if (request()->ajax()) {
            return response()->json([
                'child' => $child,
                'childAttendance' => $childAttendance,
                'centers' => $centers,
                'sexOptions' => $sexOptions,
                'pantawidDetails' => $pantawidDetails,
            ]);
        }
    
        return view('child.edit', compact('child', 'childAttendance', 'centers', 'sexOptions', 'extNameOptions', 'pantawidDetails', 'selectedProvincePsgc', 'selectedCityPsgc', 'selectedBrgyPsgc'));
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
        $validatedData = $request->validated();$request->validated();

        // Check if the child already exists
        
        $child->update($request->validated());

        // Redirect to the index page with a success message
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
