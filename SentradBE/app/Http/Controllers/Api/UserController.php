<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole; // Import model UserRole
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with('roles') // Load relasi userRole
                         ->whereNull('deleted_at')
                         ->get();
            Log::info('Users:', $users->toArray());
            if ($users->count() > 0) {
                Log::info('Data User Berhasil Ditampilkan');
                return response()->json([
                    'data' => $users,
                    'status' => 'success',
                    'message' => 'Data User Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data User Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data User Kosong',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function storeByAdmin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'nama_role' => 'required|string|exists:roles,nama_role',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 422);
            }

            $role = Role::where('nama_role', $request->nama_role)->firstOrFail();

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(), // Tanpa verifikasi, langsung dianggap terverifikasi
            ]);

            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);

            Log::info('User added successfully', ['user' => $user]);

            return response()->json([
                'status' => 'success',
                'message' => 'User added successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error adding user: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error adding user: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'nama_role' => 'required|string|exists:roles,nama_role', // Assuming roles table has 'nama_role' column
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 422);
            }

            try {
                $role = Role::where('nama_role', $request->nama_role)->firstOrFail();

                $user = User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                ]);

                Log::info('User added successfully', ['user' => $user]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'User added successfully',
                    'data' => $user,
                ], 201);
            } catch (\Exception $e) {
                Log::error('Error adding user: ' . $e->getMessage());

                return response()->json([
                    'status' => 'error',
                    'message' => 'Error adding user: ' . $e->getMessage(),
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());

            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Error adding user: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $user = User::whereNull('deleted_at')->find($id);
            if (!$user) {
                Log::error('Data User Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data User Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'nama_role' => 'required|string|exists:roles,nama_role',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }
            if ($request->has('username')) {
                $user->username = $request->username;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }


            $user->roles()->sync($request->role_id);
            $user->save();
            Log::info('User updated successfully', ['user' => $user]);

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error updating user: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id){
        try {
            $user = User::whereNull('deleted_at')->find($id);

            if (!$user) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data User tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $user,
                'status' => 'success',
                'message' => 'Data User Berhasil Ditampilkan',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                Log::info('User tidak ditemukan');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $user->delete();

            Log::info('User berhasil dihapus');
            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    
}
