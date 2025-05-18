<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user:id,name,email', 'products:id,name,price'])->get();
        return response()->json([
            'orders' => $orders
        ], 200);
    }

    public function create(Request $request)
    {
            $request->validate([
                'name' => 'required',
                'address' => 'required|string',
                'phone'  => 'required',
                'phone_alt' => 'nullable',
                'payment_method' => 'required|in:on Delivery,Visa,PayPal,Vodafone Cash',
                'product_id' => 'required|exists:products,id'
            ]);
            $user_id = Auth::id();
            $order = Order::create([
                'name' => $request->name,
                'address' => $request->address,
                'phone'  => $request->phone,
                'phone_alt' => $request->phone_alt,
                'payment_method' => $request->payment_method,
                'user_id' => $user_id
            ]);
            $order->products()->attach($request->product_id);
            return response()->json([
                'message' => "success create Order"
            ], 200);

        }
    
}
