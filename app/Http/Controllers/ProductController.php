<?php

namespace App\Http\Controllers;

use App\Models\ImageProduct;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $Products = Product::with('ImagesProducts', 'category:id,name')->get();
        return response()->json([
            'products' => $Products
        ], 200);
    }

    public function create(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'rating' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'main_image' => 'image|required|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'image|nullable|mimes:jpeg,png,jpg,gif',
        ]);

        $imagePath = $request->file('main_image')->store('products', 'public');
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'rating' => $request->rating,
            'category_id' => $request->category_id,
            'main_image' => $imagePath,
        ]);
        if ($request->hasFile('images')) {

            $images = $request->file('images');
            foreach ($images as $image) {

                $path = $image->store('products', 'public');

                ImageProduct::create([
                    'product_id' => $product->id,
                    'image' => $path
                ]);
            }
        }
        return response()->json([
            'message' => "Success Create Product"
        ], 200);
    }


    public function show($id)
    {
        $product = Product::with('ImagesProducts')->find($id);
        return response()->json([
            'product' => $product
        ], 200);
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'rating' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'main_image' => 'image|nullable|image|mimes:jpeg,png,jpg,gif',
            'images.*' => 'image|nullable|mimes:jpeg,png,jpg,gif'
        ]);
        $product = Product::findOrFail($id);
        if ($request->hasFile('main_image')) {
            $imagePath = $request->file('main_image')->store('products', 'public');
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'rating' => $request->rating,
                'category_id' => $request->category_id,
                'main_image' => $imagePath,
            ]);
        } else {
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'rating' => $request->rating,
                'category_id' => $request->category_id,
            ]);
        }

        if ($request->hasFile('images')) {
            ImageProduct::where('product_id', $id)->delete();
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $ImageProduct = ImageProduct::where('product_id', $id);
                $ImageProduct->update([
                    'product_id' => $product->id,
                    'image' => $path
                ]);
            }
        }
        return response()->json([
            'message' => "Success Update Product"
        ], 200);
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $images = ImageProduct::where('product_id', $id)->get();
            foreach ($images as $image) {
                Storage::disk('public')->delete($image->image);
                // $image->delete();
            }
            $images = ImageProduct::where('product_id', $id)->delete();
            Storage::disk('public')->delete($product->main_image);
            $product->delete();
            return response()->json([
                'message' => "Product deleted successfully"
            ], 200);
        } catch (\Exception  $e) {
            return response()->json($e);
        }
    }

    public function search(Request $request)
    {
        try{
        $query = $request->query->get('query');

            $products = Product::with('category:id,name')->where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->get();
            return response()->json(['products'=>$products]);
        }catch(\Exception $e){
            return response()->json($e);
        }


    }
}
