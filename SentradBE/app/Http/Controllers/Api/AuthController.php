<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            try {
                $role = Role::where('nama_role', 'seniman')->firstOrFail();

                $user = User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                $userRole = UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                ]);

                // Trigger the registered event and send email verification notification
                event(new Registered($user));
                $user->sendEmailVerificationNotification();

                return response()->json([
                    'status' => 'success',
                    'message' => 'User registered successfully. Please check your email for verification.',
                    'data' => $user,
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error during registration: ' . $e->getMessage(),
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Error during registration: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                Log::error('Email atau password salah');
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Email atau password salah'
                ], 401);
            }

            $user = Auth::user();
            $tokenResult = $user->createToken('AuthToken');
            $token = $tokenResult->accessToken;

            $getRole = UserRole::join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->where('user_roles.user_id', $user->id)
                ->select('roles.nama_role')
                ->first();

            Log::info('Login berhasil');
            return response()->json([
                'data' => [
                    'user' => $user,
                    'role' => $getRole,
                    'token' => $token
                ],
                'status' => 'success',
                'message' => 'Login berhasil'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyEmail($id)
    {
        $user = User::findOrFail($id);

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Email verified successfully'], 200);
    }

    public function getRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }
}
