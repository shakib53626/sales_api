<?php

namespace App\Classes;

use App\Http\Controllers\Controller;
use App\Classes\BaseHelper as BH;

class BaseController extends Controller
{
    function sendResponse($result, $message)
    {
        return BH::sendResponse($result, $message);
    }

    function sendError($message){
        return BH::sendError($message);
    }
}
