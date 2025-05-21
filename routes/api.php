<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

Route::middleware('auth:sanctum')->group( function () {
   Route::get('/user',[AuthController::class,'user']);
   Route::get('/logout',[AuthController::class,'logout']);
});

Route::get('/auth/google/redirect',  function (Request $request){
    return Socialite::driver('google')->redirect();
});
Route::get('/auth/google/callback', function (Request $request){

        $userGoogle = Socialite::driver('google')->stateless()->user();
        $user=User::where('google_id',$userGoogle->id)->first();
        if(!$user){
        $user=User::create([
            'google_id'=>$userGoogle->id,
           'name'=>$userGoogle->name,
            'email'=>$userGoogle->email,
            'password'=>Str::random(12)
            ]
            );
        }
        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken ;
        return redirect(env('FRONTEND_URL') . '?token=' . $token)->cookie(
            'token', $token, 60*24, null, null, false, true // httpOnly=true
        );

});
Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);

Route::get('/categories',[CategoryController::class,'index']);
Route::post('/categories',[CategoryController::class,'create']);
Route::get('/categories/{id}',[CategoryController::class,'show']);
Route::post('/categories/{id}',[CategoryController::class,'update']);
Route::delete('/categories/{id}',[CategoryController::class,'destroy']);

Route::get('/products',[ProductController::class,'index']);
Route::post('/products',[ProductController::class,'create']);
Route::get('/products/{id}',[ProductController::class,'show']);
Route::post('/products/{id}',[ProductController::class,'update']);
Route::delete('/products/{id}',[ProductController::class,'destroy']);

Route::get('/orders',[OrderController::class,'index']);
Route::post('/orders',[OrderController::class,'create'])->middleware('auth:sanctum');


Route::get('/search',[ProductController::class,'search']);



