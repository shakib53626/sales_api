<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Support\Str;
use App\Classes\BaseHelper as BH;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleRepository
{
    public function index($request)
    {
        $paginateSize = $request->input('paginate_size', null);
        $paginateSize = BH::checkPaginateSize($paginateSize);
        $displayName  = $request->input('display_name', null);

        try {

            $roles = Role::select('id', 'display_name', 'name', 'description')
                ->when($displayName, fn ($query) => $query->where('display_name', 'like', "%$displayName%"));
            return $roles->orderBy('created_at', 'desc')->paginate($paginateSize);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }
    public function store($request)
    {
        $displayName   = $request->input('display_name', null);
        $description   = $request->input('description', null);
        $permissionIds = $request->input('permission_ids', []);
        $name          = Str::slug($displayName, '-');

        try {
            DB::beginTransaction();

            $role = new Role();

            $role->display_name = $displayName;
            $role->name         = $name;
            $role->description  = $description;
            $res = $role->save();
            if ($res) {
                $role->syncPermissions($permissionIds);
            }

            DB::commit();

            return $role;
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            return Role::with(["createdBy:id,name", "permissions" => function ($query) {
                $query->orderBy("display_name", "asc")->select("id", "display_name");
            }])->find($id);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function update($request, Role $role)
    {
        $displayName   = $request->input('display_name', null);
        $description   = $request->input('description', null);
        $permissionIds = $request->input('permission_ids', []);
        $name          = Str::slug($displayName, '-');

        try {
            DB::beginTransaction();

            $role->display_name = $displayName;
            $role->name         = $name;
            $role->description  = $description;
            $res = $role->save();
            if ($res) {
                $role->syncPermissions($permissionIds);
            }

            DB::commit();

            return $role;
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());

            throw $exception;
        }
    }
}
