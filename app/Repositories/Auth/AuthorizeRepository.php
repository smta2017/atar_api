<?php

namespace App\Repositories\Eloquent\User;

use App\Helpers\ApiResponse;
use App\Http\Resources\authorize\permissionResource;
use App\Http\Resources\authorize\roleResource;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\User\IAuthorize;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Class AuthRepository
 */
class AuthorizeRepository extends BaseRepository implements IAuthorize
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
        return  ApiResponse::format("success", roleResource::collection($roles));
    }

    public function permissions()
    {
        $permissions = Permission::all();
        return  ApiResponse::format("success", permissionResource::collection($permissions));
    }

    public function createPermission($request)
    {
        $permission = Permission::create(['name' => $request->name]);
        return  ApiResponse::format("success", new permissionResource($permission));
    }

    public function createRole($request)
    {
        $role = Role::create(['name' => $request->name]);
        return  ApiResponse::format("success", new roleResource($role));
    }

    public function assignRoleToPermission($request)
    {
        $role = Role::find($request->role_id);
        $permission = Permission::where('name', $request->permission)->first();
        $permission->assignRole($role);
        return  ApiResponse::format("success", roleResource::collection($role->permissions));
    }

    public function rolePermissions($role_id)
    {
        $role = Role::find($role_id);
        $rolePerms = $role->permissions;
        return  ApiResponse::format("success", permissionResource::collection($rolePerms));
    }

    public function assignRoleToUser($request)
    {
        $user = User::find($request->user_id);
        $user->assignRole($request->role_name);
        return  ApiResponse::format("success", permissionResource::collection($user->getAllPermissions()));
    }

    public function userPermissions($user_id)
    {
        $user = User::find($user_id);
        return  ApiResponse::format("success", permissionResource::collection($user->getAllPermissions()));
    }

    public function revoke($request)
    {
        $role = Role::find($request->role_id);
        $permission = Permission::find($request->permission_id);
        $role->revokePermissionTo($permission);
        return  ApiResponse::format("success", permissionResource::collection($role->permissions));
    }
}
