<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use Illuminate\Support\Facades\Log;
use App\Repositories\UserRepository;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Admin\UserCollection;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;

class UserController extends BaseController
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('users-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $users = $this->repository->index($request);

            $users = new UserCollection($users);

            return $this->sendResponse($users, "User list");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(StoreUserRequest $request)
    {
        if (!$request->user()->hasPermission('users-create')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $user = $this->repository->store($request);

            $user = new UserResource($user);

            return $this->sendResponse($user, "User created successfully");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('users-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $user = $this->repository->show($id);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'User single view');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        if (!$request->user()->hasPermission('users-update')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $user = User::find($id);
            if (!$user) {
                return $this->sendError("User not found", 404);
            }

            $user = $this->repository->update($request, $user);
            if (!$user) {
                return $this->sendError("User not found", 404);
            }

            $user = new UserResource($user);

            return $this->sendResponse($user, "User updated successfully");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function userPermission()
    {
        try {
            $user = $this->repository->userPermission();

            return $this->sendResponse($user, 'User permissions');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request)
    {
        $category = User::where('id', $request->id)->delete();

        return $this->sendResponse($category, 'User Deleted successfully');
    }
}
