<?php

namespace App\Http\Controllers\Admin;

use App\Classes\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Repositories\SubjectRepository;
use App\Http\Resources\Admin\SubjectResource;
use App\Http\Resources\Admin\SubjectCollection;
use App\Http\Requests\Admin\StoreSubjectRequest;
use App\Http\Requests\Admin\UpdateSubjectRequest;
use App\Models\Subject;

class SubjectController extends BaseController
{

    protected $repository;

    public function __construct(SubjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('subjects-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $users = $this->repository->index($request);

            $users = new SubjectCollection($users);

            return $this->sendResponse($users, "User list");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(StoreSubjectRequest $request)
    {
        if (!$request->user()->hasPermission('subjects-create')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $subject = $this->repository->store($request);

            $subject = new SubjectResource($subject);

            return $this->sendResponse($subject, "Subject created successfully");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('subjects-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $subject = $this->repository->show($id);

            $subject = new SubjectResource($subject);

            return $this->sendResponse($subject, 'Subject single view');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(StoreSubjectRequest $request, $id)
    {
        if (!$request->user()->hasPermission('subjects-update')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $subject = Subject::find($id);
            if (!$subject) {
                return $this->sendError("Subject not found", 404);
            }

            $subject = $this->repository->update($request, $subject);
            if (!$subject) {
                return $this->sendError("Subject not found", 404);
            }

            $subject = new SubjectResource($subject);

            return $this->sendResponse($subject, "Subject updated successfully");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request)
    {
        $subject = Subject::where('id', $request->id)->delete();

        return $this->sendResponse($subject, 'Subject Deleted successfully');
    }
}
