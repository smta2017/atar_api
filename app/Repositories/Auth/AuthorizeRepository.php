<?php

namespace App\Repositories\Auth;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
/**
 * Class AuthRepository
 */
class AuthorizeRepository
{
    /**
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }

    public function roles()
    {
        $roles = Role::get();
        return $roles;
    }

    public function permissions()
    {
        $permissions = Permission::all();
        return $permissions;
    }

    public function createPermission($request)
    {
        $permission = Permission::create(['name' => $request->name]);
        return $permission;
    }

    public function createRole($request)
    {
        $role = Role::create(['name' => $request->name]);
        return $role;
    }

    public function assignRoleToPermission($request)
    {
        $role = Role::find($request->role_id);
        $permission = Permission::where('name', $request->permission)->first();
        $permission->assignRole($role);
        return  $role->permissions;
    }

    public function rolePermissions($role_id)
    {
        $role = Role::find($role_id);
        $permissions = $role->permissions;
        return $permissions;
    }

    public function assignRoleToUser($request)
    {
        $user = User::find($request->user_id);
        $user->assignRole($request->role_name);
        return $user->getRoleNames();
    }

    public function userPermissions($user_id)
    {
        $user = User::find($user_id);
        return $user->getAllPermissions();
    }

    public function revokePermission($request)
    {
        $role = Role::find($request->role_id);
        $permission = Permission::find($request->permission_id);
        $role->revokePermissionTo($permission);
        return $role->permissions;
    }
}
