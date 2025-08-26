<?php

namespace App\Http\Controllers;

use App\Models\ChildHistory;
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
        $oldCenter = $request->oldCenter;
        $newCenter = $request->newCenter;
        $childHistory = null;

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->value('id');

        $childStatus = ChildCenter::where('child_id' , $childID)
            ->where('implementation_id', $cycle)
            ->first();

        if($status == 'dropped'){
            $childStatus->update([
                'status' => $status,
            ]);

        } elseif($status == 'transferred'){
            $childStatus->update([
                'status' => $status,
            ]);

            ChildCenter::create([
                'child_id' => $childID,
                'child_development_center_id' => $newCenter,
                'implementation_id' => $cycle,
                'status' => 'active',
                'funded' => '1',
            ]);
        }

        $childHistory = ChildHistory::create([
            'child_id' => $childID,
            'implementation_id' => $cycle,
            'action_type' => $status,
            'action_date' => now(),
            'center_from' => $oldCenter,
            'center_to' => $newCenter,
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->back()->withSuccess('Child status updated successfully.');
    }
}
