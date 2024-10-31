<?php

namespace App\Http\Controllers;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    { 
        $activities = Activity::with('causer', 'subject')->latest()->paginate(10);

        $groupedActivities = [];

        foreach ($activities as $activityLog) {
            
            $logData = $activityLog->properties;

            $logDataArray = json_decode($logData, true);

            if (isset($logDataArray['old'])) {
                $eventType = 'updated';
                $oldAttributes = $logDataArray['old'];
                $newAttributes = $logDataArray['attributes'];
        
                $changedFields = [];
                foreach ($oldAttributes as $key => $oldValue) {
                    $newValue = $newAttributes[$key] ?? null;
                    if ($oldValue !== $newValue) {
                        $changedFields[$key] = [
                            'old' => $oldValue,
                            'new' => $newValue,
                        ];
                    }
                }
            } else {
                $eventType = 'created';
                $changedFields = $logDataArray['attributes'];
            }
        
            $groupedActivities[] = [
                'activity_id' => $activityLog->id,
                'description' => $activityLog->description,
                'event_type' => ucfirst($eventType),
                'causer' => $activityLog->causer->full_name ?? 'N/A',
                'subject_type' => class_basename($activityLog->subject_type),
                'changed_fields' => $changedFields,
                'created_at' => $activityLog->created_at,
            ];
        }

    return view('activitylogs.index', compact('groupedActivities', 'activities'));
    }
}