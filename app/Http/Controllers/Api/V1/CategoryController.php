<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['categories' => Category::all()]);
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
            $validateCategory = Validator::make($request->all(), [
                'title'    => 'required|unique:categories',
            ]);

            if ($validateCategory->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'validation error',
                    'errors'  => $validateCategory->errors()
                ], 401);
            }

            $category = Category::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
            ]);

            return response()->json([
                'status'   => true,
                'message'  => 'Category created successfully!',
                'category' => $category,
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

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Category not found!',
                ], 404);
            }

            return response()->json([
                'status'   => true,
                'category' => $category,
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
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Category not found!',
                ], 404);
            }

            $validateCategory = Validator::make($request->all(), [
                'title'    => 'required|unique:categories,title,' . $id,
            ]);

            if ($validateCategory->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'validation error',
                    'errors'  => $validateCategory->errors()
                ], 401);
            }

            $category->update([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
            ]);

            return response()->json([
                'status'   => true,
                'message'  => 'Category updated successfully!',
                'category' => $category,
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
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Category not found!',
                ], 404);
            }

            $category->delete();

            return response()->json([
                'status'   => true,
                'message'  => 'Category deleted successfully!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
