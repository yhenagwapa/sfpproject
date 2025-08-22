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
    public function updateStatus(Request $request)
    {
        $childID = $request->input('child_id');
        $status = $request->input('status');
        $oldCenter = $request->input('oldCenter');
        $newCenter = $request->input('newCenter');

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->value('id');

        $request->validate([
            'newCenter' => 'required',
            ], [
                'newCenter.required' => 'Please select a center before confirming transfer.',
            ]);

        $childStatus = ChildCenter::where('child_id' , $childID)
            ->where('implementation_id', $cycle)
            ->first();

        $childStatus->update([
            'status' => $request->input('newCenter')
        ]);

        return redirect()->back()
                ->withSuccess('User status updated successfully.');
    }
}
