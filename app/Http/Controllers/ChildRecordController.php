<?php

namespace App\Http\Controllers;

use App\Models\ChildHistory;
use Illuminate\Http\Request;
use App\Models\ChildDevelopmentCenter;
use App\Models\Implementation;
use App\Models\Child;
use App\Models\ChildRecord;
use App\Models\UserCenter;

class ChildRecordController extends Controller
{
    public function updateStatus(Request $request)
    {
        $childID = $request->input('child_id');
        $status = $request->input('status');
        $oldCenter = $request->oldCenter;
        $newCenter = $request->newCenter;

        $cycle = Implementation::where('status', 'active')->where('type', 'regular')->value('id');

        $childStatus = ChildRecord::where('child_id' , $childID)
            ->where('implementation_id', $cycle)
            ->latest();

        if($status == 'dropped'){
            $childStatus->update([
                'action_type' => $status,
                'action_date' => now(),
            ]);

        } elseif($status == 'transferred'){
            $childStatus->update([
                'action_type' => $status,
                'action_date' => now(),
            ]);

            ChildRecord::create([
                'child_id' => $childID,
                'implementation_id' => $cycle,
                'action_type' => 'active',
                'action_date' => now(),
                'center_from' => $oldCenter,
                'center_to' => $newCenter,
                'funded' => '1',
            ]);
        }
        return redirect()->back()->withSuccess('Child status updated successfully.');
    }
}
