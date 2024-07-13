<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seniman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SenimanController extends Controller
{
    public function index()
    {
        try {
            $seniman = Seniman::whereNull('deleted_at')->get();

            if (count($seniman) > 0) {
                Log::info('Data Seniman Berhasil Ditampilkan');
                return response()->json([
                    'data' => $seniman,
                    'status' => 'success',
                    'message' => 'Data Seniman Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Seniman Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Seniman Kosong',
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

    public function store(Request $request)
    {
        try {
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'user_id' => 'required|string',
                'nama_seniman' => 'required|string',
                'tgl_lahir' => 'required|date',
                'deskripsi_seniman' => 'required|string',
                'alamat_seniman' => 'required|string',
                'noTelp_seniman' => 'required|string',
                'lama_pengalaman' => 'required|integer',
                'status_seniman' => 'required|boolean',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $seniman = Seniman::create($storeData);

            Log::info('Data Seniman Berhasil Ditambahkan');
            return response()->json([
                'data' => $seniman,
                'status' => 'success',
                'message' => 'Data Seniman Berhasil Ditambahkan',
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

    public function show($id)
    {
        try {
            $seniman = Seniman::whereNull('deleted_at')->find($id);

            if (!$seniman) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Seniman tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $seniman,
                'status' => 'success',
                'message' => 'Data Seniman Berhasil Ditampilkan',
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
            $seniman = Seniman::whereNull('deleted_at')->find($id);

            if (!$seniman) {
                Log::error('Data Seniman Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Seniman Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'user_id' => 'required|string',
                'nama_seniman' => 'required|string',
                'tgl_lahir' => 'required|date',
                'deskripsi_seniman' => 'required|string',
                'alamat_seniman' => 'required|string',
                'noTelp_seniman' => 'required|string',
                'lama_pengalaman' => 'required|integer',
                'status_seniman' => 'required|boolean',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $seniman->update($request->all());

            Log::info('Data Seniman Berhasil Diupdate');
            return response()->json([
                'data' => $seniman,
                'status' => 'success',
                'message' => 'Data Seniman Berhasil Diupdate',
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

    public function destroy($id)
    {
        try {
            $seniman = Seniman::whereNull('deleted_at')->find($id);

            if (!$seniman) {
                Log::error('Data Seniman Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Seniman Tidak Ditemukan',
                ], 404);
            }

            if ($seniman->delete()) {
                Log::info('Data Seniman Berhasil Dihapus');
                return response()->json([
                    'data' => $seniman,
                    'status' => 'success',
                    'message' => 'Data Seniman Berhasil Dihapus',
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
