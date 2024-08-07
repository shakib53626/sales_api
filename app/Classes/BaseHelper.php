<?php

namespace App\Classes;

use Illuminate\Support\Facades\File;


class BaseHelper
{
    public static function sendResponse($result, $message, $code = 200)
    {
        $response = [
            "success" => true,
            "message" => $message,
            "result" => $result
        ];

        return response()->json($response, $code);
    }

    public static function sendError($message, $code = 400)
    {

        $response = [
            "success" => false,
            "message" => $message,
        ];

        return response()->json($response, $code);
    }

    public static function checkPaginateSize($paginate = null)
    {
        $maxPaginate = config('crud.paginate.max');
        $defaultPaginate = config('crud.paginate.default');
        $paginate = $paginate ?? $defaultPaginate;
        $paginate = $paginate > $maxPaginate ? $maxPaginate : $paginate;

        return $paginate;
    }

    public static function getOldPath($path, $imageName)
    {
        // Use pathinfo to extract the filename
        $pathInfo = pathinfo($imageName);

        // Get the filename from pathinfo
        $filename = $pathInfo["basename"];

        return public_path($path . "/" . $filename);
    }

    //images file
    public static function uploadFile($obj, $file, $path, $name = null)
    {
        if ($file) {
            $fileName = time() . ".$name" . 'webp';
            $file->move(public_path($path), $fileName);
            $obj->image = $fileName;
            $obj->save();
        }
    }

    public static function updateFile($obj, $file, $path, $oldFileName)
    {
        if ($file) {
            // Delete old file
            $pathInfo = pathinfo($oldFileName);
            $filename = $pathInfo["basename"];
            $oldFilePath = public_path($path . "/" . $filename);

            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }

            // Upload new image
            $fileName = time() . '.' . 'webp';
            $file->move(public_path($path), $fileName);
            $obj->image = $fileName;
            $obj->save();
        }
    }

    public static function getFilePath($path, $imageName)
    {
        if ($imageName) {
            if (File::exists(public_path($path . '/' . $imageName))) {
                $imagePath = asset($path . '/' . $imageName);
            } else {
                $imagePath = asset('images/default.png');
            }
        } else {
            $imagePath = asset('images/default.png');
        }

        return $imagePath;
    }

    public static function deleteFile($path, $fileName)
    {
        // Delete file
        if ($fileName) {
            $pathInfo = pathinfo($fileName);
            $filename = $pathInfo["basename"];
            $filePath = public_path($path . "/" . $filename);

            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
    }
}
