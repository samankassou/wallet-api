<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'validation error',
                    'errors'  => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status'  => false,
                    'message' => 'These credentials do not match our records',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            // if the user is not active
            if (!$user->status) {
                return response()->json([
                    'status'  => false,
                    'message' => 'The user account is not active',
                ], 401);
            }

            return response()->json([
                'status'  => true,
                'message' => 'User logged in successfully !',
                'token'   => $user->createToken("API TOKEN")->plainTextToken,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status'  => true,
            'message' => 'User logged out successfully !',
        ], 200);
    }



    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname'  => 'required',
                'email'     => 'required|email|unique:users',
                'password'  => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'validation error',
                    'errors'  => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'firstname'      => $request->firstname,
                'lastname'       => $request->lastname,
                'email'          => $request->email,
                'password'       => bcrypt($request->password),
                'remember_token' => Str::random(10),
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'User created successfully!',
                'token'   => $user->createToken("API TOKEN")->plainTextToken,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
