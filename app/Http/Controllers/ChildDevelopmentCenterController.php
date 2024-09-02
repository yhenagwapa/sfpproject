<?php

namespace App\Http\Controllers;

use App\Models\ChildDevelopmentCenter;
use App\Http\Requests\StoreChildDevelopmentCenterRequest;
use App\Http\Requests\UpdateChildDevelopmentCenterRequest;
use App\Http\Controllers\ChildController;
class ChildDevelopmentCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $centers = ChildDevelopmentCenter::all();
        
        // Pass the centers to the view
        return view('centers.index', compact('centers'));
    }

    public function passToChildCreate()
    {
        $centers = ChildDevelopmentCenter::all();
        
        // Pass the centers to the view
        return view('child.index', compact('centers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('centers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChildDevelopmentCenterRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $centers = ChildDevelopmentCenter::find($id);
        
        return view('child.edit', compact('centers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChildDevelopmentCenter $childDevelopmentCenter)
    {
        $centers = ChildDevelopmentCenter::all();
        
        // Return the edit view with the child details and centers
        return view('child.edit', compact('centers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChildDevelopmentCenterRequest $request, ChildDevelopmentCenter $childDevelopmentCenter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChildDevelopmentCenter $childDevelopmentCenter)
    {
        //
    }
}
