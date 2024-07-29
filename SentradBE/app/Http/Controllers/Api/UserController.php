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
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $users = User::with('roles')
                         ->whereNull('deleted_at')
                         ->paginate($perPage);

            Log::info('Users:', $users->toArray());
            if ($users->count() > 0) {
                Log::info('Data User Berhasil Ditampilkan');
                return response()->json([
                    'data' => $users->items(),
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
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
    public function indexByRole()
    {
        try {
            $role = Role::where('nama_role', 'Penilai')->first();

            if (!$role) {
                Log::info('Role Penilai tidak ditemukan');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role Penilai tidak ditemukan'
                ], 404);
            }

            $users = User::whereHas('roles', function ($query) use ($role) {
                $query->where('role_id', $role->id);
            })->get();

            Log::info('Users with role Penilai:', $users->toArray());
            return response()->json([
                'data' => $users,
                'status' => 'success',
                'message' => 'Data User dengan role Penilai Berhasil Ditampilkan',
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
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data User Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users,username,' . $id,
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|nullable|string|min:8',
                'nama_role' => 'required|string|max:255',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validate->errors()->first(),
                ], 422);
            }

            $user->username = $request->username;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password); // Hash the password
            }

            // Update user role
            $role = Role::where('nama_role', $request->nama_role)->first();
            if ($role) {
                UserRole::where('user_id', $user->id)->delete(); // Remove existing roles
                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                ]);
            }

            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data User berhasil diperbarui',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data user: ' . $e->getMessage(),
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
