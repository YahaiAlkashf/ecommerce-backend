<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'categories' => $categories
        ], 200);
    }

    public function create(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        }
        $imagePath = $request->file('image')->store('categories', 'public');
        $caterory = Category::create([
            'name' => $request->name,
            'image' => $imagePath
        ]);
        return response()->json([
            'message' => "success create category"
        ], 200);
    }

    public function show($id)
    {
        $category = Category::where('id', $id)->first();
        return response()->json([
            'category' => $category
        ], 200);
    }
    public function update(Request $request,$id)
    {

            $request->validate([
                'name' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif'
            ]);

        $category = Category::where('id', $id)->first();
        if ($request->image) {
            $imagePath = $request->file('image')->store('categories', 'public');
            $category->update([
                'name' => $request->name,
                'image' => $imagePath
            ]);
        }else{
            $category->update([
                'name' => $request->name,
            ]);
        }

        return response()->json([
            'message' => "success update category"
        ], 200);

    }

    public function destroy($id) {
        $category = Category::where('id', $id)->first();
        $category->delete();
        return response()->json([
            'message' => "seccessfuly"
        ], 200);
    }
}
