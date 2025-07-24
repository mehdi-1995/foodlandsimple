<?php

   namespace App\Http\Controllers;

   use Illuminate\Http\Request;
   use App\Models\User;
   use Illuminate\Support\Facades\Hash;
   use Laravel\Sanctum\Sanctum;

   class AuthController extends Controller
   {
       public function login(Request $request)
       {
           $request->validate([
               'phone' => 'required|string',
               'password' => 'required|string',
           ]);

           $user = User::where('phone', $request->phone)->first();

           if (!$user || !Hash::check($request->password, $user->password)) {
               return response()->json(['message' => 'اطلاعات ورود نامعتبر است'], 401);
           }

           $token = $user->createToken('auth_token')->plainTextToken;

           return response()->json(['token' => $token]);
       }
   }