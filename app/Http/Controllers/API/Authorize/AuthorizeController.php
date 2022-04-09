<?php

namespace App\Http\Controllers\Api\Authorize;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Repositories\Auth\AuthorizeRepository;
use Illuminate\Http\Request;

class AuthorizeController extends AppBaseController
{
    public $authorizeRepo;

    public function __construct(AuthorizeRepository $authorizeRepository)
    {
        $this->middleware('auth:sanctum');
        return $this->authorizeRepo = $authorizeRepository;
    }

    public function roles()
    {
        $roles = $this->authorizeRepo->roles();
        return  $this->sendResponse(RoleResource::collection($roles),"success");
    }

    public function permissions()
    {
        $permissions = $this->authorizeRepo->permissions();
        return  $this->sendResponse(RoleResource::collection($permissions),"success");
    }

    public function createRole(Request $request)
    {
        $role = $this->authorizeRepo->createRole($request);
        return $this->sendResponse(new RoleResource($role),"success");
    }

    public function createPermission(Request $request)
    {
        $permissions = $this->authorizeRepo->createPermission($request);
        return $this->sendResponse("success", new PermissionResource($permissions));
    }

    public function assignRoleToPermission(Request $request)
    {
        $permissions = $this->authorizeRepo->assignRoleToPermission($request);
        return $this->sendResponse(PermissionResource::collection($permissions),"success");
    }

    public function rolePermissions($role_id)
    {
        $permissions = $this->authorizeRepo->rolePermissions($role_id);
        return $this->sendResponse(PermissionResource::collection($permissions),"success");
    }

    public function assignRoleToUser(Request $request)
    {
        $userRoles = $this->authorizeRepo->assignRoleToUser($request);
        return $this->sendResponse($userRoles,"success");
    }

    public function userPermissions($user_id)
    {
        $permissions = $this->authorizeRepo->userPermissions($user_id);
        return $this->sendResponse(PermissionResource::collection($permissions),"success");
    }

    public function revokePermission(Request $request)
    {
        $permissions = $this->authorizeRepo->revokePermission($request);
        return $this->sendResponse(PermissionResource::collection($permissions),"success");
    }
}
