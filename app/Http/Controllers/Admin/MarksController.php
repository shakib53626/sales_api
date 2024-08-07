<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MarksRequest;
use App\Http\Resources\Admin\MarksCollection;
use App\Http\Resources\Admin\MarksResource;
use App\Models\Mark;
use App\Repositories\MarksRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MarksController extends Controller
{
    protected $repository;

    public function __construct(MarksRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('marks-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $marks = $this->repository->index($request);

            $marks = new MarksCollection($marks);

            return $this->sendResponse($marks, "Marks list");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(MarksRequest $request)
    {
        if (!$request->user()->hasPermission('marks-create')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $marks = $this->repository->store($request);

            $marks = new MarksResource($marks);

            return $this->sendResponse($marks, "Marks created successfully");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('marks-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $marks = $this->repository->show($id);

            $marks = new MarksResource($marks);

            return $this->sendResponse($marks, 'Marks single view');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(MarksRequest $request, $id)
    {
        if (!$request->user()->hasPermission('marks-update')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $marks = Mark::find($id);
            if (!$marks) {
                return $this->sendError("Marks not found", 404);
            }

            $marks = $this->repository->update($request, $marks);
            if (!$marks) {
                return $this->sendError("Marks not found", 404);
            }

            $marks = new MarksResource($marks);

            return $this->sendResponse($marks, "Marks updated successfully");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request)
    {
        $marks = Mark::where('id', $request->id)->delete();

        return $this->sendResponse($marks, 'Marks Deleted successfully');
    }
}
