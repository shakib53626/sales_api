<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Log;

class AuthRepository
{
    public function login($user)
    {
        try {

            $token =  $user->createToken('auth_token')->plainTextToken;

            $data = [
                'user'  => $user,
                'token' => $token
            ];

            return $data;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function logout($request)
    {
        try {
            return $request->user()->tokens()->delete();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }
}
