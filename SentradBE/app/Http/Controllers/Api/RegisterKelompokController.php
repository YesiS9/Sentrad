<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegistrasiKelompok;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterKelompokController extends Controller
{
    public function index(){
        try {
            $register = RegistrasiKelompok::whereNull('deleted_at')->get();

            if (count($register) > 0) {
                Log::info('Data Registrasi Kelompok Berhasil Ditampilkan');
                return response()->json([
                    'data' => $register,
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

    public function store(Request $request){
        try {

            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'seniman_id'=>'required',
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


            $storeData['tgl_terbentuk'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_terbentuk'])->format('Y-m-d');


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


    public function update(Request $request, $id){
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
                'seniman_id'=>'required',
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

            $tgl_terbentuk = Carbon::createFromFormat('d/m/Y', $request->tgl_terbentuk)->format('Y-m-d');

            $register->seniman_id = $request->seniman_id;
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
