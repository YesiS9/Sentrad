<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KaryaController extends Controller
{
    public function index()
    {
        try {
            $karyas = Karya::whereNull('deleted_at')->get();

            if (count($karyas) > 0) {
                Log::info('Data Karya Berhasil Ditampilkan');
                return response()->json([
                    'data' => $karyas,
                    'status' => 'success',
                    'message' => 'Data Karya Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Karya Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Karya Kosong',
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
                'portofolio_id' => 'required|string',
                'judul_karya' => 'required|string',
                'tgl_pembuatan' => 'required|date',
                'deskripsi_karya' => 'required|string',
                'bentuk_karya' => 'required|string',
                'media_karya' => 'required|string',
                'status_karya' => 'required|boolean',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $karya = Karya::create($storeData);

            Log::info('Data Karya Berhasil Ditambahkan');
            return response()->json([
                'data' => $karya,
                'status' => 'success',
                'message' => 'Data Karya Berhasil Ditambahkan',
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
            $karya = Karya::whereNull('deleted_at')->find($id);

            if (!$karya) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Karya tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $karya,
                'status' => 'success',
                'message' => 'Data Karya Berhasil Ditampilkan',
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
            $karya = Karya::whereNull('deleted_at')->find($id);

            if (!$karya) {
                Log::error('Data Karya Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Karya Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'portofolio_id' => 'required|string',
                'judul_karya' => 'required|string',
                'tgl_pembuatan' => 'required|date',
                'deskripsi_karya' => 'required|string',
                'bentuk_karya' => 'required|string',
                'media_karya' => 'required|string',
                'status_karya' => 'required|boolean',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $karya->update($request->all());

            Log::info('Data Karya Berhasil Diupdate');
            return response()->json([
                'data' => $karya,
                'status' => 'success',
                'message' => 'Data Karya Berhasil Diupdate',
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
            $karya = Karya::whereNull('deleted_at')->find($id);

            if (!$karya) {
                Log::error('Data Karya Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Karya Tidak Ditemukan',
                ], 404);
            }

            if ($karya->delete()) {
                Log::info('Data Karya Berhasil Dihapus');
                return response()->json([
                    'data' => $karya,
                    'status' => 'success',
                    'message' => 'Data Karya Berhasil Dihapus',
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
