<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChildDevelopmentCenter;
use App\Models\Implementation;
use App\Models\Child;
use App\Models\ChildCenter;
use App\Models\UserCenter;

class ChildCenterController extends Controller
{
    public function additionalInfo(Request $request, Implementation $cycle, $id)
    {
        $this->authorize('create-child');

        $cycles = Implementation::where('status', 'active')->where('type', 'regular')->get();
        $milkFeedings = Implementation::where('status', 'active')->where('type', 'milk')->get();

        $child = Child::findOrFail($id);

        $centers = ChildDevelopmentCenter::all();

        $userID = auth()->id();
        if (auth()->user()->hasRole('child development worker')){
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();

        } elseif(auth()->user()->hasRole('encoder')){
            $centers = UserCenter::where('user_id', $userID)->get();
            $centerIDs = $centers->pluck('child_development_center_id');

            $centerNames = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();
        }

        return view('child.additional-info',
        compact([
            'child',
            'cycles',
            'milkFeedings',
            'centerNames'
        ]));
    }
}
