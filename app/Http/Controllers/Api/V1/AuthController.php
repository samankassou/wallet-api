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
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => 'Invalid login details',
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



    /*   public function register(Request $request)
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
    } */

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
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

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    private function authenticateFrontend()
    {
        if (!Auth::guard('web')
            ->attempt(
                request()->only('email', 'password'),
                request()->boolean('remember')
            )) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        } /* else {
            $user = User::where('email', request()->email)->first();

            if (!$user || !$user->status) {
                $this->logout(request());
                throw ValidationException::withMessages([
                    'email' => 'The user account is not active',
                ]);
            }
        } */
    }
}
