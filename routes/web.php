<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
        //  return response()->json(['token' => $token]);
                                            
        return redirect(env('FRONTEND_URL') .'/login'. '?token=' . $token);

});
