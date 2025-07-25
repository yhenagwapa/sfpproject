<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lastname',
        'firstname',
        'middlename',
        'extension_name',
        'contact_number',
        'address',
        'psgc_id',
        'email',
        'password',
        'status',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['lastname', 'firstname', 'middlename', 'extension_name', 'contact_number', 'address', 'psgc_id', 'email', 'status', 'created_at', 'updated_at'])
            ->logOnlyDirty();
    }

    public function assignRoleAndPermissionsToUser($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->hasRole('child development worker')) {

            $permissions = [
                'create-child',
                'add-attendance',
                'nutrition-status-entry',
                'nutrition-status-exit',
                'view-report',
                'print-report',
                'register',
                'edit-user-profile',
            ];
            $role = Role::where('name', 'child development worker')->firstOrFail();
        } elseif ($user->hasRole('admin')) {

            $permissions = [
                'edit-child',
                'delete-child',
                'view-report',
                'print-report',
                'create-child-development-center',
                'edit-child-development-center',
                'delete-child-development-center',
                'create-role',
                'edit-role',
                'delete-role',
                'register',
                'edit-user-profile',
                'delete-user',
                'view-audit-logs'
            ];
            $role = Role::where('name', 'admin')->firstOrFail();
        }

        $permissionModels = Permission::whereIn('name', $permissions)->get();

        // Grant the fetched permissions to the role
        $role->syncPermissions($permissionModels);

        // Assign the role to the user if not already assigned
        if (!$user->hasRole($role->name)) {
            $user->assignRole($role);
        }

        // Optionally, assign permissions directly to the user
        $user->syncPermissions($permissionModels);
    }

    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->middlename} {$this->lastname} {$this->extension_name}";
    }

    public function centers()
    {
        return $this->belongsToMany(ChildDevelopmentCenter::class, 'user_center', 'user_id', 'child_development_center_id')->withTimestamps();
    }

    public function psgc()
    {
        // Model, foreign key on users table, local key on psgcs table
        return $this->belongsTo(Psgc::class, 'psgc_id', 'psgc_id');
    }

}
