<?php

namespace App\Http\Controllers;

use App\Models\Sex;
use App\Http\Requests\StoreSexRequest;
use App\Http\Requests\UpdateSexRequest;

class SexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSexRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Sex $sex)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sex $sex)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSexRequest $request, Sex $sex)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sex $sex)
    {
        //
    }
}
