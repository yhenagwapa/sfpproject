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
        $attendances = Attendance::where('child_id', $id)->paginate(10);

        return view('attendance.index', compact('child', 'attendances'));
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
    public function store(StoreAttendanceRequest $request, $child_id)
    {
        $withmilk = $request->input('with_milk', '0');
        $attendanceCount = Attendance::where('child_id', $child_id)->count();

        if ($attendanceCount >= 120) {
            return redirect()->back()->withErrors(['error' => 'Attendance limit of 120 reached for this child.']);
        }

        $validatedData = $request->validated();

        // Create the attendance record
        Attendance::create([
            'child_id' => $child_id,
            'feeding_no' => $attendanceCount + 1,
            'feeding_date' => $request->feeding_date,
            'with_milk' => $withmilk,
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
