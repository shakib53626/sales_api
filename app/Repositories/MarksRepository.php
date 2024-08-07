<?php

namespace App\Repositories;

use App\Classes\BaseHelper as BH;
use App\Models\Mark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MarksRepository
{
    public function index($request)
    {
        $paginateSize = $request->input('paginate_size', null);
        $paginateSize = BH::checkPaginateSize($paginateSize);
        $searchKey    = $request->input('search_key', null);

        try {
            $marks = Mark::when($searchKey, fn ($query) => $query->where("name", "like", "%$searchKey%"))
                ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $marks;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function store($request)
    {
        $subjectId = $request->input('subject_id', null);
        $studentId = $request->input('student_id', null);
        $regNo     = $request->input('reg_no', null);
        $marks     = $request->input('marks', null);

        try {
            DB::beginTransaction();

            $mark = new Mark();

            $mark->subject_id = $subjectId;
            $mark->student_id = $studentId;
            $mark->reg_no     = $regNo;
            $mark->marks      = $marks;
            $mark->save();

            DB::commit();

            return $mark;
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            return Mark::find($id);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function update($request, Mark $mark)
    {
        $subjectId = $request->input('subject_id', null);
        $studentId = $request->input('student_id', null);
        $regNo     = $request->input('reg_no', null);
        $marks     = $request->input('marks', null);

        try {
            DB::beginTransaction();

            $mark->subject_id = $subjectId;
            $mark->student_id = $studentId;
            $mark->reg_no     = $regNo;
            $mark->marks      = $marks;
            $mark->save();

            DB::commit();

            return $mark;
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());

            throw $exception;
        }
    }
}
