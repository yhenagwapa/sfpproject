<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ChildDevelopmentCenter;
use App\Models\Implementation;
use App\Models\User;
use App\Models\UserCenter;
use Illuminate\Http\Request;

class ChildApiController extends Controller
{
    /**
     * Get paginated children list for server-side DataTables
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        try {
            // Get parameters from request
            $userId = $request->input('user_id');
            $cdcId = $request->input('center_name');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw', 1);
            $searchValue = $request->input('search.value', '');
            $orderColumnIndex = $request->input('order.0.column', 1);
            $orderDirection = $request->input('order.0.dir', 'asc');

            // Get active cycle
            $cycle = Implementation::where('status', 'active')
                ->where('type', 'regular')
                ->first();

            if (!$cycle) {
                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'No active implementation.'
                ], 200);
            }

            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'User not found'
                ], 200);
            }

            $isAdmin = $user->hasRole('admin');

            // Build base query
            $query = Child::with([
                'records' => function ($q) use ($cycle) {
                    $q->where('implementation_id', $cycle->id)
                        ->whereIn('action_type', ['active', 'transferred'])
                        ->orderBy('created_at', 'desc')
                        ->with(['center', 'centerFrom', 'centerTo']);
                },
                'sex'
            ])
                ->withCount([
                    'records as transfer_count' => function ($q) use ($cycle) {
                        $q->where('implementation_id', $cycle->id)
                            ->where('action_type', 'transferred');
                    }
                ]);

            // Apply center filtering based on user role
            if ($isAdmin) {
                // Admin can see all centers or filter by specific center
                if ($cdcId && $cdcId !== 0 && $cdcId !== '0' && $cdcId !== 'all_center' && $cdcId !== 'all') {
                    // Filter by specific center
                    $query->whereHas('records', function ($q) use ($cdcId, $cycle) {
                        $q->where('implementation_id', $cycle->id)
                            ->where('action_type', 'active')
                            ->where('child_development_center_id', $cdcId);
                    });
                } else {
                    // Show all children from all centers
                    $query->whereHas('records', function ($q) use ($cycle) {
                        $q->where('implementation_id', $cycle->id)
                            ->where('action_type', 'active');
                    });
                }
            } else {
                // Non-admin: get user's assigned centers
                $centerIds = UserCenter::where('user_id', $userId)
                    ->pluck('child_development_center_id')
                    ->toArray();

                if (empty($centerIds)) {
                    // User has no assigned centers - return empty result
                    $query->whereRaw('1 = 0');
                } elseif ($cdcId && $cdcId !== 'all_center' && $cdcId !== 'all' && $cdcId !== '0' && $cdcId !== 0) {
                    // Filter by specific center (must be in user's assigned centers)
                    if (in_array($cdcId, $centerIds)) {
                        $query->whereHas('records', function ($q) use ($cdcId, $cycle) {
                            $q->where('implementation_id', $cycle->id)
                                ->where('action_type', 'active')
                                ->where('child_development_center_id', $cdcId);
                        });
                    } else {
                        // User trying to access center they don't have access to
                        $query->whereRaw('1 = 0');
                    }
                } else {
                    // Show all children from user's assigned centers
                    $query->whereHas('records', function ($q) use ($centerIds, $cycle) {
                        $q->where('implementation_id', $cycle->id)
                            ->where('action_type', 'active')
                            ->whereIn('child_development_center_id', $centerIds);
                    });
                }
            }

            // Apply search filter
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('firstname', 'like', "%{$searchValue}%")
                        ->orWhere('middlename', 'like', "%{$searchValue}%")
                        ->orWhere('lastname', 'like', "%{$searchValue}%")
                        ->orWhere('extension_name', 'like', "%{$searchValue}%")
                        ->orWhereHas('sex', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        })
                        ->orWhereHas('records.center', function ($q) use ($searchValue) {
                            $q->where('center_name', 'like', "%{$searchValue}%");
                        });
                });
            }

            // Get total records before filtering
            $totalRecords = Child::whereHas('records', function ($q) use ($cycle) {
                $q->where('implementation_id', $cycle->id)
                    ->where('action_type', 'active');
            })->count();

            // Get filtered count
            $filteredRecords = $query->count();

            // Apply sorting
            $columns = ['id', 'lastname', 'sex_id', 'date_of_birth', 'center_name', 'funded', 'transfer_count', 'action_type'];
            if (isset($columns[$orderColumnIndex])) {
                $orderColumn = $columns[$orderColumnIndex];

                if ($orderColumn === 'sex_id') {
                    $query->orderBy('sex_id', $orderDirection);
                } elseif ($orderColumn === 'lastname') {
                    $query->orderBy('lastname', $orderDirection)
                        ->orderBy('firstname', $orderDirection);
                } else {
                    $query->orderBy($orderColumn, $orderDirection);
                }
            } else {
                // Default sorting
                $query->orderByRaw("CASE WHEN sex_id = 1 THEN 0 ELSE 1 END")
                    ->orderBy('lastname');
            }

            // Apply pagination
            $children = $query->skip($start)
                ->take($length)
                ->get();

            // Transform data for DataTable
            $data = $children->map(function ($child, $index) use ($start) {
                $child->has_transferred = $child->transfer_count > 0;

                // Get the most recent record
                $latestRecord = $child->records->first();

                // Format full name
                $fullName = trim($child->firstname . ' ' . ($child->middlename ?? '') . ' ' . $child->lastname . ' ' . ($child->extension_name ?? ''));

                return [
                    'no' => $start + $index + 1,
                    'id' => $child->id,
                    'child_name' => $fullName,
                    'firstname' => $child->firstname,
                    'middlename' => $child->middlename,
                    'lastname' => $child->lastname,
                    'extension_name' => $child->extension_name,
                    'sex' => $child->sex->name ?? '',
                    'sex_id' => $child->sex_id,
                    'date_of_birth' => $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->format('m-d-Y') : '',
                    'center_name' => $latestRecord?->center?->center_name ?? '',
                    'center_id' => $latestRecord?->child_development_center_id ?? '',
                    'funded' => $latestRecord?->funded ? 'Yes' : 'No',
                    'has_transferred' => $child->has_transferred ? 'Yes' : 'No',
                    'action_type' => $latestRecord?->action_type ?? '',
                    'status' => ucfirst($latestRecord?->action_type ?? 'inactive'),
                    'is_dropped' => $latestRecord?->action_type === 'dropped',
                    'is_transferred' => $latestRecord?->action_type === 'transferred',
                    'edit_counter' => $child->edit_counter ?? 0,
                ];
            });

            // Get center name for display
            $centerName = $this->getCenterName($cdcId);

            // Return DataTables format
            return response()->json([
                'draw' => (int) $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
                'center_name' => $centerName,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error fetching children data: ' . $e->getMessage()
            ], 200);
        }
    }

    /**
     * Get center name based on ID
     *
     * @param mixed $cdcId
     * @return string
     */
    private function getCenterName($cdcId)
    {
        if (!$cdcId || $cdcId === 'all_center' || $cdcId === 'all' || $cdcId === '0' || $cdcId === 0) {
            return 'All CDC/SNP';
        }

        $center = ChildDevelopmentCenter::find($cdcId);
        return $center ? $center->center_name : 'Unknown Center';
    }
}
