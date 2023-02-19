<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('transactions')->get();
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname'  => 'required',
                'email'     => 'required|email|unique:users',
                'password'  => 'required|min:6',
                'role'      => 'required',
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
                'role'           => $request->role,
                'password'       => bcrypt($request->password),
                'remember_token' => Str::random(10),
            ]);

            return response()->json([
                'status'   => true,
                'message'  => 'User created successfully!',
                'user' => $user,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'User not found!',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'user'   => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, User $user)
    {
        $user->status = !$user->status;
        $user->save();
        return response()->json([
            'status' => true,
            'user'   => $user,
            'message' => 'User updated'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'User not found!',
                ], 404);
            }

            $validateUser = Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname'  => 'required',
                'email'     => 'required|email|unique:users,email,' . $id,
                'role'      => 'required',
                'status'    => 'required',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'validation error',
                    'errors'  => $validateUser->errors()
                ], 401);
            }

            $user->update([
                'firstname' => $request->firstname,
                'lastname'  => $request->lastname,
                'email'     => $request->email,
                'role'      => $request->role,
                'status'    => $request->status,
                //'password'  => bcrypt($request->password),
            ]);

            return response()->json([
                'status'   => true,
                'message'  => 'User updated successfully!',
                'user' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'User not found!',
                ], 404);
            }

            $user->delete();

            return response()->json([
                'status'   => true,
                'message'  => 'User deleted successfully!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
