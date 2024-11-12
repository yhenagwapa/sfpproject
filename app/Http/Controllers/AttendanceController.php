<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Child;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:add-attendance|view-attendance', ['only' => ['index', 'store']]);
        $this->middleware('permission:view-attendance', ['only' => ['view']]);
        $this->middleware('permission:add-attendance', ['only' => ['store']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $child = Child::findOrFail($id);
        $cycleAttendances = Attendance::where('child_id', $id)
            ->where('attendance_type', 'cycle')
            ->simplePaginate(10, ['*'], 'cyclePage');


        $milkAttendances = Attendance::where('child_id', $id)
            ->where('attendance_type', 'milk')
            ->simplePaginate(10, ['*'], 'milkPage');

        $withMilk = $child->milk_feeding_id;

        return view('attendance.index', compact('child', 'cycleAttendances', 'milkAttendances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(StoreAttendanceRequest $request, $id)
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeCycleAttendance(StoreAttendanceRequest $request, $child_id)
    {
        $cycleCount = Attendance::where('child_id', $child_id)
            ->where('attendance_type','cycle')->count();

        if ($cycleCount >= 120) {
            return redirect()->back()->withErrors(['error' => 'This child already has 120 attendances.']);
        }

        $validatedData = $request->validated();

        

        $attendance = Attendance::create([
            'attendance_no' => $cycleCount + 1, 
            'child_id' => $child_id,
            'attendance_date' => $request->attendance_date,
            'attendance_type' => 'cycle',
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Attendance added successfully.');
    }
    public function storeMilkAttendance(StoreAttendanceRequest $request, $child_id)
    {
        $milkCount = Attendance::where('child_id', $child_id)
            ->where('attendance_type','milk')->count();

        

        if ($milkCount >= 120) {
            return redirect()->back()->withErrors(['error' => 'This child already has 120 attendances.']);
        }

        $validatedData = $request->validated();

        $milkAttendance =Attendance::create([
            'attendance_no' => $milkCount + 1,
            'child_id' => $child_id,
            'attendance_date' => $request->milk_attendance_date,
            'attendance_type' => 'milk',
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Attendance added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
