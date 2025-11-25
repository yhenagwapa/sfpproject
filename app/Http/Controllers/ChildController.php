<?php

namespace App\Http\Controllers;

use App\Enums\CycleStatus;
use App\Models\Child;
use App\Http\Requests\StoreChildRequest;
use App\Http\Requests\UpdateChildRequest;
use App\Models\ChildDevelopmentCenter;
use App\Models\ChildHistory;
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
use App\Models\ChildRecord;
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

    public function index(Request $request)
    {
        $cdcId = $request->input('center_name') ?? 'all_center';
        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();

        $userID = auth()->id();

        $center_name = null;

        if (auth()->user()->hasRole('admin')) {
            $centers = UserCenter::all();
            $centerNames = ChildDevelopmentCenter::all()->keyBy('id');

            if (!$cycle) {
                $children = null;
                return view('child.index', compact('children', 'centerNames', 'cdcId'))
                    ->with('error', 'No active implementation.');
            }

            // Handle center selection for admin
            if ($cdcId === 'all_center' || !$cdcId) {
                // Create a pseudo-object for "All CDC/SNP"
                $center_name = (object) [
                    'id' => 'all_center',
                    'center_name' => 'All CDC/SNP'
                ];
            } else {
                $center_name = ChildDevelopmentCenter::find($cdcId);
            }

        } else {
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');
            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get()->keyBy('id');

            // Handle center selection for non-admin
            if ($cdcId === 'all_center' || !$cdcId) {
                // Create a pseudo-object for "All CDC/SNP"
                $center_name = (object) [
                    'id' => 'all_center',
                    'center_name' => 'All CDC/SNP'
                ];
            } else {
                // Verify user has access to this center
                if ($centerIDs->contains($cdcId)) {
                    $center_name = ChildDevelopmentCenter::find($cdcId);
                } else {
                    // User doesn't have access to this center
                    return view('child.index', compact('centerNames', 'centers', 'cdcId'))
                        ->with('error', 'You do not have access to this center.');
                }
            }
        }

        return view('child.index', compact('centerNames', 'centers', 'cdcId', 'center_name'));
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

        $minDate = Carbon::now()->subYears(6)->addDay()->format('m-d-Y');
        $maxDate = Carbon::create(null, 6, 30)->subYears(2)->format('m-d-Y');

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

        $validatedDate = Carbon::createFromFormat('m-d-Y', $validatedData['date_of_birth'])->format('Y-m-d');

        $child = Child::where([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'date_of_birth' => $validatedDate
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
            'date_of_birth' => Carbon::createFromFormat('m-d-Y', $validatedData['date_of_birth'])->format('Y-m-d'),
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

        $newChildRecord = ChildRecord::create([
            'child_id' => $newChild->id,
            'child_development_center_id' => $validatedData['child_development_center_id'],
            'implementation_id' => $validatedData['implementation_id'],
            'action_type' => 'active',
            'action_date' => now(),
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
        session(['child_status' => $request->input('child_status')]);

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

        $childRecord = ChildRecord::where('child_id', $child->id)
            ->where('implementation_id', $cycle->id)
            ->get();

        //     dd($childRecord);

        // $childCurrentCenter = $childRecord->centerTo ?? $childRecord->centerFrom;

        // if($childRecord->action_type == 'transferred'){
        //     $note = 'Child is a transferee from ' . $childRecord->centerFrom->center_name . '.'; // not working here last move
        // } elseif ($childRecord->action_type == 'dropped'){
        //     $note = 'Child was dropped last ' . $childRecord->action_date . '.';
        // } else{
        //     $note = null;
        // }


        // $childCenter = $childCurrentCenter?->center_name;
        // $cycleName = Implementation::where('id', $childRecord->implementation_id)->get();
        // $childNS = NutritionalStatus::where('child_id', $child->id)->where('implementation_id', $childRecord->implementation_id)->get();

        // $childCycle = $cycleName->pluck('name')->first();

        return view(
            'child.view',
            compact([
                'child',
                'childSex',
                'cycle',
                'psgcRecord',
                'childRecord',
            ])
        );
    }

    public function edit(Request $request)
    {
        $this->authorize('edit-child');

        $childID = session('editing_child_id');
        $childStatus = session('child_status');

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();
        $milkFeeding = Implementation::where('status', 'active')->where('type', 'milk')->first();

        $child = Child::findOrFail($childID);

        $minDate = Carbon::now()->subYears(5)->startOfYear()->format('m-d-Y');
        $maxDate = Carbon::now()->subYears(2)->endOfYear()->format('m-d-Y');

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

        $childRecord = ChildRecord::where('child_id', $childID)
            ->when($childStatus, function ($query) use ($childStatus) {
                return $query->where('action_type', $childStatus);
            })
            ->first();

        $childCurrentCenter = $childRecord->centerTo ?? $childRecord->centerFrom;

        $childCenter = $childCurrentCenter?->center_name;
        $cycleName = Implementation::where('id', $childRecord->implementation_id)->get();

        $centerName = ChildDevelopmentCenter::find($childRecord->id);
        $childCycle = $childRecord->implementation_id;

        $disabilities = Child::disabilityOptions();

        return view(
            'child.edit',
            compact([
                'child',
                'minDate',
                'maxDate',
                'cycle',
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
                'childRecord',
                'childCurrentCenter',
                'childCycle',
                'childStatus',
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
        $childStatus = $request->child_status;

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->first();

        if(!auth()->user()->hasAnyRole(['lgu focal', 'sfp coordinator'])){

            $validatedDate = Carbon::createFromFormat('m-d-Y', $validatedData['date_of_birth'])->format('Y-m-d');

            $query = Child::where('firstname', $validatedData['firstname'])
                ->where('middlename', $validatedData['middlename'])
                ->where('lastname', $validatedData['lastname'])
                ->where('date_of_birth', $validatedDate)
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
                'date_of_birth' => $validatedDate,
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
                        $getHeight = cgs_wfh_boys::where('length_from', '<=', $height)
                            ->where('length_to', '>=', $height)
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
                        $getHeight = cgs_wfh_girls::where('length_from', '<=', $height)
                            ->where('length_to', '>=', $height)
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

        // update cdc
        if(!auth()->user()->hasAnyRole(['child development worker', 'encoder'])){ // allow admin and focal
            ChildRecord::where('child_id', $child->id)->update(['child_development_center_id' => $request->child_development_center_id]);
        }

        $currentChildRecord = ChildRecord::where('child_id', $childID)
            ->when($childStatus, function ($query) use ($childStatus) {
                return $query->where('action_type', $childStatus);
            })
            ->first();

        // update funded, if any
        if($request->is_funded != $currentChildRecord->funded){
            ChildRecord::where('child_id', $child->id)->update(['funded' => $request->is_funded]);
        }

        return redirect()->route('child.index')->with('success', 'Child record updated successfully.');

    }

}
