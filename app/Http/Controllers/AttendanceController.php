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
        $this->middleware('permission:add-attendance|view-attendance', ['only' => ['index','store']]);
        $this->middleware('permission:view-attendance', ['only' => ['view']]);
        $this->middleware('permission:add-attendance', ['only' => ['store']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $child = Child::findOrFail($id);
        $childAttendance = Attendance::where('child_id', $id)->get();

        return view('attendance.index', compact('child', 'childAttendance'));
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
    public function store(StoreAttendanceRequest $request, $id)
    {
        $withmilk = $request->has('with_milk') ? 1 : 0;

        Attendance::create([
            'feeding_no' => $request->feeding_no,
            'child_id' => $request->child_id,
            'feeding_date' => $request->feeding_date,
            'with_milk' => $request->with_milk,
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('attendance.index', $id)->with('success', 'Attendance recorded successfully.');
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
