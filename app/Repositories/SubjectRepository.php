<?php

namespace App\Repositories;

use App\Models\Subject;
use App\Classes\BaseHelper as BH;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectRepository
{
    public function index($request)
    {
        $paginateSize = $request->input('paginate_size', null);
        $paginateSize = BH::checkPaginateSize($paginateSize);
        $searchKey    = $request->input('name', null);

        try {
            $subjects = Subject::when($searchKey, fn ($query) => $query->where("name", "like", "%$searchKey%"))
                ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $subjects;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function store($request)
    {
        $name     = $request->input('name', null);
        $status   = $request->input('status', 'active');
        $itemCode = $request->input('item_code', null);
        $unite    = $request->input('unite', null);
        $stock    = $request->input('stock', null);
        $price    = $request->input('unite_price', null);

        try {
            DB::beginTransaction();

            $subject = new Subject();

            $subject->name        = $name;
            $subject->status      = $status;
            $subject->item_code   = $itemCode;
            $subject->unite       = $unite;
            $subject->stock       = $stock;
            $subject->unite_price = $price;
            $subject->save();

            DB::commit();

            return $subject;
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            return Subject::find($id);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function update($request, Subject $subject)
    {
        $name     = $request->input('name', null);
        $status   = $request->input('status', 'active');
        $itemCode = $request->input('item_code', null);
        $unite    = $request->input('unite', null);
        $stock    = $request->input('stock', null);
        $price    = $request->input('unite_price', null);

        try {
            DB::beginTransaction();

            $subject->name        = $name;
            $subject->status      = $status;
            $subject->item_code   = $itemCode;
            $subject->unite       = $unite;
            $subject->stock       = $stock;
            $subject->unite_price = $price;
            $subject->save();

            DB::commit();

            return $subject;
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());

            throw $exception;
        }
    }
}
