<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Http\Request;

class AuthController extends Controller
{
     // Register new user
     public function register(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'name' => 'required|string|max:255',
             'email' => 'required|string|email|max:255|unique:users',
             'password' => 'required|string|min:6|confirmed',
         ]);
 
         if ($validator->fails()) {
             return response()->json($validator->errors(), 400);
         }
 
         $user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => Hash::make($request->password),
         ]);
 
         $token = JWTAuth::fromUser($user);
 
         return response()->json([
             'message' => 'User successfully registered',
             'token' => $token
         ], 201);
     }
 
     // Login user and return token
     public function login(Request $request)
     {
         $credentials = $request->only('email', 'password');
 
         if (!$token = JWTAuth::attempt($credentials)) {
             return response()->json(['error' => 'Unauthorized'], 401);
         }
 
         return response()->json([
             'token' => $token
         ],200);
     }
 
     // Log out user (invalidate token)
     public function logout()
     {
         JWTAuth::invalidate(JWTAuth::getToken());
 
         return response()->json([
             'message' => 'User successfully logged out'
         ],200);
     }
}
