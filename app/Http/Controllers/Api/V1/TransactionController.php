<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::all();
        return response()->json(['transactions' => $transactions]);
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
            $validateTransaction = Validator::make($request->all(), [
                'type'        => 'required',
                'amount'      => 'required',
                'user_id'     => 'required|exists:users,id',
                'category_id' => 'required|exists:categories,id',
            ]);

            if ($validateTransaction->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'validation error',
                    'errors'  => $validateTransaction->errors()
                ], 401);
            }

            $transaction = Transaction::create([
                'type'        => $request->type,
                'amount'      => $request->amount,
                'user_id'     => $request->user_id,
                'category_id' => $request->category_id,
            ]);

            return response()->json([
                'status'   => true,
                'message'  => 'Transaction created successfully!',
                'transaction' => $transaction,
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

            $transaction = Transaction::find($id);

            if (!$transaction) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Transaction not found!',
                ], 404);
            }

            return response()->json([
                'status'      => true,
                'transaction' => $transaction,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
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
            $transaction = Transaction::find($id);

            if (!$transaction) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Transaction not found!',
                ], 404);
            }

            $validateTransaction = Validator::make($request->all(), [
                'type'        => 'required',
                'amount'      => 'required',
                'user_id'     => 'required|exists:users,id',
                'category_id' => 'required|exists:categories,id',
            ]);

            if ($validateTransaction->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'validation error',
                    'errors'  => $validateTransaction->errors()
                ], 401);
            }

            $transaction->update([
                'type'        => $request->type,
                'amount'      => $request->amount,
                'user_id'     => $request->user_id,
                'category_id' => $request->category_id,
            ]);

            return response()->json([
                'status'      => true,
                'message'     => 'Transaction updated successfully!',
                'transaction' => $transaction,
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
            $transaction = Transaction::find($id);

            if (!$transaction) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Transaction not found!',
                ], 404);
            }

            $transaction->delete();

            return response()->json([
                'status'   => true,
                'message'  => 'Transaction deleted successfully!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
