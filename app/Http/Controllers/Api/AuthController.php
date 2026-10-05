<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users', 'email')],
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('MyApp Token')->accessToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Sai email hoặc mật khẩu',
            ], 401);   
        }

        $token = $user->createToken('MyApp Token')->accessToken;

        return response()->json([
            'user'        => $user,
            'token_type'  => 'Bearer',
            'access_token'=> $token,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user'       => $request->user(),
            'token_id'   => $request->user()->token()->id,
            'scopes'     => $request->user()->token()->scopes,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Đăng xuất thành công, token đã bị thu hồi',
        ]);
    }
}   