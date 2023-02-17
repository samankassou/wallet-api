<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'), $request->remember)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid login details',
            ]);
        }

        // the user account is disabled
        if (!auth()->user()->status) {
            throw ValidationException::withMessages([
                'email' => 'The user account is disabled',
            ]);
        }

        $token = auth()->user()->createToken('user-token');

        return response()->json([
            'token' => $token->plainTextToken,
            'message' => ['successfully logged in'],
        ]);
    }

    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();
        return [
            'message' => 'Successfully Logged out'
        ];
    }

    public function register(Request $request)
    {
        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed'],
        ]);

        $user = User::create([
            'firstname'      => $request->firstname,
            'lastname'       => $request->lastname,
            'email'          => $request->email,
            'password'       => bcrypt($request->password),
            'remember_token' => Str::random(10),
        ]);

        $token = $user->createToken('user-token');

        return response()->json([
            'token' => $token->plainTextToken,
            'message' => ['successfully logged in'],
        ]);
    }
}
