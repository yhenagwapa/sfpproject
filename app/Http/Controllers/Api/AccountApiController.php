<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Psgc;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class AccountApiController extends Controller
{
    /**
     * Get paginated users list for server-side DataTables
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        try {
            // Get parameters from request
            $userId = $request->input('user_id');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw', 1);
            $searchValue = $request->input('search.value', '');
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'asc');

            $currentUser = User::find($userId);

            if (!$currentUser) {
                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'User not authenticated'
                ], 200);
            }

            $isAdmin = $currentUser->hasRole('admin');

            // Build base query with relationships
            $query = User::with(['roles', 'psgc']);

            // Apply location-based filtering for non-admins
            if (!$isAdmin) {
                $psgcCity = Psgc::find($currentUser->psgc_id)->city_name_psgc ?? null;

                if (!$psgcCity) {
                    return response()->json([
                        'draw' => $draw,
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => [],
                        'error' => 'User location not found'
                    ], 200);
                }

                // If user is SFP coordinator in Davao City
                if ($currentUser->hasRole('sfp coordinator')) {
                    $psgcDistrict = Psgc::find($currentUser->psgc_id)->subdistrict ?? null;

                    if ($psgcDistrict) {
                        // Filter by district
                        $query->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                            ->where('psgcs.subdistrict', $psgcDistrict)
                            ->select('users.*');
                    } else {
                        // No district found - return empty result
                        $query->whereRaw('1 = 0');
                    }
                } else {
                    // Filter by city
                    $query->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                        ->where('psgcs.city_name_psgc', $psgcCity)
                        ->select('users.*');
                }
            }

            // Apply search filter
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('firstname', 'like', "%{$searchValue}%")
                        ->orWhere('lastname', 'like', "%{$searchValue}%")
                        ->orWhere('email', 'like', "%{$searchValue}%")
                        ->orWhereHas('roles', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        })
                        ->orWhereHas('psgc', function ($q) use ($searchValue) {
                            $q->where('city_name_psgc', 'like', "%{$searchValue}%")
                                ->orWhere('subdistrict', 'like', "%{$searchValue}%");
                        });
                });
            }

            // Get total records count (before filtering)
            $totalQuery = User::query();
            if (!$isAdmin) {
                $psgcCity = Psgc::find($currentUser->psgc_id)->city_name_psgc ?? null;

                if ($currentUser->hasRole('sfp coordinator')) {
                    $psgcDistrict = Psgc::find($currentUser->psgc_id)->subdistrict ?? null;
                    if ($psgcDistrict) {
                        $totalQuery->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                            ->where('psgcs.subdistrict', $psgcDistrict);
                    }
                } else {
                    $totalQuery->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                        ->where('psgcs.city_name_psgc', $psgcCity);
                }
            }
            $totalRecords = $totalQuery->count();

            // Get filtered count
            $filteredRecords = $query->count();

            // Apply sorting
            $columns = ['id', 'lastname', 'firstname', 'middlename', 'email', 'created_at'];
            if (isset($columns[$orderColumnIndex])) {
                $orderColumn = $columns[$orderColumnIndex];
                $query->orderBy($orderColumn, $orderDirection);
            } else {
                // Default sorting
                $query->orderBy('lastname');
                $query->orderBy('firstname');
                $query->orderBy('middlename');
            }

            // Apply pagination
            $users = $query->skip($start)
                ->take($length)
                ->get();

            // Transform data for DataTable
            $data = $users->map(function ($user, $index) use ($start) {
                return [
                    'no' => $start + $index + 1,
                    'id' => $user->id,
                    'name' => $user->firstname . ' ' . $user->middlename . ' ' . $user->lastname,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->implode(', '),
                    'location' => $user->psgc ? ($user->psgc->brgy_name_psgc . ', ' . $user->psgc->city_name_psgc) : 'N/A',
                    'city' => $user->psgc->city_name_psgc ?? 'N/A',
                    'barangay' => $user->psgc->brgy_name_psgc ?? 'N/A',
                    'district' => $user->psgc->subdistrict ?? 'N/A',
                    'created_at' => $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('m-d-Y') : '',
                    'is_active' => $user->is_active ?? true,
                    'email_verified_at' => $user->email_verified_at ?? null,
                    'status' => $user->status ?? 'deactivated',
                ];
            });

            // Get all roles for additional data
            $roles = Role::all()->sortBy('name')->values();

            // Return DataTables format
            return response()->json([
                'draw' => (int) $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
                'roles' => $roles,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error fetching users data: ' . $e->getMessage()
            ], 200);
        }
    }
}
