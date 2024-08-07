<?php

namespace App\Http\Controllers\Admin;

use App\Classes\BaseController;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseController
{
    protected $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    public function login(LoginRequest $request){
        try {

            $user = User::where('email', $request->email)->first();

            if(!$user){
                return $this->sendError("User Not Found", 404);
            }

            if($user->status == 'inactive'){
                return $this->sendError("User Account Deactivate", 404);
            }

            if(Hash::check($request->password, $user->password)){

                $data = $this->repository->login($user);
                return $this->sendResponse($data, "Login successfully");

            }else{
                return $this->sendError("User credential doesn't match",);
            }

        } catch (\Exception $exception) {

            Log::error($exception->getMessage());
            return $this->sendError(__("common.commonError"));
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->repository->logout($request);

            return $this->sendResponse(null, "User logout successfully");
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
