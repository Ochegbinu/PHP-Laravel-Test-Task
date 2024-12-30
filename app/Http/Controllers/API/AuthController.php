<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseAPIController;
use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseAPIController
{


    // public function register(Request $request)
    // {
    //     $rules = [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8|confirmed',
    //     ];

    //     $validator = Validator::make($request->all(), $rules);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     $token = JWTAuth::fromUser($user);

    //     return $this->success_response([
    //         'user' => [
    //             'name' => $user->name,
    //             'email' => $user->email,
    //         ],
    //         'token' => $token,
    //     ]);
    // }



    public function register(Request $request)
{
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $verificationToken = Str::random(64);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'email_verification_token' => $verificationToken,
        'email_verified_at' => null,
    ]);

    Mail::to($user->email)->send(new EmailVerification($verificationToken));

    $token = JWTAuth::fromUser($user);

    return $this->success_response([
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
            // 'verified' => false,
        ],
        'token' => $token,
        'message' => 'Registration successful. Please verify your email.'
    ]);
}
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = JWTAuth::user();

        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ]);
    }
}
