<?php

namespace App\Repositories;

use App\Models\User;
use App\Classes\BaseHelper as BH;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class UserRepository
{
    public function index($request)
    {
        $paginateSize = $request->input('paginate_size', null);
        $paginateSize = BH::checkPaginateSize($paginateSize);
        $name         = $request->input('name', null);
        $phoneNumber  = $request->input('phone_number', null);

        try {
            $users = User::with('roles')
                ->when($name, fn ($query)        => $query->where("name", "like", "%$name%"))
                ->when($phoneNumber, fn ($query) => $query->where("phone_number", "like", "%$phoneNumber%"))
                ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $users;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function store($request)
    {
        $name        = $request->input('name', null);
        $email       = $request->input('email', null);
        $phoneNumber = $request->input('phone_number', null);
        $password    = $request->input('password', null);
        $status      = $request->input('status', 'active');
        $roleIds     = $request->input('role_ids', []);

        try {
            DB::beginTransaction();

            $user = new User();

            $user->name         = $name;
            $user->email        = $email;
            $user->phone_number = $phoneNumber;
            $user->status       = $status;
            $user->password     =  Hash::make($password);
            $res = $user->save();
            if ($res) {
                $user->syncRoles($roleIds);
            }
            if ($res) {
                // Upload image
                if ($request->hasFile('image')) {
                    $image     = $request->file('image');
                    $imageName = time() . '.' . 'webp';
                    $image->move(public_path($user->path), $imageName);
                    $user->image = $imageName;
                    $user->save();
                }
            }

            DB::commit();

            return $user->load("roles:id,name", "roles.permissions:id,name");
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            return User::with("roles:id,name,display_name", "roles.permissions:id,name,display_name")
                ->find($id);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function update($request, User $user)
    {
        $name        = $request->input('name', null);
        $email       = $request->input('email', null);
        $phoneNumber = $request->input('phone_number', null);
        $password    = $request->input('password', null);
        $status      = $request->input('status', 'inactive');
        $roleIds     = $request->input('role_ids', []);

        try {
            DB::beginTransaction();

            $user->name         = $name;
            $user->email        = $email;
            $user->phone_number = $phoneNumber;
            $user->status       = $status;
            if ($password) {
                $user->password =  Hash::make($password);
            }
            $res = $user->save();
            if ($res) {
                $user->syncRoles($roleIds);
            }
            if ($res) {
                if ($request->hasFile("image")) {
                    $file         = $request->file("image");
                    $oldImagePath = BH::getOldPath($user->path, $user->image);

                    // Delete old image
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }

                    $imageName = time() . "." . "webp";
                    $file->move(public_path($user->path), $imageName);
                    $user->image = $imageName;
                    $user->save();
                }
            }

            DB::commit();

            return $user->load("roles:id,name", "roles.permissions:id,name");
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function userPermission()
    {
        $id = auth()->id();

        try {
            $user = User::with("roles:id,name,display_name", "roles.permissions:id,name,display_name")->find($id);

            return $user;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }
}
