<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|string|email|max:255',
                'password' => 'required',
            ],
            [
                'email.required' => 'Email harus diisi',
                'password.required' => 'Password harus diisi',
                'email.email' => 'Email tidak valid',
                'email.max' => 'Email maksimal 255 karakter',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // check email and password
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Kombinasi email dan password salah',
            ], 401);
        }
        // mengambil email
        $user = User::where('email', $request['email'])
            ->firstOrFail();
        // membuat token
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'status' => true,
            'role' => $user->role,
            'token' => $token,
        ]);
    }

    function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|min:6|same:password',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
            'role' => 'user',
        ]);
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'status' => true,
            'role' => $user->role,
            'token' => $token,
        ]);
    }

    function cekToken(Request $request)
    {
        $user = $request->user()->load('userInfo.shippingCost.subDistrict');
        return response()->json([
            'status' => true,
            'role' => $user->role,
            'user' => $user
        ]);
    }

    function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'status' => true,
            'message' => 'Logout Berhasil',
        ]);
    }
}
