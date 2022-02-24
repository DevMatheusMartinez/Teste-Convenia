<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLogin;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function authenticate(AuthLogin $credentials)
    {
        if (Auth::attempt($credentials->validated())) {
            return response()->json(
                ['success' => Auth::user()->createToken('token')->accessToken],
                200
            );
        }
        return response()->json(['error' => 'Unauthorised'], 401);
    }
}
