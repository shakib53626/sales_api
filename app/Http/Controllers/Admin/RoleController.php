<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use Illuminate\Support\Facades\Log;
use App\Repositories\RoleRepository;
use App\Http\Resources\Admin\RoleResource;
use App\Http\Resources\Admin\RoleCollection;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;

class RoleController extends BaseController
{
    protected $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('roles-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $roles = $this->repository->index($request);

            $roles = new RoleCollection($roles);
            return $this->sendResponse($roles, 'Role list');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(StoreRoleRequest $request)
    {
        if (!$request->user()->hasPermission("roles-create")) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {

            $res = $this->repository->store($request);

            return new RoleResource($res);

            return $this->sendResponse($res, "Role created successfully");
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('roles-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $role = $this->repository->show($id);
            if (!$role) {
                return $this->sendError("Role not found", 404);
            }

            $role = new RoleResource($role);

            return $this->sendResponse($role, "Role single view");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        if (!$request->user()->hasPermission('roles-update')) {
            return $this->sendError(__("common.unauthorized"));
        }

        $role = Role::find($id);

        if (!$role) {
            return $this->sendError("Role not found", 404);
        }

        try {
            $role = $this->repository->update($request, $role);

            $role = new RoleResource($role);

            return $this->sendResponse($role, "Role updated successfully");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
