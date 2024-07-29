<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegistrasiKelompok;
use App\Models\Seniman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterKelompokController extends Controller
{
    public function index(Request $request){
        try {
            $perPage = $request->input('per_page', 10);
            $register = RegistrasiKelompok::whereNull('deleted_at')->paginate($perPage);

            if (count($register) > 0) {
                Log::info('Data Registrasi Kelompok Berhasil Ditampilkan');
                return response()->json([
                    'data' => $register->items(),
                    'current_page' => $register->currentPage(),
                    'per_page' => $register->perPage(),
                    'total' => $register->total(),
                    'last_page' => $register->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Registrasi Kelompok Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Penilai Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok Kosong',
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

    public function getRegistrasiKelompok()
    {
        try {
            $user = Auth::user();
            if (!$user->seniman) {
                return response()->json([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Seniman not found for the user',
                ], 404);
            }

            $senimanId = $user->seniman->id;

            $kelompok = RegistrasiKelompok::where('seniman_id', $senimanId)
                ->select('nama_kelompok', 'created_at', 'status_kelompok')
                ->paginate(10);

            if ($kelompok->count() > 0) {
                return response()->json([
                    'status' => 'success',
                    'data' => $kelompok->items(),
                    'current_page' => $kelompok->currentPage(),
                    'last_page' => $kelompok->lastPage(),
                    'per_page' => $kelompok->perPage(),
                    'total' => $kelompok->total(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'Data Registrasi Kelompok tidak ditemukan',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'nama_kelompok' => 'required',
                'tgl_terbentuk' => 'required|date_format:d/m/Y',
                'alamat_kelompok' => 'required',
                'deskripsi_kelompok' => 'required',
                'noTelp_kelompok' => 'required|numeric|max:20',
                'email_kelompok' => 'required|email',
                'jumlah_anggota' => 'required|numeric',
                'status_kelompok' => 'required|boolean',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $seniman = Auth::user();

            if (!$seniman) {
                Log::error('Seniman not logged in');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Seniman not logged in',
                ], 401);
            }

            // Converting date to Y-m-d format
            $storeData['tgl_terbentuk'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_terbentuk'])->format('Y-m-d');
            $storeData['seniman_id'] = $seniman->id; // Set the seniman_id to the logged-in user's ID

            // Create the RegistrasiKelompok record
            $register = RegistrasiKelompok::create($storeData);

            Log::info('Data Registrasi Kelompok Berhasil Ditambahkan');
            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok Berhasil Ditambahkan',
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


    public function storebyAdmin(Request $request)
    {
        try {
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'nama_seniman'=>'required|exists:seniman,nama_seniman',
                'nama_kelompok' => 'required',
                'tgl_terbentuk' => 'required|date_format:d/m/Y',
                'alamat_kelompok' => 'required',
                'deskripsi_kelompok' => 'required',
                'noTelp_kelompok' => 'required|numeric',
                'email_kelompok' => 'required|email',
                'jumlah_anggota' => 'required|numeric',
                'status_kelompok' => 'required|boolean',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $seniman = Seniman::where('nama_seniman', $request->nama_seniman)->first();

            if (!$seniman) {
                Log::error('Seniman not found with nama_seniman: ' . $request->nama_seniman);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Seniman not found',
                ], 404);
            }

            $storeData['tgl_terbentuk'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_terbentuk'])->format('Y-m-d');
            $storeData['seniman_id'] = $seniman->id;

            $register = RegistrasiKelompok::create($storeData);

            Log::info('Data Registrasi Kelompok Berhasil Ditambahakan');
            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok Berhasil Ditambahakan',
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




    public function show($id){
        try {
            $register = RegistrasiKelompok::whereNull('deleted_at')->find($id);

            if (!$register) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Kelompok tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok Berhasil Ditampilkan',
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


    public function update(Request $request, $id)
    {
        try {
            $register = RegistrasiKelompok::whereNull('deleted_at')->find($id);

            if (!$register) {
                Log::error('Data Registrasi Kelompok Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Kelompok Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'nama_seniman' => 'required|exists:seniman,nama_seniman',
                'nama_kelompok' => 'required',
                'tgl_terbentuk' => 'required|date_format:d/m/Y',
                'alamat_kelompok' => 'required',
                'deskripsi_kelompok' => 'required',
                'noTelp_kelompok' => 'required|numeric',
                'email_kelompok' => 'required|email',
                'jumlah_anggota' => 'required|numeric',
                'status_kelompok' => 'required|boolean',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            // Find the Seniman by nama_seniman
            $seniman = Seniman::where('nama_seniman', $request->nama_seniman)->first();

            if (!$seniman) {
                Log::error('Seniman not found with nama_seniman: ' . $request->nama_seniman);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Seniman not found',
                ], 404);
            }

            // Convert the date format
            $tgl_terbentuk = Carbon::createFromFormat('d/m/Y', $request->tgl_terbentuk)->format('Y-m-d');

            // Update the RegistrasiKelompok record
            $register->seniman_id = $seniman->id;
            $register->nama_kelompok = $request->nama_kelompok;
            $register->tgl_terbentuk = $tgl_terbentuk;
            $register->alamat_kelompok = $request->alamat_kelompok;
            $register->deskripsi_kelompok = $request->deskripsi_kelompok;
            $register->noTelp_kelompok = $request->noTelp_kelompok;
            $register->email_kelompok = $request->email_kelompok;
            $register->jumlah_anggota = $request->jumlah_anggota;
            $register->status_kelompok = $request->status_kelompok;

            $register->save();

            Log::info('Data Registrasi Kelompok Berhasil Diupdate');
            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok Berhasil Diupdate',
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



    public function destroy($id){
        try {
            $register = RegistrasiKelompok::whereNull('deleted_at')->find($id);

            if (!$register) {
                Log::error('Data Registrasi Kelompok Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Kelompok Tidak Ditemukan',
                ], 404);
            }

            if ($register->delete()) {
                Log::info('Data Registrasi Kelompok Berhasil Dihapus');
                return response()->json([
                    'data' => $register,
                    'status' => 'success',
                    'message' => 'Data Penilai Berhasil Dihapus',
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
